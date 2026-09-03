<?php

use App\Enums\PayoutRequestStatus;
use App\Models\Admin;
use App\Models\PayoutRequest;
use App\Models\User;

test('guests are redirected away from the admin payout requests page', function () {
    $this->get(route('admin.payout_requests.index'))
        ->assertRedirect(route('admin.login'));
});

test('regular users cannot access the admin payout requests page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.payout_requests.index'))
        ->assertRedirect(route('admin.login'));
});

test('admin can view payout requests with pagination', function () {
    $admin = Admin::factory()->create();
    PayoutRequest::factory()->count(17)->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.payout_requests.index'));

    $response->assertOk();
    $response->assertViewHas('payoutRequests', fn ($requests) => $requests->count() === 15 && $requests->total() === 17);
});

test('admin can approve a pending payout request', function () {
    $admin = Admin::factory()->create();
    $payoutRequest = PayoutRequest::factory()->create(['status' => PayoutRequestStatus::Pending]);

    $response = $this->actingAs($admin, 'admin')
        ->patch(route('admin.payout_requests.approve', $payoutRequest));

    $response->assertRedirect();
    $payoutRequest->refresh();
    expect($payoutRequest->status)->toBe(PayoutRequestStatus::Approved);
    expect($payoutRequest->processed_by)->toBe($admin->id);
    expect($payoutRequest->processed_at)->not->toBeNull();
});

test('admin can cancel a pending payout request', function () {
    $admin = Admin::factory()->create();
    $payoutRequest = PayoutRequest::factory()->create(['status' => PayoutRequestStatus::Pending]);

    $response = $this->actingAs($admin, 'admin')
        ->patch(route('admin.payout_requests.cancel', $payoutRequest));

    $response->assertRedirect();
    $payoutRequest->refresh();
    expect($payoutRequest->status)->toBe(PayoutRequestStatus::Cancelled);
    expect($payoutRequest->processed_by)->toBe($admin->id);
});

test('an already processed payout request cannot be approved again', function () {
    $admin = Admin::factory()->create();
    $payoutRequest = PayoutRequest::factory()->approved()->create();

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.payout_requests.approve', $payoutRequest))
        ->assertStatus(409);
});

test('an already processed payout request cannot be cancelled again', function () {
    $admin = Admin::factory()->create();
    $payoutRequest = PayoutRequest::factory()->cancelled()->create();

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.payout_requests.cancel', $payoutRequest))
        ->assertStatus(409);
});

test('a doctor cannot approve their own payout request', function () {
    $doctor = User::factory()->doctor()->create();
    $payoutRequest = PayoutRequest::factory()->create(['doctor_id' => $doctor->id, 'status' => PayoutRequestStatus::Pending]);

    $this->actingAs($doctor)
        ->patch(route('admin.payout_requests.approve', $payoutRequest))
        ->assertRedirect(route('admin.login'));

    expect($payoutRequest->fresh()->status)->toBe(PayoutRequestStatus::Pending);
});

test('an approved payout request moves the amount from requested to earned for the doctor', function () {
    $admin = Admin::factory()->create();
    $doctor = User::factory()->doctor()->create();
    \App\Models\Payment::factory()->create(['doctor_id' => $doctor->id, 'amount' => 500, 'payment_status' => \App\Enums\PaymentStatus::Paid]);
    $payoutRequest = PayoutRequest::factory()->create(['doctor_id' => $doctor->id, 'amount' => 200, 'status' => PayoutRequestStatus::Pending]);

    $this->actingAs($admin, 'admin')->patch(route('admin.payout_requests.approve', $payoutRequest));

    $response = $this->actingAs($doctor)->get(route('doctor.accounts'));
    $response->assertViewHas('balance', function ($balance) {
        return $balance['requested'] === 0.0 && $balance['earned'] === 200.0 && $balance['available'] === 300.0;
    });
});
