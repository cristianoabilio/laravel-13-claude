<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            'Urology' => 'specialities-01.png',
            'Neurology' => 'specialities-02.png',
            'Orthopedic' => 'specialities-03.png',
            'Cardiologist' => 'specialities-04.png',
            'Dentist' => 'specialities-05.png',
        ];

        foreach ($specialities as $name => $image) {
            Speciality::firstOrCreate(
                ['name' => $name],
                ['image' => "admin/assets/img/specialities/{$image}"]
            );
        }
    }
}
