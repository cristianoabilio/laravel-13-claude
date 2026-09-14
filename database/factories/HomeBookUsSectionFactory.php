<?php

namespace Database\Factories;

use App\Models\HomeBookUsSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeBookUsSection>
 */
class HomeBookUsSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'badge_text' => 'Why Book With Us',
            'heading_prefix' => 'We are committed to understanding your',
            'heading_highlight' => 'unique needs and delivering care.',
            'description' => fake()->paragraph(),
            'image_one' => null,
            'image_two' => null,
            'image_three' => null,
        ];
    }
}
