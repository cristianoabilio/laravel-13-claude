<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-15 years', '-6 years');
        $endDate = fake()->dateTimeBetween($startDate, '-2 years');

        return [
            'doctor_id' => User::factory()->doctor(),
            'logo' => null,
            'institution' => fake()->company().' University',
            'course' => fake()->randomElement(['MBBS', 'MD', 'MS', 'DNB', 'BDS', 'MDS']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'no_of_years' => (string) max(1, $startDate->diff($endDate)->y),
            'description' => fake()->paragraph(),
        ];
    }
}
