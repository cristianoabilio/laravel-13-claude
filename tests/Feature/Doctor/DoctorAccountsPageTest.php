<?php

use App\Enums\PayoutRequestStatus;
use App\Models\Appointment;
use App\Models\DoctorBankAccount;
use App\Models\Payment;
use App\Models\PayoutRequest;
use App\Models\User;

test('guests are redirected away from the doctor accounts page', function () {
    $this->get(route('doctor.accounts'))
        ->assertRedirect(route('login'));
});

test('patients cannot access the doctor accounts page', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('doctor.accounts'))
        ->assertRedirect(route('dashboard'));
});

test('a doctor with no activity sees a zeroed out balance and empty state', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->get(route('doctor.accounts'));

    $response->assertOk();
    $response->assertViewHas('balance', fn ($balance) => $balance === ['paid' => 0.0, 'requested' => 0.0, 'earned' => 0.0, 'available' => 0.0]);
    $response->assertSee("haven't requested any payouts yet", false);
});

test('total balance reflects paid amounts minus pending and approved payout requests', function () {
    $doctor = User::factory()->doctor()->create();

    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 500, 'payment_status' => \App\Enums\PaymentStatus::Paid]);
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 300, 'payment_status' => \App\Enums\PaymentStatus::Paid]);
    PayoutRequest::factory()->create(['doctor_id' => $doctor->id, 'amount' => 100, 'status' => PayoutRequestStatus::Pending]);
    PayoutRequest::factory()->approved()->create(['doctor_id' => $doctor->id, 'amount' => 150]);
    PayoutRequest::factory()->cancelled()->create(['doctor_id' => $doctor->id, 'amount' => 9999]);

    $response = $this->actingAs($doctor)->get(route('doctor.accounts'));

    $response->assertOk();
    $response->assertViewHas('balance', function ($balance) {
        return $balance['paid'] === 800.0
            && $balance['requested'] === 100.0
            && $balance['earned'] === 150.0
            && $balance['available'] === 550.0;
    });
});

test('a doctor does not see another doctors payments or requests in their balance', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();

    Payment::factory()->create(['doctor_id' => $other->id, 'amount' => 1000, 'payment_status' => \App\Enums\PaymentStatus::Paid]);

    $response = $this->actingAs($doctor)->get(route('doctor.accounts'));

    $response->assertViewHas('balance', fn ($balance) => $balance['paid'] === 0.0);
});

test('a doctor can add their bank account details', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)->put(route('doctor.bank_account.update'), [
        'bank_name' => 'Citi Bank Inc',
        'branch_name' => 'London',
        'account_number' => '5396525019081234',
        'account_holder_name' => 'Darren Elder',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('doctor_bank_accounts', [
        'doctor_id' => $doctor->id,
        'bank_name' => 'Citi Bank Inc',
        'account_number' => '5396525019081234',
    ]);
});

test('saving bank account details twice updates the same record', function () {
    $doctor = User::factory()->doctor()->create();
    DoctorBankAccount::factory()->create(['doctor_id' => $doctor->id, 'bank_name' => 'Old Bank']);

    $this->actingAs($doctor)->put(route('doctor.bank_account.update'), [
        'bank_name' => 'New Bank',
        'branch_name' => 'Paris',
        'account_number' => '1111222233334444',
        'account_holder_name' => 'Darren Elder',
    ]);

    $this->assertDatabaseCount('doctor_bank_accounts', 1);
    expect($doctor->bankAccount->fresh()->bank_name)->toBe('New Bank');
});

test('bank account fields are required', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)->put(route('doctor.bank_account.update'), [])
        ->assertSessionHasErrors(['bank_name', 'account_number', 'account_holder_name']);
});

test('a doctor can request a payout within their available balance', function () {
    $doctor = User::factory()->doctor()->create();
    DoctorBankAccount::factory()->create(['doctor_id' => $doctor->id]);
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 500, 'payment_status' => \App\Enums\PaymentStatus::Paid]);

    $response = $this->actingAs($doctor)->post(route('doctor.payout_requests.store'), [
        'amount' => 200,
        'description' => 'Monthly payout',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('payout_requests', [
        'doctor_id' => $doctor->id,
        'amount' => 200,
        'status' => PayoutRequestStatus::Pending->value,
        'description' => 'Monthly payout',
    ]);

    $request = PayoutRequest::first();
    expect($request->bank_name)->toBe($doctor->bankAccount->bank_name);
    expect($request->account_number)->toBe($doctor->bankAccount->account_number);
});

test('a doctor cannot request more than their available balance', function () {
    $doctor = User::factory()->doctor()->create();
    DoctorBankAccount::factory()->create(['doctor_id' => $doctor->id]);
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 100, 'payment_status' => \App\Enums\PaymentStatus::Paid]);

    $response = $this->actingAs($doctor)->post(route('doctor.payout_requests.store'), [
        'amount' => 500,
    ]);

    $response->assertSessionHasErrors('amount');
    $this->assertDatabaseCount('payout_requests', 0);
});

test('a doctor cannot request a payout without a bank account on file', function () {
    $doctor = User::factory()->doctor()->create();
    Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 500, 'payment_status' => \App\Enums\PaymentStatus::Paid]);

    $response = $this->actingAs($doctor)->post(route('doctor.payout_requests.store'), [
        'amount' => 100,
    ]);

    $response->assertSessionHasErrors('amount');
    $this->assertDatabaseCount('payout_requests', 0);
});

test('requesting a zero or negative amount is rejected', function () {
    $doctor = User::factory()->doctor()->create();
    DoctorBankAccount::factory()->create(['doctor_id' => $doctor->id]);

    $this->actingAs($doctor)->post(route('doctor.payout_requests.store'), ['amount' => 0])
        ->assertSessionHasErrors('amount');
});

test('payout requests are paginated ten per page and scoped to the doctor', function () {
    $doctor = User::factory()->doctor()->create();
    $other = User::factory()->doctor()->create();

    PayoutRequest::factory()->count(12)->create(['doctor_id' => $doctor->id]);
    PayoutRequest::factory()->create(['doctor_id' => $other->id]);

    $response = $this->actingAs($doctor)->get(route('doctor.accounts'));

    $response->assertOk();
    $response->assertViewHas('payoutRequests', fn ($requests) => $requests->count() === 10 && $requests->total() === 12);
});
