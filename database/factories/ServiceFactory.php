<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'speciality_id' => Speciality::factory(),
            'name' => fake()->unique()->words(2, true),
        ];
    }
}
