<?php

namespace App\Services\Frontend;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    /**
     * Whether this patient has ever completed an appointment with this
     * doctor - the gate for being allowed to leave a review at all.
     */
    public function hasCompletedAppointment(User $patient, User $doctor): bool
    {
        return Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', AppointmentStatus::Completed)
            ->exists();
    }

    public function hasReviewed(User $patient, User $doctor): bool
    {
        return Review::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $patient, User $doctor, array $data): Review
    {
        if (! $this->hasCompletedAppointment($patient, $doctor)) {
            throw ValidationException::withMessages([
                'rating' => "You didn't meet with this doctor.",
            ]);
        }

        if ($this->hasReviewed($patient, $doctor)) {
            throw ValidationException::withMessages([
                'rating' => 'You have already reviewed this doctor.',
            ]);
        }

        return Review::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);
    }
}
