<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests are redirected away from the doctor change password page', function () {
    $this->get(route('doctor.change_password'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor change password page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.change_password'))
        ->assertRedirect(route('dashboard'));
});

test('doctor can update their password, then is logged out and sent to login', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->put(route('doctor.change_password.update'), [
        'current_password' => 'password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status');

    $this->assertGuest();
    expect(Hash::check('new-secret-password', $doctor->refresh()->password))->toBeTrue();
});

test('doctor can log in with their new password after being logged out', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.change_password.update'), [
        'current_password' => 'password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]);

    $this->post(route('login'), [
        'email' => $doctor->email,
        'password' => 'new-secret-password',
    ])->assertRedirect(route('doctor.dashboard'));

    $this->assertAuthenticatedAs($doctor);
});

test('the correct current password must be provided', function () {
    $doctor = User::factory()->doctor()->create();
    $originalHash = $doctor->password;

    $this->actingAs($doctor)
        ->put(route('doctor.change_password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])
        ->assertSessionHasErrors('current_password');

    expect($doctor->refresh()->password)->toBe($originalHash);
});

test('the new password must be confirmed', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.change_password.update'), [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'does-not-match',
        ])
        ->assertSessionHasErrors('password');
});

test('the new password is required', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->put(route('doctor.change_password.update'), [
            'current_password' => 'password',
        ])
        ->assertSessionHasErrors('password');
});

test('a doctor cannot update another doctors password by manipulating the request', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();
    $otherOriginalHash = $other->password;

    $this->actingAs($doctor)->put(route('doctor.change_password.update'), [
        'current_password' => 'password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]);

    expect($other->refresh()->password)->toBe($otherOriginalHash);
});
