<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
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
            'patient_id' => fn (array $attributes) => Appointment::find($attributes['appointment_id'])?->patient_id,
            'doctor_id' => fn (array $attributes) => Appointment::find($attributes['appointment_id'])?->doctor_id,
            'amount' => fake()->randomFloat(2, 20, 300),
            'payment_method' => 'credit_card',
            'transaction_id' => 'TXN-'.strtoupper(fake()->bothify('########')),
            'payment_status' => PaymentStatus::Paid,
            'card_holder_name' => fake()->name(),
            'card_last_four' => fake()->numerify('####'),
            'paid_at' => now(),
        ];
    }
}
