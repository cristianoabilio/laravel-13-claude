<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\ClinicImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClinicImage>
 */
class ClinicImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'image' => 'clinics/gallery/'.fake()->uuid().'.jpg',
        ];
    }
}
