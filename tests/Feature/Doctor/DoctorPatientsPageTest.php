<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;

test('guests are redirected away from the doctor patients page', function () {
    $this->get(route('doctor.patients'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor patients page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.patients'))
        ->assertRedirect(route('dashboard'));
});

test('only patients with a completed appointment appear in my patients', function () {
    $doctor = User::factory()->doctor()->create();
    $treated = User::factory()->patient()->create();
    $onlyPending = User::factory()->patient()->create();

    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $treated->id, 'status' => AppointmentStatus::Completed]);
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $onlyPending->id, 'status' => AppointmentStatus::Pending]);

    $response = $this->actingAs($doctor)->get(route('doctor.patients'));

    $response->assertOk();
    $response->assertViewHas('patients', function ($patients) use ($treated, $onlyPending) {
        return $patients->pluck('id')->contains($treated->id)
            && ! $patients->pluck('id')->contains($onlyPending->id);
    });
});

test('a doctor does not see another doctors treated patients', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $otherPatient = User::factory()->patient()->create();

    Appointment::factory()->create(['doctor_id' => $other->id, 'patient_id' => $otherPatient->id, 'status' => AppointmentStatus::Completed]);

    $response = $this->actingAs($doctor)->get(route('doctor.patients'));

    $response->assertViewHas('patients', fn ($patients) => $patients->isEmpty());
});
