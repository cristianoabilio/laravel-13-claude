<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
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
            'logo' => null,
            'name' => fake()->company().' Clinic',
            'location' => fake()->city(),
            'address' => fake()->address(),
        ];
    }
}
