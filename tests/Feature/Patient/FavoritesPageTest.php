<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Favorite;
use App\Models\User;

test('guests are redirected away from the patient favorites page', function () {
    $this->get(route('patient.favorites'))
        ->assertRedirect(route('login'));
});

test('doctors cannot access the patient favorites page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.favorites'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('a patient with no favorites sees an empty state', function () {
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->get(route('patient.favorites'));

    $response->assertOk();
    $response->assertSee("You haven't favourited any doctors yet.", false);
});

test('a patient sees their own favorited doctors with real data', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Favorite Test',
        'designation' => 'Cardiologist',
    ]);
    Clinic::factory()->create(['doctor_id' => $doctor->id, 'location' => 'Springfield']);
    Favorite::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($patient)->get(route('patient.favorites'));

    $response->assertOk();
    $response->assertViewHas('favoriteDoctors', fn ($doctors) => $doctors->pluck('id')->all() === [$doctor->id]);
    $response->assertSee('Dr Favorite Test');
    $response->assertSee('Cardiologist');
    $response->assertSee('Springfield');
});

test('a patient does not see another patients favorites', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create(['display_name' => 'Not Mine']);
    Favorite::factory()->create(['patient_id' => $other->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($patient)->get(route('patient.favorites'));

    $response->assertOk();
    $response->assertDontSee('Not Mine');
});

test('the last booked date is shown when the patient has an appointment history with that doctor', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    Favorite::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => '2026-01-15',
    ]);

    $response = $this->actingAs($patient)->get(route('patient.favorites'));

    $response->assertOk();
    $response->assertSee('Last Book on 15 Jan 2026');
});
