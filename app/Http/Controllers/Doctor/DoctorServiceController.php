<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorServicesRequest;
use App\Models\DoctorService;
use App\Models\Speciality;
use App\Services\Doctor\DoctorServiceManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DoctorServiceController extends Controller
{
    public function __construct(protected DoctorServiceManager $services) {}

    public function update(UpdateDoctorServicesRequest $request): RedirectResponse
    {
        $this->services->upsert(Auth::user(), $request->validated('services', []));

        return back()->with('success', 'Services saved successfully.');
    }

    public function destroy(DoctorService $doctorService): RedirectResponse
    {
        abort_if($doctorService->doctor_id !== Auth::id(), 403);

        $this->services->delete($doctorService);

        return back()->with('success', 'Service removed successfully.');
    }

    public function destroySpeciality(Speciality $speciality): RedirectResponse
    {
        $this->services->deleteForSpeciality(Auth::user(), $speciality);

        return back()->with('success', 'Speciality services removed successfully.');
    }
}
