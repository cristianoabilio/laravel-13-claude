<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\AppointmentService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentService>
 */
class AppointmentServiceFactory extends Factory
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
            'service_name' => fake()->words(2, true),
            'price' => fake()->randomFloat(2, 20, 300),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60]),
        ];
    }
}
