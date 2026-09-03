<?php

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

test('guests are redirected away from the doctor invoices page', function () {
    $this->get(route('doctor.invoices'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor invoices page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.invoices'))
        ->assertRedirect(route('dashboard'));
});

test('a doctor with no invoices sees an empty state', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->get(route('doctor.invoices'));

    $response->assertOk();
    $response->assertSee("You don't have any invoices yet.", false);
});

test('a doctor sees their own real invoice data', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create(['first_name' => 'Jamie', 'last_name' => 'Rivers']);

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $invoice = Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'invoice_number' => 'INV-2026-000456',
        'total' => 249.50,
    ]);

    $response = $this->actingAs($doctor)->get(route('doctor.invoices'));

    $response->assertOk();
    $response->assertViewHas('invoices', fn ($invoices) => $invoices->pluck('id')->all() === [$invoice->id]);
    $response->assertSee('INV-2026-000456');
    $response->assertSee('Jamie Rivers');
    $response->assertSee('249.50');
});

test('a doctor does not see another doctors invoices', function () {
    $doctor = User::factory()->doctor()->create();
    $otherDoctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $otherDoctor->id]);
    Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $otherDoctor->id,
        'invoice_number' => 'INV-2026-999888',
    ]);

    $response = $this->actingAs($doctor)->get(route('doctor.invoices'));

    $response->assertOk();
    $response->assertDontSee('INV-2026-999888');
});

test('the invoice view and download links point to the real appointment', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor)->get(route('doctor.invoices'));

    $response->assertOk();
    $response->assertSee(route('appointments.invoice.download', $appointment), false);
});

test('invoices are paginated ten per page', function () {
    $doctor = User::factory()->doctor()->create();

    Invoice::factory()->count(15)->create([
        'appointment_id' => fn () => Appointment::factory()->create(['doctor_id' => $doctor->id])->id,
        'doctor_id' => $doctor->id,
        'patient_id' => fn (array $attrs) => Appointment::find($attrs['appointment_id'])->patient_id,
    ]);

    $response = $this->actingAs($doctor)->get(route('doctor.invoices'));

    $response->assertOk();
    $response->assertViewHas('invoices', fn ($invoices) => $invoices->count() === 10 && $invoices->total() === 15);
});

test('the treating doctor can download the invoice', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($doctor)
        ->get(route('appointments.invoice.download', $appointment))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('a doctor cannot download another doctors invoice', function () {
    $doctor = User::factory()->doctor()->create();
    $otherDoctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $otherDoctor->id]);
    Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $otherDoctor->id,
    ]);

    $this->actingAs($doctor)
        ->get(route('appointments.invoice.download', $appointment))
        ->assertForbidden();
});
