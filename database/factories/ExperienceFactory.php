<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-10 years', '-2 years');

        return [
            'doctor_id' => User::factory()->doctor(),
            'hospital_logo' => null,
            'title' => fake()->jobTitle(),
            'hospital' => fake()->company().' Hospital',
            'years_of_experience' => (string) fake()->numberBetween(1, 15),
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'job_description' => fake()->paragraph(),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '-1 years'),
            'currently_working' => false,
        ];
    }

    /**
     * Indicate that the doctor is still working at this experience.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => null,
            'currently_working' => true,
        ]);
    }
}
