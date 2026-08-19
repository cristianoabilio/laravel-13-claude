<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the patient settings page', function () {
    $this->get(route('patient.settings'))
        ->assertRedirect(route('login'));
});

test('doctors cannot access the patient settings page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.settings'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('patient can view their own settings', function () {
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->get(route('patient.settings'));

    $response->assertOk();
    $response->assertViewHas('patient', fn ($viewPatient) => $viewPatient->is($patient));
});

test('patient can update their profile information', function () {
    $patient = User::factory()->patient()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($patient)->put(route('patient.settings.update'), basePatientProfilePayload([
        'email' => 'jamet@example.com',
        'profile_photo' => $photo,
    ]));

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $patient->refresh();
    expect($patient->first_name)->toBe('Jamet');
    expect($patient->last_name)->toBe('Cyrus');
    expect($patient->date_of_birth->format('d/m/Y'))->toBe('24/03/1994');
    expect($patient->phone)->toBe('+1 555 123 4567');
    expect($patient->email)->toBe('jamet@example.com');
    expect($patient->blood_group)->toBe('B+ve');
    expect($patient->address)->toBe('4517 Washington Ave');
    expect($patient->city)->toBe('Manchester');
    expect($patient->state)->toBe('England');
    expect($patient->country)->toBe('United Kingdom');
    expect($patient->pincode)->toBe('M14 5RP');
    Storage::disk('s3')->assertExists($patient->profile_photo);
});

test('uploaded profile photos are resized to a 360x360 square', function () {
    $patient = User::factory()->patient()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg', 1200, 800);

    $this->actingAs($patient)
        ->put(route('patient.settings.update'), basePatientProfilePayload(['profile_photo' => $photo]))
        ->assertSessionHasNoErrors();

    $patient->refresh();
    [$width, $height] = getimagesize(Storage::disk('s3')->path($patient->profile_photo));

    expect($width)->toBe(360);
    expect($height)->toBe(360);
});

test('patient can remove their profile photo', function () {
    $patient = User::factory()->patient()->create();
    $photo = UploadedFile::fake()->image('avatar.jpg', 500, 500);

    $this->actingAs($patient)->put(route('patient.settings.update'), basePatientProfilePayload(['profile_photo' => $photo]));
    $storedPath = $patient->refresh()->profile_photo;

    $this->actingAs($patient)
        ->delete(route('patient.settings.photo.destroy'))
        ->assertRedirect();

    expect($patient->refresh()->profile_photo)->toBeNull();
    Storage::disk('s3')->assertMissing($storedPath);
});

test('patient settings requires the core fields', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->put(route('patient.settings.update'), [])
        ->assertSessionHasErrors([
            'first_name', 'last_name', 'date_of_birth', 'phone', 'email',
            'blood_group', 'address', 'city', 'state', 'country', 'pincode',
        ]);
});

test('patient settings email must stay unique but can keep its own value', function () {
    $patient = User::factory()->patient()->create(['email' => 'patient@example.com']);
    $other = User::factory()->patient()->create(['email' => 'taken@example.com']);

    $this->actingAs($patient)
        ->put(route('patient.settings.update'), basePatientProfilePayload(['email' => 'taken@example.com']))
        ->assertSessionHasErrors('email');

    $this->actingAs($patient)
        ->put(route('patient.settings.update'), basePatientProfilePayload(['email' => 'patient@example.com']))
        ->assertSessionHasNoErrors();
});

test('patient settings rejects an invalid date of birth format', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->put(route('patient.settings.update'), basePatientProfilePayload(['date_of_birth' => '1994-03-24']))
        ->assertSessionHasErrors('date_of_birth');
});

test('patient settings rejects an unknown blood group', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->put(route('patient.settings.update'), basePatientProfilePayload(['blood_group' => 'X+ve']))
        ->assertSessionHasErrors('blood_group');
});

test('a patient cannot update another patients profile by manipulating the request', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create(['first_name' => 'Original']);

    $this->actingAs($patient)->put(route('patient.settings.update'), basePatientProfilePayload());

    expect($other->refresh()->first_name)->toBe('Original');
});

function basePatientProfilePayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Jamet',
        'last_name' => 'Cyrus',
        'date_of_birth' => '24/03/1994',
        'phone' => '+1 555 123 4567',
        'email' => 'jamet@example.com',
        'blood_group' => 'B+ve',
        'address' => '4517 Washington Ave',
        'city' => 'Manchester',
        'state' => 'England',
        'country' => 'United Kingdom',
        'pincode' => 'M14 5RP',
    ], $overrides);
}
