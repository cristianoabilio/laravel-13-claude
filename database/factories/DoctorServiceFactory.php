<?php

namespace Database\Factories;

use App\Models\DoctorService;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorService>
 */
class DoctorServiceFactory extends Factory
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
            'service_id' => Service::factory(),
            'price' => fake()->randomFloat(2, 20, 500),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
