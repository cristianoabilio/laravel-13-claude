<?php

use App\Enums\PaymentStatus;
use App\Models\Admin;
use App\Models\DoctorService;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

test('guests are redirected away from the admin doctors page', function () {
    $this->get(route('admin.doctors.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the admin doctors page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.doctors.index'))
        ->assertRedirect(route('admin.login'));
});

test('an admin with no doctors sees an empty state', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.doctors.index'));

    $response->assertOk();
    $response->assertSee('No doctors have registered yet.');
});

test('admin sees real doctor data including speciality, member since, earned and availability', function () {
    $admin = Admin::factory()->create();
    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);
    $doctor = User::factory()->doctor()->create([
        'first_name' => 'Gregory',
        'last_name' => 'House',
        'display_name' => null,
        'availability_status' => 'available',
    ]);
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $service->id]);

    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 300, 'payment_status' => PaymentStatus::Paid]);
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 200, 'payment_status' => PaymentStatus::Paid]);
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 999, 'payment_status' => PaymentStatus::Failed]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.doctors.index'));

    $response->assertOk();
    $response->assertSee('Gregory House');
    $response->assertSee('Cardiology');
    $response->assertSee('500.00');
    $response->assertSee('Available');
    $response->assertViewHas('doctors', fn ($doctors) => $doctors->pluck('id')->contains($doctor->id));
});

test('a doctor with no priced services shows a placeholder speciality and zero earned', function () {
    $admin = Admin::factory()->create();
    User::factory()->doctor()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.doctors.index'));

    $response->assertOk();
    $response->assertSee('0.00');
});

test('patients are not listed on the admin doctors page', function () {
    $admin = Admin::factory()->create();
    User::factory()->patient()->create(['first_name' => 'Should', 'last_name' => 'NotAppear']);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.doctors.index'));

    $response->assertDontSee('ShouldNotAppear');
});

test('doctors are paginated fifteen per page', function () {
    $admin = Admin::factory()->create();
    User::factory()->count(17)->doctor()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.doctors.index'));

    $response->assertViewHas('doctors', fn ($doctors) => $doctors->count() === 15 && $doctors->total() === 17);
});
