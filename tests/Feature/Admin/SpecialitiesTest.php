<?php

use App\Models\Admin;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the specialities index', function () {
    $this->get(route('admin.specialities.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the specialities index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.specialities.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin can view the specialities index with pagination', function () {
    $admin = Admin::factory()->create();
    Speciality::factory()->count(15)->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.specialities.index'));

    $response->assertOk();
    $response->assertViewHas('specialities', function ($specialities) {
        return $specialities->count() === 10 && $specialities->total() === 15;
    });
});

test('admin can create a speciality', function () {
    $admin = Admin::factory()->create();
    $image = UploadedFile::fake()->image('urology.jpg');

    $response = $this->actingAs($admin, 'admin')
        ->post(route('admin.specialities.store'), [
            'name' => 'Urology',
            'image' => $image,
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $speciality = Speciality::firstWhere('name', 'Urology');
    expect($speciality)->not->toBeNull();
    Storage::disk('s3')->assertExists($speciality->image);
});

test('speciality name is required and must be unique', function () {
    $admin = Admin::factory()->create();
    Speciality::factory()->create(['name' => 'Urology']);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.specialities.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.specialities.store'), ['name' => 'Urology'])
        ->assertSessionHasErrors('name');
});

test('admin can update a speciality', function () {
    $admin = Admin::factory()->create();
    $speciality = Speciality::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.specialities.update', $speciality), [
            'name' => 'New Name',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    expect($speciality->fresh()->name)->toBe('New Name');
});

test('updating a speciality can keep its own name without triggering a uniqueness error', function () {
    $admin = Admin::factory()->create();
    $speciality = Speciality::factory()->create(['name' => 'Cardiologist']);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.specialities.update', $speciality), [
            'name' => 'Cardiologist',
        ])
        ->assertSessionHasNoErrors();
});

test('admin can delete a speciality and its stored image', function () {
    $admin = Admin::factory()->create();
    $image = UploadedFile::fake()->image('dentist.jpg')->store('specialities', 's3');
    $speciality = Speciality::factory()->create(['image' => $image]);

    $response = $this->actingAs($admin, 'admin')
        ->delete(route('admin.specialities.destroy', $speciality));

    $response->assertRedirect();
    $this->assertModelMissing($speciality);
    Storage::disk('s3')->assertMissing($image);
});
