<?php

use App\Enums\PaymentStatus;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;

test('guests are redirected away from the admin patients page', function () {
    $this->get(route('admin.patients.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the admin patients page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.patients.index'))
        ->assertRedirect(route('admin.login'));
});

test('an admin with no patients sees an empty state', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.patients.index'));

    $response->assertOk();
    $response->assertSee('No patients have registered yet.');
});

test('admin sees real patient data including age, address, phone, last visit and paid amount', function () {
    $admin = Admin::factory()->create();
    $patient = User::factory()->patient()->create([
        'first_name' => 'Jamie',
        'last_name' => 'Rivers',
        'display_name' => null,
        'date_of_birth' => now()->subYears(30)->format('Y-m-d'),
        'address' => '123 Main St',
        'city' => 'Springfield',
        'state' => 'IL',
        'pincode' => '62704',
        'phone' => '5551234567',
    ]);

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'appointment_date' => now()->subDays(3)->format('Y-m-d')]);
    Payment::factory()->create(['patient_id' => $patient->id, 'appointment_id' => $appointment->id, 'amount' => 150, 'payment_status' => PaymentStatus::Paid]);
    Payment::factory()->create(['patient_id' => $patient->id, 'amount' => 100, 'payment_status' => PaymentStatus::Paid]);
    Payment::factory()->create(['patient_id' => $patient->id, 'amount' => 999, 'payment_status' => PaymentStatus::Refunded]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.patients.index'));

    $response->assertOk();
    $response->assertSee('Jamie Rivers');
    $response->assertSee('30');
    $response->assertSee('123 Main St, Springfield, IL, 62704');
    $response->assertSee('5551234567');
    $response->assertSee(now()->subDays(3)->format('d M Y'));
    $response->assertSee('250.00');
    $response->assertViewHas('patients', fn ($patients) => $patients->pluck('id')->contains($patient->id));
});

test('a patient with no appointments or payments shows placeholders instead of errors', function () {
    $admin = Admin::factory()->create();
    User::factory()->patient()->create(['date_of_birth' => null, 'address' => null, 'city' => null, 'phone' => null]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.patients.index'));

    $response->assertOk();
    $response->assertSee('0.00');
});

test('doctors are not listed on the admin patients page', function () {
    $admin = Admin::factory()->create();
    User::factory()->doctor()->create(['first_name' => 'Should', 'last_name' => 'NotAppear']);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.patients.index'));

    $response->assertDontSee('ShouldNotAppear');
});

test('patients are paginated fifteen per page', function () {
    $admin = Admin::factory()->create();
    User::factory()->count(17)->patient()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.patients.index'));

    $response->assertViewHas('patients', fn ($patients) => $patients->count() === 15 && $patients->total() === 17);
});
