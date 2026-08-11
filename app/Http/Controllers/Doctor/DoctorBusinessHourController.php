<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateBusinessHoursRequest;
use App\Services\Doctor\DoctorBusinessHourService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DoctorBusinessHourController extends Controller
{
    public function __construct(protected DoctorBusinessHourService $businessHours) {}

    public function update(UpdateBusinessHoursRequest $request): RedirectResponse
    {
        $this->businessHours->update(Auth::user(), $request->validated('business_hours', []));

        return back()->with('success', 'Business hours saved successfully.');
    }
}
