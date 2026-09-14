<?php

namespace Database\Factories;

use App\Models\HomeReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeReason>
 */
class HomeReasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => 'isax isax-tag-user5',
            'icon_color' => 'text-orange',
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
