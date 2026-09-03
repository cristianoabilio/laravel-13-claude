<?php

namespace App\Services\Doctor;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $doctor, User $patient, array $data): Prescription
    {
        $prescription = Prescription::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'prescription_number' => 'PENDING',
            'other_information' => $data['other_information'] ?? null,
            'follow_up' => $data['follow_up'] ?? null,
            'issued_at' => now(),
        ]);

        $prescription->update([
            'prescription_number' => sprintf('PR-%s-%06d', now()->year, $prescription->id),
        ]);

        foreach ($data['items'] as $item) {
            $prescription->items()->create([
                'medicine_name' => $item['medicine_name'],
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
                'duration' => $item['duration'] ?? null,
                'timings' => $item['timings'] ?? null,
            ]);
        }

        return $prescription->load('items');
    }
}
