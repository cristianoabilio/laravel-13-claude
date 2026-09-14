<?php

namespace Database\Factories;

use App\Models\HomeReasonSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeReasonSection>
 */
class HomeReasonSectionFactory extends Factory
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
            'heading' => 'Compelling Reasons to Choose',
        ];
    }
}
