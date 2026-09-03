<?php

namespace App\Services\Doctor;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Collection;

class DoctorPatientService
{
    /**
     * Every patient this doctor has completed at least one appointment with,
     * each annotated with their most recent booking date with this doctor.
     *
     * @return Collection<int, User>
     */
    public function forDoctor(User $doctor): Collection
    {
        $patientIds = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', AppointmentStatus::Completed)
            ->distinct()
            ->pluck('patient_id');

        $lastBookings = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereIn('patient_id', $patientIds)
            ->selectRaw('patient_id, MAX(appointment_date) as last_booking')
            ->groupBy('patient_id')
            ->pluck('last_booking', 'patient_id');

        return User::query()
            ->whereIn('id', $patientIds)
            ->orderBy('first_name')
            ->get()
            ->map(function (User $patient) use ($lastBookings) {
                $patient->last_booking_date = $lastBookings->get($patient->id);

                return $patient;
            });
    }

    /**
     * Whether this doctor has completed at least one appointment with this
     * patient - the gate for viewing the patient's shared chart.
     */
    public function isTreatingPatient(User $doctor, User $patient): bool
    {
        return Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('status', AppointmentStatus::Completed)
            ->exists();
    }
}
