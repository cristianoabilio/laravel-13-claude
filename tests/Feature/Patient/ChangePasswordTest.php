<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests are redirected away from the patient change password page', function () {
    $this->get(route('patient.change_password'))
        ->assertRedirect(route('login'));
});

test('doctors cannot access the patient change password page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.change_password'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('patient can update their password, then is logged out and sent to login', function () {
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->put(route('patient.change_password.update'), [
        'current_password' => 'password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status');

    $this->assertGuest();
    expect(Hash::check('new-secret-password', $patient->refresh()->password))->toBeTrue();
});

test('patient can log in with their new password after being logged out', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)->put(route('patient.change_password.update'), [
        'current_password' => 'password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]);

    $this->post(route('login'), [
        'email' => $patient->email,
        'password' => 'new-secret-password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($patient);
});

test('the correct current password must be provided', function () {
    $patient = User::factory()->patient()->create();
    $originalHash = $patient->password;

    $this->actingAs($patient)
        ->put(route('patient.change_password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])
        ->assertSessionHasErrors('current_password');

    expect($patient->refresh()->password)->toBe($originalHash);
});

test('the new password must be confirmed', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->put(route('patient.change_password.update'), [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'does-not-match',
        ])
        ->assertSessionHasErrors('password');
});

test('the new password is required', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->put(route('patient.change_password.update'), [
            'current_password' => 'password',
        ])
        ->assertSessionHasErrors('password');
});

test('a patient cannot update another patients password by manipulating the request', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();
    $otherOriginalHash = $other->password;

    $this->actingAs($patient)->put(route('patient.change_password.update'), [
        'current_password' => 'password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]);

    expect($other->refresh()->password)->toBe($otherOriginalHash);
});
