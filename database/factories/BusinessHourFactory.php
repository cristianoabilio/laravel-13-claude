<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\BusinessHour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessHour>
 */
class BusinessHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $day = fake()->randomElement(DayOfWeek::cases());
        $isOpen = ! $day->isWeekend();

        return [
            'doctor_id' => User::factory()->doctor(),
            'day' => $day,
            'is_open' => $isOpen,
            'from_time' => $isOpen ? '09:00:00' : null,
            'to_time' => $isOpen ? '18:00:00' : null,
        ];
    }

    /**
     * Indicate that the doctor is closed on this day.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open' => false,
            'from_time' => null,
            'to_time' => null,
        ]);
    }
}
