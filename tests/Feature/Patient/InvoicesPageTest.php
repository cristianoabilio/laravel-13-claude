<?php

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;

test('guests are redirected away from the patient invoices page', function () {
    $this->get(route('patient.invoices'))
        ->assertRedirect(route('login'));
});

test('doctors cannot access the patient invoices page', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->get(route('patient.invoices'))
        ->assertRedirect(route('doctor.dashboard'));
});

test('a patient with no invoices sees an empty state', function () {
    $patient = User::factory()->patient()->create();

    $response = $this->actingAs($patient)->get(route('patient.invoices'));

    $response->assertOk();
    $response->assertSee("You don't have any invoices yet.", false);
});

test('a patient sees their own real invoice data', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create(['display_name' => 'Dr Test Doctor']);

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $invoice = Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'invoice_number' => 'INV-2026-000123',
        'total' => 199.99,
    ]);

    $response = $this->actingAs($patient)->get(route('patient.invoices'));

    $response->assertOk();
    $response->assertViewHas('invoices', fn ($invoices) => $invoices->pluck('id')->all() === [$invoice->id]);
    $response->assertSee('INV-2026-000123');
    $response->assertSee('Dr Test Doctor');
    $response->assertSee('199.99');
});

test('a patient cannot see another patients invoices', function () {
    $patient = User::factory()->patient()->create();
    $other = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();

    $appointment = Appointment::factory()->create(['patient_id' => $other->id, 'doctor_id' => $doctor->id]);
    Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $other->id,
        'doctor_id' => $doctor->id,
        'invoice_number' => 'INV-2026-999999',
    ]);

    $response = $this->actingAs($patient)->get(route('patient.invoices'));

    $response->assertOk();
    $response->assertDontSee('INV-2026-999999');
});

test('the invoice view and download links point to the real appointment', function () {
    $patient = User::factory()->patient()->create();
    $doctor = User::factory()->doctor()->create();

    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($patient)->get(route('patient.invoices'));

    $response->assertOk();
    $response->assertSee(route('appointments.confirmation', $appointment), false);
    $response->assertSee(route('appointments.invoice.download', $appointment), false);
});
