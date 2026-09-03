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
        return $this->updateStatus($appointment, AppointmentStatus::Pending, AppointmentStatus::Confirmed, 'Appointment accepted.', notify: true);
    }

    public function reject(Appointment $appointment): JsonResponse
    {
        return $this->updateStatus($appointment, AppointmentStatus::Pending, AppointmentStatus::Cancelled, 'Appointment rejected.', notify: true);
    }

    public function complete(Appointment $appointment): JsonResponse
    {
        return $this->updateStatus($appointment, AppointmentStatus::Confirmed, AppointmentStatus::Completed, 'Appointment marked as completed.', notify: false);
    }

    protected function updateStatus(Appointment $appointment, AppointmentStatus $requiredStatus, AppointmentStatus $newStatus, string $message, bool $notify): JsonResponse
    {
        abort_if($appointment->doctor_id !== Auth::id(), 403);
        abort_if($appointment->status !== $requiredStatus, 409, 'This appointment can no longer be updated.');

        $appointment->update(['status' => $newStatus]);

        if ($notify) {
            Mail::to($appointment->email)->queue(new AppointmentStatusMail($appointment));
        }

        return response()->json(['message' => $message, 'status' => $newStatus->value]);
    }
}
