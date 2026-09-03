<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\User;

test('guests are redirected away from the patient details page', function () {
    $patient = User::factory()->patient()->create();

    $this->get(route('patient.details', $patient))
        ->assertRedirect(route('login'));
});

test('a doctor who has not treated the patient is forbidden', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $this->actingAs($doctor)
        ->get(route('patient.details', $patient))
        ->assertForbidden();
});

test('visiting a non patient id returns not found', function () {
    $doctor = User::factory()->doctor()->create();
    $otherDoctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.details', $otherDoctor))
        ->assertNotFound();
});

test('a doctor who completed an appointment with the patient can view their shared chart', function () {
    $doctor = User::factory()->doctor()->create();
    $anotherDoctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id, 'status' => AppointmentStatus::Completed]);
    $otherDoctorAppointment = Appointment::factory()->create(['doctor_id' => $anotherDoctor->id, 'patient_id' => $patient->id, 'status' => AppointmentStatus::Completed]);
    $record = MedicalRecord::factory()->create(['patient_id' => $patient->id]);
    $prescription = Prescription::factory()->create(['doctor_id' => $anotherDoctor->id, 'patient_id' => $patient->id]);

    $response = $this->actingAs($doctor)->get(route('patient.details', $patient));

    $response->assertOk();
    $response->assertViewHas('patient', fn ($viewPatient) => $viewPatient->is($patient));
    $response->assertViewHas('appointments', function ($appointments) use ($otherDoctorAppointment) {
        return $appointments->pluck('id')->contains($otherDoctorAppointment->id);
    });
    $response->assertViewHas('medicalRecords', fn ($records) => $records->pluck('id')->contains($record->id));
    $response->assertViewHas('prescriptions', fn ($prescriptions) => $prescriptions->pluck('id')->contains($prescription->id));
});
