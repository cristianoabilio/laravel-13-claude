<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\User;

function treatedPatient(User $doctor): User
{
    $patient = User::factory()->patient()->create();

    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id, 'status' => AppointmentStatus::Completed]);

    return $patient;
}

test('a doctor can add a prescription for a patient they have treated', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = treatedPatient($doctor);

    $response = $this->actingAs($doctor)->post(route('doctor.prescriptions.store', $patient), [
        'items' => [
            ['medicine_name' => 'Ecosprin 75MG', 'dosage' => '75 mg', 'frequency' => '1-0-0-1', 'duration' => '1 month', 'timings' => 'Before Meal'],
        ],
        'other_information' => 'Rest well',
        'follow_up' => 'After 2 weeks',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('prescriptions', [
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'other_information' => 'Rest well',
        'follow_up' => 'After 2 weeks',
    ]);

    $prescription = Prescription::first();
    expect($prescription->prescription_number)->toStartWith('PR-'.now()->year.'-');
    $this->assertDatabaseHas('prescription_items', [
        'prescription_id' => $prescription->id,
        'medicine_name' => 'Ecosprin 75MG',
    ]);
});

test('adding a prescription requires at least one medicine item', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = treatedPatient($doctor);

    $response = $this->actingAs($doctor)->post(route('doctor.prescriptions.store', $patient), [
        'items' => [],
    ]);

    $response->assertSessionHasErrors('items');
    $this->assertDatabaseCount('prescriptions', 0);
});

test('a doctor cannot add a prescription for a patient they have not treated', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $this->actingAs($doctor)->post(route('doctor.prescriptions.store', $patient), [
        'items' => [
            ['medicine_name' => 'Ecosprin'],
        ],
    ])->assertForbidden();

    $this->assertDatabaseCount('prescriptions', 0);
});

test('patients cannot add prescriptions', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = treatedPatient($doctor);

    $this->actingAs($patient)->post(route('doctor.prescriptions.store', $patient), [
        'items' => [['medicine_name' => 'Ecosprin']],
    ])->assertRedirect(route('dashboard'));
});

test('a prescription written for a patient shows up on their medical records page', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = treatedPatient($doctor);
    $prescription = Prescription::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $response = $this->actingAs($patient)->get(route('patient.medical_appointments'));

    $response->assertOk();
    $response->assertViewHas('prescriptions', fn ($prescriptions) => $prescriptions->pluck('id')->contains($prescription->id));
});
