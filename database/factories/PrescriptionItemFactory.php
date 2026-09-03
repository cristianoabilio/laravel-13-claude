<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'medicine_name' => fake()->word().' '.fake()->numberBetween(5, 500).'MG',
            'dosage' => fake()->numberBetween(1, 100).' mg',
            'frequency' => '1-0-0-1',
            'duration' => '1 month',
            'timings' => fake()->randomElement(['Before Meal', 'After Meal']),
        ];
    }
}
