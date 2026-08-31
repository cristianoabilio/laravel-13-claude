<?php

use App\Models\Clinic;
use App\Models\DoctorService;
use App\Models\User;

test('the homepage loads successfully with no doctors', function () {
    $this->get(route('home'))->assertOk();
});

test('the homepage shows real doctor data in the featured doctors section', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Edalin Hendry',
        'designation' => 'Cardiologist',
        'availability_status' => 'available',
    ]);
    Clinic::factory()->create(['doctor_id' => $doctor->id, 'location' => 'Minneapolis, MN']);
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'price' => 150]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Dr Edalin Hendry');
    $response->assertSee('Cardiologist');
    $response->assertSee('Minneapolis, MN');
    $response->assertSee('150.00');
    $response->assertSee('Available');
});

test('a doctor without a clinic or priced service still renders gracefully', function () {
    User::factory()->doctor()->create([
        'display_name' => 'Dr No Extras',
        'availability_status' => 'not_available',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Dr No Extras');
    $response->assertSee('Contact for pricing');
    $response->assertSee('Not Available');
});

test('patients are not shown in the featured doctors section', function () {
    User::factory()->patient()->create(['first_name' => 'Should', 'last_name' => 'NotAppear']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('ShouldNotAppear');
});
