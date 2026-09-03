<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => User::factory()->doctor(),
            'patient_id' => User::factory()->patient(),
            'appointment_id' => null,
            'prescription_number' => 'PR-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'other_information' => fake()->sentence(),
            'follow_up' => fake()->sentence(),
            'issued_at' => now(),
        ];
    }
}
