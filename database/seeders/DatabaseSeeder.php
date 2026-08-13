<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\BusinessHour;
use App\Models\Clinic;
use App\Models\ClinicImage;
use App\Models\DoctorService;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Membership;
use App\Models\Service;
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

        $clinic = Clinic::factory()->create(['doctor_id' => $doctor->id]);
        ClinicImage::factory()->count(2)->create(['clinic_id' => $clinic->id]);

        foreach (DayOfWeek::cases() as $day) {
            BusinessHour::factory()->create([
                'doctor_id' => $doctor->id,
                'day' => $day,
                'is_open' => ! $day->isWeekend(),
                'from_time' => $day->isWeekend() ? null : '09:00:00',
                'to_time' => $day->isWeekend() ? null : '18:00:00',
            ]);
        }

        $this->call([
            AdminSeeder::class,
            SpecialitySeeder::class,
            ServiceSeeder::class,
        ]);

        Service::whereHas('speciality', fn ($query) => $query->where('name', 'Dentist'))
            ->get()
            ->each(fn (Service $service) => DoctorService::factory()->create([
                'doctor_id' => $doctor->id,
                'service_id' => $service->id,
            ]));
    }
}
