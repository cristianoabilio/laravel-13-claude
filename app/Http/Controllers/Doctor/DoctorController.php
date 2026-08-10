<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorLanguagesRequest;
use App\Http\Requests\Doctor\UpdateDoctorProfileRequest;
use App\Services\Doctor\DoctorProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(protected DoctorProfileService $doctorProfile) {}

    public function index()
    {
        return view('doctor.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function profile(): View
    {
        return view('doctor.dashboard.profile.doctor_profile', [
            'doctor' => Auth::user()->load('memberships'),
        ]);
    }

    public function updateProfile(UpdateDoctorProfileRequest $request): RedirectResponse
    {
        $this->doctorProfile->update(
            Auth::user(),
            $request->safe()->except('profile_photo'),
            $request->file('profile_photo')
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function removeProfilePhoto(): RedirectResponse
    {
        $this->doctorProfile->removePhoto(Auth::user());

        return back()->with('success', 'Profile photo removed successfully.');
    }

    public function updateLanguages(UpdateDoctorLanguagesRequest $request): JsonResponse
    {
        $doctor = $this->doctorProfile->updateLanguages(Auth::user(), $request->validated('known_languages', []));

        return response()->json([
            'message' => 'Known languages updated successfully.',
            'known_languages' => $doctor->known_languages,
        ]);
    }

    public function experience()
    {
        return view('doctor.dashboard.profile.doctor_experience');
    }

    public function education()
    {
        return view('doctor.dashboard.profile.doctor_education');
    }

    public function clinics()
    {
        return view('doctor.dashboard.profile.doctor_clinics');
    }

    public function business()
    {
        return view('doctor.dashboard.profile.doctor_business_hours');
    }
}
