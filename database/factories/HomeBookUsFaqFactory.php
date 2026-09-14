<?php

namespace Database\Factories;

use App\Models\HomeBookUsFaq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeBookUsFaq>
 */
class HomeBookUsFaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
