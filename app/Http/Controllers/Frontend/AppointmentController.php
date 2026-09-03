<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorService;
use App\Models\User;
use App\Services\Booking\AppointmentBookingService;
use App\Services\Booking\InvoicePdfService;
use App\Services\Booking\SlotGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppointmentController extends Controller
{
    public function __construct(
        protected SlotGeneratorService $slotGenerator,
        protected AppointmentBookingService $booking,
        protected InvoicePdfService $invoicePdf,
    ) {}

    /**
     * AJAX endpoint: the time slots available for this doctor on a given date
     * for a given service's duration. Called whenever the patient changes the
     * date (or the selected service changes its duration).
     */
    public function loadSlots(Request $request, int $doctorId): JsonResponse
    {
        $doctor = User::where('role', 'doctor')->findOrFail($doctorId);

        $validated = $request->validate([
            'doctor_service_ids' => ['required', 'array', 'min:1'],
            'doctor_service_ids.*' => ['integer'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $totalDuration = DoctorService::where('doctor_id', $doctor->id)
            ->whereIn('id', $validated['doctor_service_ids'])
            ->sum('duration_minutes');

        if ($totalDuration <= 0) {
            return response()->json(['message' => 'Invalid services selected.'], 422);
        }

        $slots = $this->slotGenerator
            ->forDate($doctor, Carbon::parse($validated['date']), (int) $totalDuration)
            ->map(fn (array $slot) => [
                'time' => $slot['start']->format('H:i'),
                'label' => $slot['label'],
                'available' => $slot['available'],
            ])
            ->values();

        return response()->json(['slots' => $slots]);
    }

    /**
     * Submit the fully-completed booking wizard. Everything is validated and
     * persisted in one transaction; the mock payment and invoice are created
     * as part of that same flow.
     */
    public function store(StoreAppointmentRequest $request, int $doctorId): RedirectResponse
    {
        $doctor = User::where('role', 'doctor')->findOrFail($doctorId);
        $doctorServices = DoctorService::with('service')
            ->where('doctor_id', $doctor->id)
            ->whereIn('id', $request->validated('doctor_service_ids'))
            ->get();

        $data = $request->validated();
        $data['appointment_date'] = Carbon::parse($data['appointment_date']);

        try {
            $appointment = $this->booking->book(
                Auth::user(),
                $doctor,
                $doctorServices,
                $data,
                $request->file('documents', []),
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['start_time' => $e->getMessage()]);
        }

        $this->invoicePdf->generate($appointment->invoice);

        return redirect()->route('appointments.confirmation', $appointment);
    }

    public function confirmation(Appointment $appointment): View
    {
        abort_unless($appointment->patient_id === Auth::id(), 403);

        return view('frontend.booking_confirmation', [
            'appointment' => $appointment->load(['doctor', 'clinic', 'services', 'payment', 'invoice']),
        ]);
    }

    public function downloadInvoice(Appointment $appointment): StreamedResponse
    {
        abort_unless(in_array(Auth::id(), [$appointment->patient_id, $appointment->doctor_id], true), 403);

        $invoice = $appointment->invoice()->firstOrFail();

        if (! $invoice->pdf_path || ! Storage::disk('s3')->exists($invoice->pdf_path)) {
            $this->invoicePdf->generate($invoice);
        }

        return Storage::disk('s3')->download($invoice->pdf_path, $invoice->invoice_number.'.pdf');
    }
}
