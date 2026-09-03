<?php

namespace Database\Factories;

use App\Models\DoctorBankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorBankAccount>
 */
class DoctorBankAccountFactory extends Factory
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
            'bank_name' => fake()->company().' Bank',
            'branch_name' => fake()->city(),
            'account_number' => fake()->numerify('################'),
            'account_holder_name' => fake()->name(),
        ];
    }
}
