<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('now', '+30 days');
        $start = fake()->randomElement(['09:00:00', '09:30:00', '10:00:00', '14:00:00', '15:30:00']);

        return [
            'appointment_number' => 'APT-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'patient_id' => User::factory()->patient(),
            'doctor_id' => User::factory()->doctor(),
            'clinic_id' => null,
            'doctor_service_id' => null,
            'service_name' => fake()->words(2, true),
            'duration_minutes' => 30,
            'appointment_type' => AppointmentType::Clinic,
            'appointment_date' => $date->format('Y-m-d'),
            'start_time' => $start,
            'end_time' => date('H:i:s', strtotime($start.' +30 minutes')),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'symptoms' => fake()->optional()->sentence(),
            'reason_for_visit' => fake()->optional()->paragraph(),
            'consultation_fee' => fake()->randomFloat(2, 20, 300),
            'booking_fee' => 0,
            'tax' => 0,
            'discount' => 0,
            'total_amount' => fake()->randomFloat(2, 20, 300),
            'status' => AppointmentStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
        ];
    }
}
