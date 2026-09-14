<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
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
            'quote' => fake()->paragraph(),
            'patient_name' => fake()->name(),
            'patient_country' => fake()->country(),
            'image' => null,
            'rating' => 5,
            'sort_order' => 0,
        ];
    }
}
