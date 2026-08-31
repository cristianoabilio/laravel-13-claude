<?php

namespace App\Services\Booking;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AppointmentDocument;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AppointmentBookingService
{
    public function __construct(
        protected SlotGeneratorService $slots,
    ) {}

    /**
     * Book an appointment end-to-end: validate the slot is still free, create the
     * appointment (with one line item per selected service), "process" the mock
     * card payment, generate the invoice, and store any uploaded documents - all
     * inside one DB transaction so a failure anywhere rolls everything back.
     *
     * @param  Collection<int, \App\Models\DoctorService>  $doctorServices  every service the patient selected, must all belong to $doctor
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $documents
     *
     * @throws RuntimeException when the requested slot is no longer available
     */
    public function book(User $patient, User $doctor, Collection $doctorServices, array $data, array $documents = []): Appointment
    {
        return DB::transaction(function () use ($patient, $doctor, $doctorServices, $data, $documents) {
            // Lock every one of this doctor's appointments for the date so a
            // concurrent booking request can't slip in between our availability
            // check and the insert below (the classic double-booking race).
            Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $data['appointment_date'])
                ->lockForUpdate()
                ->get();

            $totalDuration = (int) $doctorServices->sum('duration_minutes');
            $start = $data['appointment_date']->copy()->setTimeFromTimeString($data['start_time']);
            $end = $start->copy()->addMinutes($totalDuration);

            $stillFree = $this->slots
                ->forDate($doctor, $data['appointment_date'], $totalDuration)
                ->first(fn (array $slot) => $slot['start']->equalTo($start));

            if (! $stillFree || ! $stillFree['available']) {
                throw new RuntimeException('That time slot is no longer available. Please choose another.');
            }

            $appointmentType = AppointmentType::from($data['appointment_type']);
            $consultationFee = (float) $doctorServices->sum(fn ($service) => (float) $service->price);
            $bookingFee = 0.0;
            $tax = 0.0;
            $discount = 0.0;
            $total = round($consultationFee + $bookingFee + $tax - $discount, 2);
            $primaryService = $doctorServices->first();

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'clinic_id' => $appointmentType->requiresClinic() ? $data['clinic_id'] : null,
                'doctor_service_id' => $primaryService->id,
                'service_name' => $doctorServices->pluck('service.name')->implode(', '),
                'duration_minutes' => $totalDuration,
                'appointment_type' => $appointmentType,
                'appointment_date' => $data['appointment_date']->toDateString(),
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'home_visit_address' => $appointmentType->requiresHomeAddress() ? $data['home_visit_address'] : null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'symptoms' => $data['symptoms'] ?? null,
                'reason_for_visit' => $data['reason_for_visit'] ?? null,
                'consultation_fee' => $consultationFee,
                'booking_fee' => $bookingFee,
                'tax' => $tax,
                'discount' => $discount,
                'total_amount' => $total,
                'status' => AppointmentStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
            ]);

            // The formatted number depends on the row's own auto-increment id,
            // so it can only be set after the insert - this sidesteps any
            // manual counter that could collide under concurrent bookings.
            $appointment->forceFill([
                'appointment_number' => sprintf('APT-%s-%06d', now()->year, $appointment->id),
            ])->save();

            $this->recordServiceLines($appointment, $doctorServices);

            $payment = $this->recordPayment($appointment, $data);

            $appointment->forceFill(['payment_status' => $payment->payment_status])->save();

            $this->generateInvoice($appointment);

            $this->storeDocuments($appointment, $documents);

            return $appointment->fresh([
                'patient', 'doctor', 'clinic', 'doctorService.service', 'services', 'payment', 'invoice', 'documents',
            ]);
        });
    }

    /**
     * @param  Collection<int, \App\Models\DoctorService>  $doctorServices
     */
    protected function recordServiceLines(Appointment $appointment, Collection $doctorServices): void
    {
        foreach ($doctorServices as $doctorService) {
            $appointment->services()->create([
                'doctor_service_id' => $doctorService->id,
                'service_name' => $doctorService->service->name,
                'price' => $doctorService->price,
                'duration_minutes' => $doctorService->duration_minutes,
            ]);
        }
    }

    /**
     * "Process" the mock credit card payment. No real gateway is involved -
     * the card number and CVV are validated by the form request but never
     * reach this method, let alone get persisted.
     *
     * @param  array<string, mixed>  $data
     */
    protected function recordPayment(Appointment $appointment, array $data): Payment
    {
        return Payment::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'amount' => $appointment->total_amount,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN-'.Str::upper(Str::random(10)),
            'payment_status' => PaymentStatus::Paid,
            'card_holder_name' => $data['card_holder_name'],
            'card_last_four' => substr(preg_replace('/\D/', '', $data['card_number']), -4),
            'paid_at' => now(),
        ]);
    }

    protected function generateInvoice(Appointment $appointment): Invoice
    {
        $invoice = Invoice::create([
            'appointment_id' => $appointment->id,
            'invoice_number' => 'PENDING',
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'subtotal' => $appointment->consultation_fee + $appointment->booking_fee,
            'tax' => $appointment->tax,
            'discount' => $appointment->discount,
            'total' => $appointment->total_amount,
            'status' => $appointment->payment_status === PaymentStatus::Paid ? 'paid' : 'unpaid',
            'generated_at' => now(),
        ]);

        $invoice->forceFill([
            'invoice_number' => sprintf('INV-%s-%06d', now()->year, $invoice->id),
        ])->save();

        return $invoice;
    }

    /**
     * @param  array<int, UploadedFile>  $documents
     */
    protected function storeDocuments(Appointment $appointment, array $documents): void
    {
        foreach ($documents as $document) {
            if (! $document) {
                continue;
            }

            $path = $document->store('appointment-documents', 's3');

            AppointmentDocument::create([
                'appointment_id' => $appointment->id,
                'file_path' => $path,
                'file_type' => $document->getClientMimeType(),
                'original_name' => $document->getClientOriginalName(),
            ]);
        }
    }
}
