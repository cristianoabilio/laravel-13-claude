<?php

namespace App\Services\Patient;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Collection;

class PatientDoctorService
{
    /**
     * Every doctor this patient has completed at least one appointment with,
     * each annotated with their most recent booking date with this patient.
     *
     * @return Collection<int, User>
     */
    public function forPatient(User $patient): Collection
    {
        $doctorIds = Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('status', AppointmentStatus::Completed)
            ->distinct()
            ->pluck('doctor_id');

        $lastBookings = Appointment::query()
            ->where('patient_id', $patient->id)
            ->whereIn('doctor_id', $doctorIds)
            ->selectRaw('doctor_id, MAX(appointment_date) as last_booking')
            ->groupBy('doctor_id')
            ->pluck('last_booking', 'doctor_id');

        return User::query()
            ->whereIn('id', $doctorIds)
            ->orderBy('first_name')
            ->get()
            ->map(function (User $doctor) use ($lastBookings) {
                $doctor->last_booking_date = $lastBookings->get($doctor->id);

                return $doctor;
            });
    }

    /**
     * Whether this patient has completed at least one appointment with this
     * doctor - the gate for chatting with them.
     */
    public function isTreatedByDoctor(User $patient, User $doctor): bool
    {
        return Appointment::query()
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', AppointmentStatus::Completed)
            ->exists();
    }
}
