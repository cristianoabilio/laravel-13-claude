<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Speciality;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'Urology' => ['Urology Consultation', 'Kidney Stone Treatment', 'Prostate Exam'],
            'Neurology' => ['Neurological Exam', 'EEG', 'Stroke Management'],
            'Orthopedic' => ['Orthopedic Consultation', 'Joint Replacement', 'Fracture Treatment'],
            'Cardiologist' => ['Cardiac Consultation', 'ECG', 'Angioplasty'],
            'Dentist' => ['General Checkup', 'Tooth Bleaching', 'Root Canal'],
        ];

        foreach ($services as $specialityName => $names) {
            $speciality = Speciality::where('name', $specialityName)->first();

            if (! $speciality) {
                continue;
            }

            foreach ($names as $name) {
                Service::firstOrCreate([
                    'speciality_id' => $speciality->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
