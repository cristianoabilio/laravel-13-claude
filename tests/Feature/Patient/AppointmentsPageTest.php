<?php

use App\Models\Appointment;
use App\Models\User;

test('guests are redirected away from the patient appointments page', function () {
    $this->get(route('patient.appointments'))
        ->assertRedirect(route('login'));
});

test('doctors cannot access the patient appointments page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.appointments'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('a patient sees their own upcoming and cancelled appointments split into the right tabs', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();

    $confirmed = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'status' => 'confirmed',
        'service_name' => 'General Checkup',
    ]);
    $pending = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'status' => 'pending',
        'service_name' => 'Dental Cleaning',
    ]);
    $cancelled = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'status' => 'cancelled',
        'service_name' => 'Eye Exam',
    ]);

    $response = $this->actingAs($patient)->get(route('patient.appointments'));

    $response->assertOk();
    $response->assertViewHas('upcomingAppointments', function ($appointments) use ($confirmed, $pending) {
        return $appointments->pluck('id')->sort()->values()->all() === collect([$confirmed->id, $pending->id])->sort()->values()->all();
    });
    $response->assertViewHas('cancelledAppointments', fn ($appointments) => $appointments->pluck('id')->all() === [$cancelled->id]);

    $response->assertSee('General Checkup');
    $response->assertSee('Dental Cleaning');
    $response->assertSee('Eye Exam');
    $response->assertSee($confirmed->appointment_number);
});

test('a patient cannot see another patients appointments', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();

    $theirs = Appointment::factory()->create([
        'patient_id' => $other->id,
        'doctor_id' => $doctor->id,
        'appointment_number' => 'APT-2026-999999',
    ]);

    $response = $this->actingAs($patient)->get(route('patient.appointments'));

    $response->assertOk();
    $response->assertDontSee('APT-2026-999999');
});
