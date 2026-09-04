<?php

use App\Models\DoctorService;
use App\Models\Experience;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

test('the speciality page loads for a real speciality', function () {
    $speciality = Speciality::factory()->create();

    $this->get(route('doctor.all.speciality', $speciality))->assertOk();
});

test('an unknown speciality id returns not found', function () {
    $this->get(route('doctor.all.speciality', 999999))->assertNotFound();
});

test('it shows only doctors who offer a service within this speciality', function () {
    $speciality = Speciality::factory()->create();
    $otherSpeciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);
    $otherService = Service::factory()->create(['speciality_id' => $otherSpeciality->id]);

    $inSpeciality = User::factory()->doctor()->create(['first_name' => 'Included', 'last_name' => 'Doctor']);
    DoctorService::factory()->create(['doctor_id' => $inSpeciality->id, 'service_id' => $service->id]);

    $outsideSpeciality = User::factory()->doctor()->create(['first_name' => 'Excluded', 'last_name' => 'Doctor']);
    DoctorService::factory()->create(['doctor_id' => $outsideSpeciality->id, 'service_id' => $otherService->id]);

    $response = $this->get(route('doctor.all.speciality', $speciality));

    $response->assertOk();
    $response->assertViewHas('doctors', function ($doctors) use ($inSpeciality, $outsideSpeciality) {
        return $doctors->pluck('id')->contains($inSpeciality->id)
            && ! $doctors->pluck('id')->contains($outsideSpeciality->id);
    });
});

test('the search filter matches by doctor name', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $match = User::factory()->doctor()->create(['first_name' => 'Gregory', 'last_name' => 'House']);
    DoctorService::factory()->create(['doctor_id' => $match->id, 'service_id' => $service->id]);

    $noMatch = User::factory()->doctor()->create(['first_name' => 'James', 'last_name' => 'Wilson']);
    DoctorService::factory()->create(['doctor_id' => $noMatch->id, 'service_id' => $service->id]);

    $response = $this->get(route('doctor.all.speciality', $speciality).'?search=Gregory');

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->all() === [$match->id]);
});

test('the gender filter narrows results', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $male = User::factory()->doctor()->create(['gender' => 'male']);
    DoctorService::factory()->create(['doctor_id' => $male->id, 'service_id' => $service->id]);

    $female = User::factory()->doctor()->create(['gender' => 'female']);
    DoctorService::factory()->create(['doctor_id' => $female->id, 'service_id' => $service->id]);

    $response = $this->get(route('doctor.all.speciality', $speciality).'?gender[]=female');

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->all() === [$female->id]);
});

test('the availability filter only shows available doctors', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $available = User::factory()->doctor()->create(['availability_status' => 'available']);
    DoctorService::factory()->create(['doctor_id' => $available->id, 'service_id' => $service->id]);

    $unavailable = User::factory()->doctor()->create(['availability_status' => 'not_available']);
    DoctorService::factory()->create(['doctor_id' => $unavailable->id, 'service_id' => $service->id]);

    $response = $this->get(route('doctor.all.speciality', $speciality).'?available=1');

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->all() === [$available->id]);
});

test('the price range filter matches the doctors service price within this speciality', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $cheap = User::factory()->doctor()->create();
    DoctorService::factory()->create(['doctor_id' => $cheap->id, 'service_id' => $service->id, 'price' => 50]);

    $expensive = User::factory()->doctor()->create();
    DoctorService::factory()->create(['doctor_id' => $expensive->id, 'service_id' => $service->id, 'price' => 400]);

    $response = $this->get(route('doctor.all.speciality', $speciality).'?min_price=200&max_price=500');

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->all() === [$expensive->id]);
});

test('the experience filter matches doctors with at least the selected years', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $senior = User::factory()->doctor()->create();
    DoctorService::factory()->create(['doctor_id' => $senior->id, 'service_id' => $service->id]);
    Experience::factory()->create(['doctor_id' => $senior->id, 'years_of_experience' => 12]);

    $junior = User::factory()->doctor()->create();
    DoctorService::factory()->create(['doctor_id' => $junior->id, 'service_id' => $service->id]);
    Experience::factory()->create(['doctor_id' => $junior->id, 'years_of_experience' => 1]);

    $response = $this->get(route('doctor.all.speciality', $speciality).'?experience[]=10');

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->all() === [$senior->id]);
});

test('filters combine with an and condition', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    $matchesBoth = User::factory()->doctor()->create(['gender' => 'female', 'availability_status' => 'available']);
    DoctorService::factory()->create(['doctor_id' => $matchesBoth->id, 'service_id' => $service->id]);

    $matchesOnlyGender = User::factory()->doctor()->create(['gender' => 'female', 'availability_status' => 'not_available']);
    DoctorService::factory()->create(['doctor_id' => $matchesOnlyGender->id, 'service_id' => $service->id]);

    $response = $this->get(route('doctor.all.speciality', $speciality).'?gender[]=female&available=1');

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->all() === [$matchesBoth->id]);
});

test('results are paginated nine per page', function () {
    $speciality = Speciality::factory()->create();
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);

    User::factory()->count(11)->doctor()->create()->each(function ($doctor) use ($service) {
        DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $service->id]);
    });

    $response = $this->get(route('doctor.all.speciality', $speciality));

    $response->assertOk();
    $response->assertViewHas('doctors', fn ($doctors) => $doctors->count() === 9 && $doctors->total() === 11);
});

test('an empty speciality shows the empty state without error', function () {
    $speciality = Speciality::factory()->create();

    $response = $this->get(route('doctor.all.speciality', $speciality));

    $response->assertOk();
    $response->assertSee('No doctors match these filters yet', false);
});
