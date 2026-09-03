<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StorePayoutRequestRequest;
use App\Services\Doctor\DoctorAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PayoutRequestController extends Controller
{
    public function __construct(protected DoctorAccountService $accounts) {}

    public function store(StorePayoutRequestRequest $request): RedirectResponse
    {
        $this->accounts->createPayoutRequest(Auth::user(), $request->validated());

        return back()->with('success', 'Payout request submitted successfully.');
    }
}
