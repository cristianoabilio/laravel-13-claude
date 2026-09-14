<?php

namespace Database\Factories;

use App\Models\HomeService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeService>
 */
class HomeServiceFactory extends Factory
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
            'url' => null,
            'sort_order' => 0,
        ];
    }
}
