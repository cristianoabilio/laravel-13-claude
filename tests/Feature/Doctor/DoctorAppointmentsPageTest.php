<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;

test('guests are redirected away from the doctor appointments page', function () {
    $this->get(route('doctor.appointments'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor appointments page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.appointments'))
        ->assertRedirect(route('dashboard'));
});

test('doctor sees their appointments split into upcoming, cancelled and completed', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();

    $confirmed = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Confirmed]);
    $cancelled = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Cancelled]);
    $completed = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Completed]);
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Pending]);
    Appointment::factory()->create(['doctor_id' => $other->id, 'status' => AppointmentStatus::Confirmed]);

    $response = $this->actingAs($doctor)->get(route('doctor.appointments'));

    $response->assertOk();
    $response->assertViewHas('upcomingAppointments', fn ($appts) => $appts->count() === 1 && $appts->first()->is($confirmed));
    $response->assertViewHas('cancelledAppointments', fn ($appts) => $appts->count() === 1 && $appts->first()->is($cancelled));
    $response->assertViewHas('completedAppointments', fn ($appts) => $appts->count() === 1 && $appts->first()->is($completed));
});

test('doctor can mark a confirmed appointment as completed', function () {
    $doctor = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Confirmed]);

    $response = $this->actingAs($doctor)->patch(route('doctor.appointments.complete', $appointment));

    $response->assertOk();
    $response->assertJson(['status' => AppointmentStatus::Completed->value]);
    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Completed);
});

test('a pending appointment cannot be marked as completed', function () {
    $doctor = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Pending]);

    $this->actingAs($doctor)->patch(route('doctor.appointments.complete', $appointment))
        ->assertStatus(409);

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Pending);
});

test('a doctor cannot complete another doctors appointment', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $other->id, 'status' => AppointmentStatus::Confirmed]);

    $this->actingAs($doctor)->patch(route('doctor.appointments.complete', $appointment))
        ->assertForbidden();

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Confirmed);
});
