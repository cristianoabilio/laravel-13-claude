<?php

use App\Enums\AppointmentStatus;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\DoctorService;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

test('guests are redirected away from the admin appointments page', function () {
    $this->get(route('admin.appointments.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the admin appointments page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.appointments.index'))
        ->assertRedirect(route('admin.login'));
});

test('an admin with no appointments sees an empty state', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.appointments.index'));

    $response->assertOk();
    $response->assertSee('No appointments have been booked yet.');
});

test('admin sees real appointment data including doctor, patient, speciality, status and amount', function () {
    $admin = Admin::factory()->create();
    $doctor = User::factory()->doctor()->create(['first_name' => 'Gregory', 'last_name' => 'House', 'display_name' => null]);
    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);
    $doctorService = DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $service->id]);

    $appointment = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'doctor_service_id' => $doctorService->id,
        'first_name' => 'Jamie',
        'last_name' => 'Rivers',
        'status' => AppointmentStatus::Confirmed,
        'total_amount' => 249.50,
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.appointments.index'));

    $response->assertOk();
    $response->assertSee('Gregory House');
    $response->assertSee('Cardiology');
    $response->assertSee('Jamie Rivers');
    $response->assertSee('Confirmed');
    $response->assertSee('249.50');
    $response->assertViewHas('appointments', fn ($appointments) => $appointments->pluck('id')->contains($appointment->id));
});

test('an appointment without a linked service shows a speciality placeholder instead of an error', function () {
    $admin = Admin::factory()->create();
    Appointment::factory()->create(['doctor_service_id' => null]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.appointments.index'));

    $response->assertOk();
});

test('appointments are paginated fifteen per page ordered by most recent appointment date', function () {
    $admin = Admin::factory()->create();
    Appointment::factory()->count(17)->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.appointments.index'));

    $response->assertOk();
    $response->assertViewHas('appointments', fn ($appointments) => $appointments->count() === 15 && $appointments->total() === 17);
});

test('appointments from every status are shown, not filtered to one status', function () {
    $admin = Admin::factory()->create();
    $pending = Appointment::factory()->create(['status' => AppointmentStatus::Pending]);
    $cancelled = Appointment::factory()->create(['status' => AppointmentStatus::Cancelled]);
    $completed = Appointment::factory()->create(['status' => AppointmentStatus::Completed]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.appointments.index'));

    $response->assertViewHas('appointments', function ($appointments) use ($pending, $cancelled, $completed) {
        $ids = $appointments->pluck('id');

        return $ids->contains($pending->id) && $ids->contains($cancelled->id) && $ids->contains($completed->id);
    });
});
