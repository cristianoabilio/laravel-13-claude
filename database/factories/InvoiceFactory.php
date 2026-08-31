<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 300);

        return [
            'appointment_id' => Appointment::factory(),
            'invoice_number' => 'INV-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'patient_id' => fn (array $attributes) => Appointment::find($attributes['appointment_id'])?->patient_id,
            'doctor_id' => fn (array $attributes) => Appointment::find($attributes['appointment_id'])?->doctor_id,
            'subtotal' => $subtotal,
            'tax' => 0,
            'discount' => 0,
            'total' => $subtotal,
            'status' => 'paid',
            'generated_at' => now(),
        ];
    }
}
