<?php

use App\Enums\AppointmentStatus;
use App\Mail\AppointmentStatusMail;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('guests are redirected away from the doctor requests page', function () {
    $this->get(route('doctor.requests'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor requests page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.requests'))
        ->assertRedirect(route('dashboard'));
});

test('doctor sees only their own pending appointment requests', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();

    $pending = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Pending]);
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Confirmed]);
    Appointment::factory()->create(['doctor_id' => $other->id, 'status' => AppointmentStatus::Pending]);

    $response = $this->actingAs($doctor)->get(route('doctor.requests'));

    $response->assertOk();
    $response->assertViewHas('appointments', function ($appointments) use ($pending) {
        return $appointments->count() === 1 && $appointments->first()->is($pending);
    });
});

test('doctor can accept a pending appointment request and a status email is queued', function () {
    Mail::fake();

    $doctor = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Pending]);

    $response = $this->actingAs($doctor)->patch(route('doctor.requests.accept', $appointment));

    $response->assertOk();
    $response->assertJson(['status' => AppointmentStatus::Confirmed->value]);
    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Confirmed);

    Mail::assertQueued(AppointmentStatusMail::class, function ($mail) use ($appointment) {
        return $mail->appointment->is($appointment) && $mail->hasTo($appointment->email);
    });
});

test('doctor can reject a pending appointment request and a status email is queued', function () {
    Mail::fake();

    $doctor = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Pending]);

    $response = $this->actingAs($doctor)->patch(route('doctor.requests.reject', $appointment));

    $response->assertOk();
    $response->assertJson(['status' => AppointmentStatus::Cancelled->value]);
    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Cancelled);

    Mail::assertQueued(AppointmentStatusMail::class, function ($mail) use ($appointment) {
        return $mail->appointment->is($appointment) && $mail->hasTo($appointment->email);
    });
});

test('a doctor cannot act on another doctors appointment', function () {
    Mail::fake();

    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $other->id, 'status' => AppointmentStatus::Pending]);

    $this->actingAs($doctor)->patch(route('doctor.requests.accept', $appointment))->assertForbidden();

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Pending);
    Mail::assertNothingQueued();
});

test('an already handled appointment cannot be accepted or rejected again', function () {
    Mail::fake();

    $doctor = User::factory()->doctor()->create();
    $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id, 'status' => AppointmentStatus::Confirmed]);

    $this->actingAs($doctor)->patch(route('doctor.requests.accept', $appointment))->assertStatus(409);

    Mail::assertNothingQueued();
});
