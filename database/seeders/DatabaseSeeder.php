<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->patient()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        $doctor = User::factory()->doctor()->create([
            'first_name' => 'Test',
            'last_name' => 'Doctor',
            'email' => 'doctor@example.com',
        ]);

        Membership::factory()->count(2)->create(['doctor_id' => $doctor->id]);
        Experience::factory()->count(2)->create(['doctor_id' => $doctor->id]);
        Experience::factory()->current()->create(['doctor_id' => $doctor->id]);
        Education::factory()->count(2)->create(['doctor_id' => $doctor->id]);

        $this->call([
            AdminSeeder::class,
            SpecialitySeeder::class,
        ]);
    }
}
