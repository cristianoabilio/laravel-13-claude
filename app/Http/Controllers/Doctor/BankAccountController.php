<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateBankAccountRequest;
use App\Services\Doctor\DoctorBankAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function __construct(protected DoctorBankAccountService $bankAccounts) {}

    public function update(UpdateBankAccountRequest $request): RedirectResponse
    {
        $this->bankAccounts->save(Auth::user(), $request->validated());

        return back()->with('success', 'Bank account details saved successfully.');
    }
}
