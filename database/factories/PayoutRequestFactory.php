<?php

namespace Database\Factories;

use App\Enums\PayoutRequestStatus;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayoutRequest>
 */
class PayoutRequestFactory extends Factory
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
            'amount' => fake()->randomFloat(2, 20, 500),
            'description' => fake()->optional()->sentence(),
            'status' => PayoutRequestStatus::Pending,
            'bank_name' => fake()->company().' Bank',
            'branch_name' => fake()->city(),
            'account_number' => fake()->numerify('################'),
            'account_holder_name' => fake()->name(),
            'processed_at' => null,
            'processed_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => PayoutRequestStatus::Approved,
            'processed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => PayoutRequestStatus::Cancelled,
            'processed_at' => now(),
        ]);
    }
}
