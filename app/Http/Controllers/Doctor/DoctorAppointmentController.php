<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Mail\AppointmentStatusMail;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DoctorAppointmentController extends Controller
{
    public function requests(): View
    {
        $appointments = Appointment::query()
            ->where('doctor_id', Auth::id())
            ->where('status', AppointmentStatus::Pending)
            ->with('patient')
            ->orderByDesc('created_at')
            ->get();

        return view('doctor.dashboard.requests.requests', ['appointments' => $appointments]);
    }

    public function accept(Appointment $appointment): JsonResponse
    {
        return $this->updateStatus($appointment, AppointmentStatus::Confirmed, 'Appointment accepted.');
    }

    public function reject(Appointment $appointment): JsonResponse
    {
        return $this->updateStatus($appointment, AppointmentStatus::Cancelled, 'Appointment rejected.');
    }

    protected function updateStatus(Appointment $appointment, AppointmentStatus $status, string $message): JsonResponse
    {
        abort_if($appointment->doctor_id !== Auth::id(), 403);
        abort_if($appointment->status !== AppointmentStatus::Pending, 409, 'This request has already been handled.');

        $appointment->update(['status' => $status]);

        Mail::to($appointment->email)->queue(new AppointmentStatusMail($appointment));

        return response()->json(['message' => $message, 'status' => $status->value]);
    }
}
