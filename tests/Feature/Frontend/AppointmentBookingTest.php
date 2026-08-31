<?php

use App\Enums\DayOfWeek;
use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Clinic;
use App\Models\DoctorService;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');

    $this->doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Edalin Hendry',
        'availability_status' => 'available',
    ]);

    foreach (DayOfWeek::cases() as $day) {
        BusinessHour::factory()->create([
            'doctor_id' => $this->doctor->id,
            'day' => $day,
            'is_open' => true,
            'from_time' => '09:00:00',
            'to_time' => '18:00:00',
        ]);
    }

    $this->clinic = Clinic::factory()->create(['doctor_id' => $this->doctor->id]);

    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id, 'name' => 'ECG Test']);
    $this->doctorService = DoctorService::factory()->create([
        'doctor_id' => $this->doctor->id,
        'service_id' => $service->id,
        'price' => 100,
        'duration_minutes' => 30,
    ]);

    $this->patient = User::factory()->patient()->create([
        'first_name' => 'Jamet',
        'last_name' => 'Cyrus',
        'phone' => '+1 555 123 4567',
        'email' => 'jamet@example.com',
    ]);
});

function bookingPayload(array $overrides = []): array
{
    return array_merge([
        'doctor_service_ids' => [],
        'appointment_type' => 'clinic',
        'appointment_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'first_name' => 'Jamet',
        'last_name' => 'Cyrus',
        'phone' => '+1 555 123 4567',
        'email' => 'jamet@example.com',
        'symptoms' => 'Mild chest discomfort',
        'reason_for_visit' => 'Routine check',
        'card_holder_name' => 'Jamet Cyrus',
        'card_number' => '4242 4242 4242 4242',
        'card_expiry' => '12/30',
        'card_cvv' => '123',
    ], $overrides);
}

test('guests are redirected to login instead of the booking page', function () {
    $this->get(route('doctor.booking', $this->doctor->id))
        ->assertRedirect(route('login'));
});

test('doctors are blocked from the booking page', function () {
    $this->actingAs($this->doctor)
        ->get(route('doctor.booking', $this->doctor->id))
        ->assertRedirect(route('doctor.dashboard'));
});

test('guests cannot submit a booking', function () {
    $this->post(route('booking.store', $this->doctor->id), bookingPayload())
        ->assertRedirect(route('login'));

    expect(Appointment::count())->toBe(0);
});

test('doctors cannot submit a booking even if they hit the endpoint directly', function () {
    $this->actingAs($this->doctor)
        ->post(route('booking.store', $this->doctor->id), bookingPayload([
            'doctor_service_ids' => [$this->doctorService->id],
            'clinic_id' => $this->clinic->id,
        ]))
        ->assertRedirect(route('doctor.dashboard'));

    expect(Appointment::count())->toBe(0);
});

test('a patient can book a full appointment end to end', function () {
    $document = UploadedFile::fake()->create('lab-result.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
        'documents' => [$document],
    ]));

    $appointment = Appointment::first();

    $response->assertRedirect(route('appointments.confirmation', $appointment));

    expect($appointment)->not->toBeNull();
    expect($appointment->appointment_number)->toBe('APT-'.now()->year.'-'.str_pad($appointment->id, 6, '0', STR_PAD_LEFT));
    expect($appointment->patient_id)->toBe($this->patient->id);
    expect($appointment->doctor_id)->toBe($this->doctor->id);
    expect($appointment->clinic_id)->toBe($this->clinic->id);
    expect($appointment->status->value)->toBe('pending');
    expect($appointment->payment_status->value)->toBe('paid');
    expect((float) $appointment->consultation_fee)->toBe(100.0);
    expect((float) $appointment->total_amount)->toBe(100.0);
    expect($appointment->start_time->format('H:i'))->toBe('09:00');
    expect($appointment->end_time->format('H:i'))->toBe('09:30');

    expect($appointment->services)->toHaveCount(1);
    expect($appointment->services->first()->service_name)->toBe('ECG Test');

    expect($appointment->documents)->toHaveCount(1);
    Storage::disk('s3')->assertExists($appointment->documents->first()->file_path);

    $payment = $appointment->payment;
    expect($payment)->not->toBeNull();
    expect($payment->card_last_four)->toBe('4242');
    expect($payment->payment_status->value)->toBe('paid');
    expect(str_starts_with($payment->transaction_id, 'TXN-'))->toBeTrue();

    $invoice = $appointment->invoice;
    expect($invoice)->not->toBeNull();
    expect($invoice->invoice_number)->toBe('INV-'.now()->year.'-'.str_pad($invoice->id, 6, '0', STR_PAD_LEFT));
    expect((float) $invoice->total)->toBe(100.0);
    expect($invoice->pdf_path)->not->toBeNull();
    Storage::disk('s3')->assertExists($invoice->pdf_path);
});

test('a patient can book more than one service in a single appointment', function () {
    $secondService = DoctorService::factory()->create([
        'doctor_id' => $this->doctor->id,
        'service_id' => Service::factory()->create(['speciality_id' => $this->doctorService->service->speciality_id]),
        'price' => 50,
        'duration_minutes' => 15,
    ]);

    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id, $secondService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    $appointment = Appointment::first();

    expect($appointment->services)->toHaveCount(2);
    expect((float) $appointment->consultation_fee)->toBe(150.0);
    expect($appointment->duration_minutes)->toBe(45);
    expect($appointment->end_time->format('H:i'))->toBe('09:45');
});

test('a doctor cannot receive two bookings for the same date and time slot', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    expect(Appointment::count())->toBe(1);

    $otherPatient = User::factory()->patient()->create();

    $response = $this->actingAs($otherPatient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
        'first_name' => 'Other',
        'last_name' => 'Patient',
        'email' => 'other@example.com',
    ]));

    $response->assertRedirect();
    $response->assertSessionHasErrors('start_time');
    expect(Appointment::count())->toBe(1);
});

test('a cancelled appointment frees its slot back up for booking', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    Appointment::first()->update(['status' => 'cancelled']);

    $otherPatient = User::factory()->patient()->create();

    $this->actingAs($otherPatient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
        'first_name' => 'Other',
        'last_name' => 'Patient',
        'email' => 'other@example.com',
    ]))->assertSessionHasNoErrors();

    expect(Appointment::count())->toBe(2);
});

test('a clinic must be selected for clinic appointments', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'appointment_type' => 'clinic',
        'clinic_id' => null,
    ]))->assertSessionHasErrors('clinic_id');
});

test('an address is required for home visit appointments', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'appointment_type' => 'home_visit',
        'clinic_id' => null,
        'home_visit_address' => '',
    ]))->assertSessionHasErrors('home_visit_address');
});

test('a home visit booking does not require a clinic', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'appointment_type' => 'home_visit',
        'clinic_id' => null,
        'home_visit_address' => '42 Home Street',
    ]))->assertSessionHasNoErrors();

    expect(Appointment::first()->clinic_id)->toBeNull();
    expect(Appointment::first()->home_visit_address)->toBe('42 Home Street');
});

test('a service must belong to the doctor being booked', function () {
    $otherDoctor = User::factory()->doctor()->create();
    $foreignService = DoctorService::factory()->create(['doctor_id' => $otherDoctor->id]);

    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$foreignService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertSessionHasErrors('doctor_service_ids.0');
});

test('a clinic must belong to the doctor being booked', function () {
    $foreignClinic = Clinic::factory()->create();

    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $foreignClinic->id,
    ]))->assertSessionHasErrors('clinic_id');
});

test('an expired card is rejected', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
        'card_expiry' => '01/20',
    ]))->assertSessionHasErrors('card_expiry');
});

test('a past appointment date is rejected', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
        'appointment_date' => now()->subDay()->toDateString(),
    ]))->assertSessionHasErrors('appointment_date');
});

test('the card number and cvv are never persisted', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    $payment = Appointment::first()->payment;

    expect($payment->getAttributes())->not->toHaveKey('card_number');
    expect($payment->getAttributes())->not->toHaveKey('card_cvv');
    expect(strlen($payment->card_last_four))->toBe(4);
});

test('the slot loading endpoint returns real availability for a date', function () {
    $response = $this->actingAs($this->patient)->getJson(route('booking.slots', $this->doctor->id).'?'.http_build_query([
        'doctor_service_ids' => [$this->doctorService->id],
        'date' => now()->addDay()->toDateString(),
    ]));

    $response->assertOk();
    $response->assertJsonFragment(['time' => '09:00', 'available' => true]);
});

test('the slot loading endpoint marks an already booked slot as unavailable', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    $response = $this->actingAs($this->patient)->getJson(route('booking.slots', $this->doctor->id).'?'.http_build_query([
        'doctor_service_ids' => [$this->doctorService->id],
        'date' => now()->addDay()->toDateString(),
    ]));

    $response->assertJsonFragment(['time' => '09:00', 'available' => false]);
});

test('a patient cannot view another patients booking confirmation', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    $appointment = Appointment::first();
    $otherPatient = User::factory()->patient()->create();

    $this->actingAs($otherPatient)
        ->get(route('appointments.confirmation', $appointment))
        ->assertForbidden();

    $this->actingAs($otherPatient)
        ->get(route('appointments.invoice.download', $appointment))
        ->assertForbidden();
});

test('the booking owner can view their confirmation and download their invoice', function () {
    $this->actingAs($this->patient)->post(route('booking.store', $this->doctor->id), bookingPayload([
        'doctor_service_ids' => [$this->doctorService->id],
        'clinic_id' => $this->clinic->id,
    ]))->assertRedirect();

    $appointment = Appointment::first();

    $this->actingAs($this->patient)
        ->get(route('appointments.confirmation', $appointment))
        ->assertOk()
        ->assertSee($appointment->appointment_number);

    $this->actingAs($this->patient)
        ->get(route('appointments.invoice.download', $appointment))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
