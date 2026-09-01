<?php

use App\Models\Favorite;
use App\Models\User;

test('guests cannot favorite a doctor', function () {
    $doctor = User::factory()->doctor()->create();

    // This app's redirectGuestsTo callback always redirects, even for
    // JSON-expecting requests, matching every other protected route.
    $this->postJson(route('doctor.favorite.toggle', $doctor->id))
        ->assertRedirect(route('login'));
});

test('doctors cannot favorite another doctor', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->post(route('doctor.favorite.toggle', $other->id))
        ->assertRedirect(route('doctor.dashboard'));
});

test('a patient can favorite a doctor', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($patient)->postJson(route('doctor.favorite.toggle', $doctor->id));

    $response->assertOk();
    $response->assertJson(['favorited' => true]);
    expect(Favorite::where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->exists())->toBeTrue();
});

test('favoriting an already-favorited doctor unfavorites them', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();
    Favorite::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($patient)->postJson(route('doctor.favorite.toggle', $doctor->id));

    $response->assertOk();
    $response->assertJson(['favorited' => false]);
    expect(Favorite::where('patient_id', $patient->id)->where('doctor_id', $doctor->id)->exists())->toBeFalse();
});

test('a patient id cannot be favorited as if it were a doctor', function () {
    $patient = User::factory()->patient()->create();
    $otherPatient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->postJson(route('doctor.favorite.toggle', $otherPatient->id))
        ->assertNotFound();
});

test('two different patients can independently favorite the same doctor', function () {
    $patientOne = User::factory()->patient()->create();
    $patientTwo = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($patientOne)->postJson(route('doctor.favorite.toggle', $doctor->id))->assertJson(['favorited' => true]);
    $this->actingAs($patientTwo)->postJson(route('doctor.favorite.toggle', $doctor->id))->assertJson(['favorited' => true]);

    expect(Favorite::where('doctor_id', $doctor->id)->count())->toBe(2);
});
