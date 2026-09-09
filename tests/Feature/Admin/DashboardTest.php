<?php

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\DoctorService;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

test('guests are redirected away from the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

test('an admin with no data sees the dashboard with zeroed counts and empty states', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewHas('doctorsCount', 0);
    $response->assertViewHas('patientsCount', 0);
    $response->assertViewHas('appointmentsCount', 0);
    $response->assertViewHas('totalRevenue', 0.0);
    $response->assertSee('No doctors have registered yet.');
    $response->assertSee('No patients have registered yet.');
    $response->assertSee('No appointments have been booked yet.');
    $response->assertSee('No paid revenue recorded yet.');
    $response->assertSee('No doctor or patient sign-ups recorded yet.');
});

test('the dashboard shows real counts across doctors, patients, appointments and revenue', function () {
    $admin = Admin::factory()->create();
    $doctors = User::factory()->count(3)->doctor()->create();
    $patients = User::factory()->count(4)->patient()->create();
    $appointments = collect([
        Appointment::factory()->create(['doctor_id' => $doctors[0]->id, 'patient_id' => $patients[0]->id]),
        Appointment::factory()->create(['doctor_id' => $doctors[1]->id, 'patient_id' => $patients[1]->id]),
    ]);
    Payment::factory()->create(['appointment_id' => $appointments[0]->id, 'doctor_id' => $appointments[0]->doctor_id, 'patient_id' => $appointments[0]->patient_id, 'amount' => 100, 'payment_status' => PaymentStatus::Paid, 'paid_at' => now()]);
    Payment::factory()->create(['appointment_id' => $appointments[1]->id, 'doctor_id' => $appointments[1]->doctor_id, 'patient_id' => $appointments[1]->patient_id, 'amount' => 50, 'payment_status' => PaymentStatus::Paid, 'paid_at' => now()]);
    Payment::factory()->create(['appointment_id' => $appointments[0]->id, 'doctor_id' => $appointments[0]->doctor_id, 'patient_id' => $appointments[0]->patient_id, 'amount' => 999, 'payment_status' => PaymentStatus::Failed]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewHas('doctorsCount', 3);
    $response->assertViewHas('patientsCount', 4);
    $response->assertViewHas('appointmentsCount', 2);
    $response->assertViewHas('totalRevenue', 150.0);
    $response->assertSee('150.00');
});

test('the doctors list shows real speciality, earned and average rating', function () {
    $admin = Admin::factory()->create();
    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);
    $doctor = User::factory()->doctor()->create(['first_name' => 'Gregory', 'last_name' => 'House', 'display_name' => null]);
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $service->id]);
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 300, 'payment_status' => PaymentStatus::Paid]);
    Review::factory()->create(['doctor_id' => $doctor->id, 'rating' => 4]);
    Review::factory()->create(['doctor_id' => $doctor->id, 'rating' => 2]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Gregory House');
    $response->assertSee('Cardiology');
    $response->assertSee('300.00');
    $response->assertViewHas('doctors', function ($doctors) use ($doctor) {
        $row = $doctors->firstWhere('id', $doctor->id);

        return $row && (float) $row->average_rating === 3.0;
    });
});

test('the patients list shows real phone, last visit and paid amount', function () {
    $admin = Admin::factory()->create();
    $patient = User::factory()->patient()->create(['first_name' => 'Jamie', 'last_name' => 'Rivers', 'display_name' => null, 'phone' => '5551234567']);
    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'appointment_date' => now()->subDays(2)->format('Y-m-d')]);
    Payment::factory()->create(['patient_id' => $patient->id, 'appointment_id' => $appointment->id, 'amount' => 120, 'payment_status' => PaymentStatus::Paid]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Jamie Rivers');
    $response->assertSee('5551234567');
    $response->assertSee('120.00');
});

test('the appointment list shows real doctor, patient, status and amount', function () {
    $admin = Admin::factory()->create();
    $doctor = User::factory()->doctor()->create(['first_name' => 'Meredith', 'last_name' => 'Grey', 'display_name' => null]);
    Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'first_name' => 'Alex',
        'last_name' => 'Karev',
        'status' => AppointmentStatus::Confirmed,
        'total_amount' => 275.25,
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Meredith Grey');
    $response->assertSee('Alex Karev');
    $response->assertSee('Confirmed');
    $response->assertSee('275.25');
});

test('only the five most recent doctors, patients and appointments are shown', function () {
    $admin = Admin::factory()->create();
    User::factory()->count(7)->doctor()->create();
    User::factory()->count(7)->patient()->create();
    Appointment::factory()->count(7)->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewHas('doctors', fn ($doctors) => $doctors->count() === 5);
    $response->assertViewHas('patients', fn ($patients) => $patients->count() === 5);
    $response->assertViewHas('appointments', fn ($appointments) => $appointments->count() === 5);
});
