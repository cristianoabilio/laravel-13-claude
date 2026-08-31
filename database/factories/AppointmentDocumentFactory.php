<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\AppointmentDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentDocument>
 */
class AppointmentDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'file_path' => 'appointment-documents/'.fake()->uuid().'.pdf',
            'file_type' => 'application/pdf',
            'original_name' => fake()->word().'.pdf',
        ];
    }
}
