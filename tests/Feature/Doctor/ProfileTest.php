<?php

use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the doctor profile page', function () {
    $this->get(route('doctor.profile'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor profile page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.profile'))
        ->assertRedirect(route('dashboard'));
});

test('doctor can view their own profile with memberships', function () {
    $doctor = User::factory()->doctor()->create();
    $membership = Membership::factory()->create(['doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.profile'));

    $response->assertOk();
    $response->assertViewHas('doctor', function ($viewDoctor) use ($doctor, $membership) {
        return $viewDoctor->is($doctor)
            && $viewDoctor->memberships->pluck('id')->contains($membership->id);
    });
});

test('doctor can update their profile information', function () {
    $doctor = User::factory()->doctor()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($doctor)->put(route('doctor.profile.update'), [
        'first_name' => 'Edalin',
        'last_name' => 'Hendry',
        'display_name' => 'Dr Edalin Hendry',
        'designation' => 'Oral & Maxillofacial Surgeon',
        'phone' => '+1 555 123 4567',
        'email' => 'edalin@example.com',
        'known_languages' => 'English,German,Portuguese',
        'profile_photo' => $photo,
        'memberships' => [
            ['title' => 'American Dental Association', 'description' => 'Active member'],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $doctor->refresh();
    expect($doctor->first_name)->toBe('Edalin');
    expect($doctor->display_name)->toBe('Dr Edalin Hendry');
    expect($doctor->email)->toBe('edalin@example.com');
    expect($doctor->known_languages)->toBe(['English', 'German', 'Portuguese']);
    Storage::disk('s3')->assertExists($doctor->profile_photo);

    expect($doctor->memberships)->toHaveCount(1);
    expect($doctor->memberships->first()->title)->toBe('American Dental Association');
});

test('uploaded profile photos are resized to a 360x360 square', function () {
    $doctor = User::factory()->doctor()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg', 1200, 800);

    $this->actingAs($doctor)
        ->put(route('doctor.profile.update'), baseProfilePayload(['profile_photo' => $photo]))
        ->assertSessionHasNoErrors();

    $doctor->refresh();
    [$width, $height] = getimagesize(Storage::disk('s3')->path($doctor->profile_photo));

    expect($width)->toBe(360);
    expect($height)->toBe(360);
});

test('doctor can remove their profile photo', function () {
    $doctor = User::factory()->doctor()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg', 500, 500);

    $this->actingAs($doctor)->put(route('doctor.profile.update'), baseProfilePayload(['profile_photo' => $photo]));
    $storedPath = $doctor->refresh()->profile_photo;

    $this->actingAs($doctor)
        ->delete(route('doctor.profile.photo.destroy'))
        ->assertRedirect();

    expect($doctor->refresh()->profile_photo)->toBeNull();
    Storage::disk('s3')->assertMissing($storedPath);
});

test('doctor profile requires the core fields', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.profile.update'), [])
        ->assertSessionHasErrors(['first_name', 'last_name', 'display_name', 'designation', 'phone', 'email']);
});

test('doctor profile email must stay unique but can keep its own value', function () {
    $doctor = User::factory()->doctor()->create(['email' => 'doctor@example.com']);
    $other = User::factory()->doctor()->create(['email' => 'taken@example.com']);

    $this->actingAs($doctor)
        ->put(route('doctor.profile.update'), baseProfilePayload(['email' => 'taken@example.com']))
        ->assertSessionHasErrors('email');

    $this->actingAs($doctor)
        ->put(route('doctor.profile.update'), baseProfilePayload(['email' => 'doctor@example.com']))
        ->assertSessionHasNoErrors();
});

test('saving the profile replaces existing memberships with the submitted list', function () {
    $doctor = User::factory()->doctor()->create();
    Membership::factory()->count(2)->create(['doctor_id' => $doctor->id]);

    $this->actingAs($doctor)
        ->put(route('doctor.profile.update'), baseProfilePayload([
            'memberships' => [
                ['title' => 'New Membership', 'description' => 'Joined this year'],
            ],
        ]))
        ->assertSessionHasNoErrors();

    expect($doctor->memberships()->count())->toBe(1);
    expect($doctor->memberships()->first()->title)->toBe('New Membership');
});

test('guests cannot update known languages', function () {
    $this->patchJson(route('doctor.profile.languages.update'), ['known_languages' => 'English'])
        ->assertUnauthorized();
});

test('doctor can update known languages independently of the rest of the profile', function () {
    $doctor = User::factory()->doctor()->create([
        'first_name' => 'Edalin',
        'known_languages' => ['English'],
    ]);

    $response = $this->actingAs($doctor)
        ->patchJson(route('doctor.profile.languages.update'), [
            'known_languages' => 'English,German,Portuguese',
        ]);

    $response->assertOk();
    $response->assertJson([
        'known_languages' => ['English', 'German', 'Portuguese'],
    ]);

    $doctor->refresh();
    expect($doctor->known_languages)->toBe(['English', 'German', 'Portuguese']);
    expect($doctor->first_name)->toBe('Edalin');
});

test('known languages can be cleared independently', function () {
    $doctor = User::factory()->doctor()->create(['known_languages' => ['English']]);

    $this->actingAs($doctor)
        ->patchJson(route('doctor.profile.languages.update'), ['known_languages' => ''])
        ->assertOk();

    expect($doctor->refresh()->known_languages)->toBe([]);
});

function baseProfilePayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Edalin',
        'last_name' => 'Hendry',
        'display_name' => 'Dr Edalin Hendry',
        'designation' => 'Oral & Maxillofacial Surgeon',
        'phone' => '+1 555 123 4567',
        'email' => 'edalin@example.com',
    ], $overrides);
}
