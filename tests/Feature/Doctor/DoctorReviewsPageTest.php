<?php

use App\Models\Review;
use App\Models\User;

test('guests are redirected away from the doctor reviews page', function () {
    $this->get(route('doctor.reviews'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor reviews page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.reviews'))
        ->assertRedirect(route('dashboard'));
});

test('a doctor with no reviews sees an empty state', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->get(route('doctor.reviews'));

    $response->assertOk();
    $response->assertSee("You don't have any reviews yet.", false);
});

test('a doctor sees their own reviews with the correct average rating', function () {
    $doctor = User::factory()->doctor()->create();
    $reviewer = User::factory()->patient()->create(['first_name' => 'Alex', 'last_name' => 'Stone']);
    Review::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $reviewer->id, 'rating' => 5, 'comment' => 'Superb']);
    Review::factory()->create(['doctor_id' => $doctor->id, 'rating' => 3]);

    $response = $this->actingAs($doctor)->get(route('doctor.reviews'));

    $response->assertOk();
    $response->assertSee('Alex Stone');
    $response->assertSee('Superb');
    $response->assertViewHas('averageRating', 4.0);
    $response->assertViewHas('reviewsCount', 2);
});

test('a doctor does not see another doctors reviews', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    Review::factory()->create(['doctor_id' => $other->id, 'comment' => 'Should not appear here']);

    $response = $this->actingAs($doctor)->get(route('doctor.reviews'));

    $response->assertOk();
    $response->assertDontSee('Should not appear here');
});

test('doctor reviews are paginated ten per page', function () {
    $doctor = User::factory()->doctor()->create();
    Review::factory()->count(12)->create(['doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.reviews'));

    $response->assertViewHas('reviews', fn ($reviews) => $reviews->count() === 10 && $reviews->total() === 12);
});
