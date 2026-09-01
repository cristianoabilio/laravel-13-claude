<?php

use App\Models\Clinic;
use App\Models\DoctorService;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Favorite;
use App\Models\Membership;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

test('a patient id returns a 404 instead of a doctor profile', function () {
    $patient = User::factory()->patient()->create();

    $this->get(route('doctor.details', $patient->id))->assertNotFound();
});

test('an unknown doctor id returns a 404', function () {
    $this->get(route('doctor.details', 999999))->assertNotFound();
});

test('a brand new doctor with no related data renders gracefully', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Fresh Start',
        'designation' => null,
        'known_languages' => [],
        'availability_status' => 'not_available',
    ]);

    $response = $this->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee('Dr Fresh Start');
    $response->assertSee('Not Available');
    $response->assertSee('No practice experience added yet.');
    $response->assertSee('No specialities added yet.');
    $response->assertSee('No services added yet.');
    $response->assertSee('No clinics added yet.');
    $response->assertSee('No memberships added yet.');
    $response->assertSee('No reviews yet.');
});

test('a fully populated doctor shows real data from every related table', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Edalin Hendry',
        'designation' => 'Cardiologist',
        'known_languages' => ['English', 'French'],
        'availability_status' => 'available',
        'email_verified_at' => now(),
    ]);

    Experience::factory()->create([
        'doctor_id' => $doctor->id,
        'hospital' => 'Cambridge University Hospital',
        'title' => 'Senior Cardiologist',
        'currently_working' => true,
        'start_date' => now()->subYears(3),
        'end_date' => null,
    ]);

    Education::factory()->create([
        'doctor_id' => $doctor->id,
        'course' => 'MBBS',
    ]);

    Membership::factory()->create([
        'doctor_id' => $doctor->id,
        'title' => 'American Heart Association',
    ]);

    $clinic = Clinic::factory()->create([
        'doctor_id' => $doctor->id,
        'name' => "Sofi's Clinic",
        'address' => '2286 Sundown Lane, Old Trafford',
    ]);

    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id, 'name' => 'ECG Test']);
    DoctorService::factory()->create([
        'doctor_id' => $doctor->id,
        'service_id' => $service->id,
        'price' => 120,
    ]);

    $response = $this->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee('Dr Edalin Hendry');
    $response->assertSee('Available');
    $response->assertSee('Cambridge University Hospital');
    $response->assertSee('MBBS');
    $response->assertSee('American Heart Association');
    $response->assertSee("Sofi's Clinic");
    $response->assertSee('Cardiology');
    $response->assertSee('ECG Test');
    $response->assertSee('120.00');
    $response->assertSee('English, French');
    $response->assertSee('In Practice for 3 Years');
});

test('the favorite heart shows unselected for a guest and for a patient who has not favorited the doctor', function () {
    $doctor = User::factory()->doctor()->create();
    Experience::factory()->create(['doctor_id' => $doctor->id]);
    $patient = User::factory()->patient()->create();

    $guestResponse = $this->get(route('doctor.details', $doctor->id));
    $guestResponse->assertOk();
    $guestResponse->assertDontSee('fav-icon selected', false);

    $patientResponse = $this->actingAs($patient)->get(route('doctor.details', $doctor->id));
    $patientResponse->assertOk();
    $patientResponse->assertDontSee('fav-icon selected', false);
});

test('the favorite heart shows selected when the authenticated patient has favorited the doctor', function () {
    $doctor = User::factory()->doctor()->create();
    Experience::factory()->create(['doctor_id' => $doctor->id]);
    $patient = User::factory()->patient()->create();
    Favorite::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($patient)->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee('fav-icon selected', false);
});
