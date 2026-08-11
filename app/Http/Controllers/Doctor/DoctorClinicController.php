<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateClinicsRequest;
use App\Models\Clinic;
use App\Models\ClinicImage;
use App\Services\Doctor\DoctorClinicService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DoctorClinicController extends Controller
{
    public function __construct(protected DoctorClinicService $clinics) {}

    public function update(UpdateClinicsRequest $request): RedirectResponse
    {
        $this->clinics->upsert(Auth::user(), $request->validated('clinics', []));

        return back()->with('success', 'Clinic saved successfully. New gallery photos are being processed and will appear shortly.');
    }

    public function destroy(Clinic $clinic): RedirectResponse
    {
        abort_if($clinic->doctor_id !== Auth::id(), 403);

        $this->clinics->delete($clinic);

        return back()->with('success', 'Clinic deleted successfully.');
    }

    public function destroyLogo(Clinic $clinic): RedirectResponse
    {
        abort_if($clinic->doctor_id !== Auth::id(), 403);

        $this->clinics->removeLogo($clinic);

        return back()->with('success', 'Logo removed successfully.');
    }

    public function destroyImage(ClinicImage $image): RedirectResponse
    {
        abort_if($image->clinic->doctor_id !== Auth::id(), 403);

        $this->clinics->removeGalleryImage($image);

        return back()->with('success', 'Gallery image removed successfully.');
    }
}
