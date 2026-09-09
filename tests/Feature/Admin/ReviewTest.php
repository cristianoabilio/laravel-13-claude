<?php

use App\Models\Admin;
use App\Models\Review;
use App\Models\User;

test('guests are redirected away from the admin reviews page', function () {
    $this->get(route('admin.reviews'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the admin reviews page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reviews'))
        ->assertRedirect(route('admin.login'));
});

test('admin sees real patient and doctor names on a review', function () {
    $admin = Admin::factory()->create();
    $doctor = User::factory()->doctor()->create(['first_name' => 'Gregory', 'last_name' => 'House', 'display_name' => null]);
    $patient = User::factory()->patient()->create(['first_name' => 'James', 'last_name' => 'Wilson']);
    Review::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id, 'comment' => 'Fantastic bedside manner']);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.reviews'));

    $response->assertOk();
    $response->assertSee('Gregory House');
    $response->assertSee('James Wilson');
    $response->assertSee('Fantastic bedside manner');
});

test('admin reviews are paginated fifteen per page', function () {
    $admin = Admin::factory()->create();
    Review::factory()->count(17)->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.reviews'));

    $response->assertOk();
    $response->assertViewHas('reviews', fn ($reviews) => $reviews->count() === 15 && $reviews->total() === 17);
});

test('admin can delete a review', function () {
    $admin = Admin::factory()->create();
    $review = Review::factory()->create();

    $response = $this->actingAs($admin, 'admin')->delete(route('admin.reviews.destroy', $review));

    $response->assertRedirect();
    $this->assertModelMissing($review);
});
