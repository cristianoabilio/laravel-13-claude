<?php

namespace App\Services\Doctor;

use App\Enums\PaymentStatus;
use App\Enums\PayoutRequestStatus;
use App\Models\Admin;
use App\Models\Payment;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DoctorAccountService
{
    /**
     * The doctor's balance breakdown:
     *  - paid: everything ever collected for this doctor's appointments.
     *  - requested: sum of payout requests still awaiting an admin decision.
     *  - earned: sum of payout requests an admin has approved (already paid out).
     *  - available: paid minus requested minus earned - the amount that can
     *    still be requested. Computed via aggregate SUM queries scoped by an
     *    indexed doctor_id, so this stays cheap regardless of history size.
     *
     * @return array{paid: float, requested: float, earned: float, available: float}
     */
    public function balanceFor(User $doctor): array
    {
        $paid = (float) Payment::query()
            ->where('doctor_id', $doctor->id)
            ->where('payment_status', PaymentStatus::Paid)
            ->sum('amount');

        $requested = (float) PayoutRequest::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', PayoutRequestStatus::Pending)
            ->sum('amount');

        $earned = (float) PayoutRequest::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', PayoutRequestStatus::Approved)
            ->sum('amount');

        return [
            'paid' => $paid,
            'requested' => $requested,
            'earned' => $earned,
            'available' => max(0.0, $paid - $requested - $earned),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPayoutRequest(User $doctor, array $data): PayoutRequest
    {
        $bankAccount = $doctor->bankAccount;

        if (! $bankAccount) {
            throw ValidationException::withMessages([
                'amount' => 'Add your bank account details before requesting a payout.',
            ]);
        }

        $available = $this->balanceFor($doctor)['available'];

        if ($data['amount'] > $available) {
            throw ValidationException::withMessages([
                'amount' => 'The requested amount exceeds your available balance of $'.number_format($available, 2).'.',
            ]);
        }

        return PayoutRequest::create([
            'doctor_id' => $doctor->id,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'status' => PayoutRequestStatus::Pending,
            'bank_name' => $bankAccount->bank_name,
            'branch_name' => $bankAccount->branch_name,
            'account_number' => $bankAccount->account_number,
            'account_holder_name' => $bankAccount->account_holder_name,
        ]);
    }

    public function approve(PayoutRequest $payoutRequest, Admin $admin): void
    {
        abort_if($payoutRequest->status !== PayoutRequestStatus::Pending, 409, 'This request has already been processed.');

        $payoutRequest->update([
            'status' => PayoutRequestStatus::Approved,
            'processed_at' => now(),
            'processed_by' => $admin->id,
        ]);
    }

    public function cancel(PayoutRequest $payoutRequest, Admin $admin): void
    {
        abort_if($payoutRequest->status !== PayoutRequestStatus::Pending, 409, 'This request has already been processed.');

        $payoutRequest->update([
            'status' => PayoutRequestStatus::Cancelled,
            'processed_at' => now(),
            'processed_by' => $admin->id,
        ]);
    }
}
