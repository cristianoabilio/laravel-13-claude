<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->patient(),
            'title' => fake()->words(3, true),
            'record_for' => fake()->name(),
            'record_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'comments' => fake()->sentence(),
            'file_path' => 'medical-records/'.fake()->uuid().'.pdf',
            'file_type' => 'application/pdf',
            'original_name' => fake()->word().'.pdf',
        ];
    }
}
