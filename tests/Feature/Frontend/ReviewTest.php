<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\User;

function completedAppointmentWith(User $doctor): User
{
    $patient = User::factory()->patient()->create();

    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id, 'status' => AppointmentStatus::Completed]);

    return $patient;
}

test('guests cannot submit a review', function () {
    $doctor = User::factory()->doctor()->create();

    $this->post(route('doctor.reviews.store', $doctor->id), ['rating' => 5, 'comment' => 'Great doctor'])
        ->assertRedirect(route('login'));
});

test('doctors cannot submit a review', function () {
    $doctor = User::factory()->doctor()->create();
    $otherDoctor = User::factory()->doctor()->create();

    $this->actingAs($otherDoctor)
        ->post(route('doctor.reviews.store', $doctor->id), ['rating' => 5, 'comment' => 'Great doctor'])
        ->assertRedirect(route('doctor.dashboard'));
});

test('a patient who completed an appointment can leave a review', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = completedAppointmentWith($doctor);

    $response = $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), [
        'rating' => 5,
        'comment' => 'Excellent care and very attentive.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('reviews', [
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'rating' => 5,
        'comment' => 'Excellent care and very attentive.',
    ]);
});

test('a patient who never had an appointment with the doctor cannot review them', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), [
        'rating' => 5,
        'comment' => 'Great doctor',
    ]);

    $response->assertSessionHasErrors('rating');
    $this->assertDatabaseCount('reviews', 0);
});

test('a patient with only a pending or cancelled appointment cannot review the doctor', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id, 'status' => AppointmentStatus::Confirmed]);

    $response = $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), [
        'rating' => 4,
        'comment' => 'Looking forward to the visit',
    ]);

    $response->assertSessionHasErrors('rating');
    $this->assertDatabaseCount('reviews', 0);
});

test('a patient cannot review the same doctor twice', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = completedAppointmentWith($doctor);
    Review::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $response = $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), [
        'rating' => 3,
        'comment' => 'Second attempt',
    ]);

    $response->assertSessionHasErrors('rating');
    $this->assertDatabaseCount('reviews', 1);
});

test('rating must be between 1 and 5', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = completedAppointmentWith($doctor);

    $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), [
        'rating' => 6,
        'comment' => 'Too high a rating',
    ])->assertSessionHasErrors('rating');

    $this->assertDatabaseCount('reviews', 0);
});

test('comment is required', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = completedAppointmentWith($doctor);

    $this->actingAs($patient)->post(route('doctor.reviews.store', $doctor->id), [
        'rating' => 5,
    ])->assertSessionHasErrors('comment');
});

test('the doctor details page shows real reviews and the average rating', function () {
    $doctor = User::factory()->doctor()->create();
    $reviewer = User::factory()->patient()->create(['first_name' => 'Jamie', 'last_name' => 'Rivers']);
    Review::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $reviewer->id, 'rating' => 4, 'comment' => 'Very thorough and kind.']);

    $response = $this->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee('Jamie Rivers');
    $response->assertSee('Very thorough and kind.');
    $response->assertViewHas('reviewsCount', 1);
    $response->assertViewHas('averageRating', 4.0);
});

test('the doctor details page tells a patient they have not met the doctor', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee("You didn't meet with this doctor.", false);
});

test('the doctor details page lets an eligible patient see the review form', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = completedAppointmentWith($doctor);

    $response = $this->actingAs($patient)->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee('Write a review for');
});

test('the doctor details page tells a patient they already reviewed the doctor', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = completedAppointmentWith($doctor);
    Review::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $response = $this->actingAs($patient)->get(route('doctor.details', $doctor->id));

    $response->assertOk();
    $response->assertSee('You have already reviewed this doctor.');
});
