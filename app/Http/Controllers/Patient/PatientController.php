<?php

namespace App\Http\Controllers\Patient;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdatePatientPasswordRequest;
use App\Http\Requests\Patient\UpdatePatientProfileRequest;
use App\Services\Patient\PatientProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(
        protected PatientProfileService $patientProfile,
    ) {}

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function settings(): View
    {
        return view('patient.dashboard.profile.patient_settings', [
            'patient' => Auth::user(),
        ]);
    }

    public function updateSettings(UpdatePatientProfileRequest $request): RedirectResponse
    {
        $this->patientProfile->update(
            Auth::user(),
            $request->safe()->except('profile_photo'),
            $request->file('profile_photo')
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function removeProfilePhoto(): RedirectResponse
    {
        $this->patientProfile->removePhoto(Auth::user());

        return back()->with('success', 'Profile photo removed successfully.');
    }

    public function changePassword(): View
    {
        return view('patient.dashboard.profile.change_password');
    }

    public function updatePassword(UpdatePatientPasswordRequest $request): RedirectResponse
    {
        $this->patientProfile->updatePassword(Auth::user(), $request->validated('password'));

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Your password has been updated. Please log in again.');
    }

    public function appointments(): View
    {
        $appointments = Auth::user()
            ->patientAppointments()
            ->with(['doctor', 'clinic'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->get();

        return view('patient.dashboard.appointments.appointments', [
            'upcomingAppointments' => $appointments->where('status', '!=', AppointmentStatus::Cancelled)->values(),
            'cancelledAppointments' => $appointments->where('status', AppointmentStatus::Cancelled)->values(),
        ]);
    }

    public function invoices(): View
    {
        $invoices = Auth::user()
            ->patientInvoices()
            ->with(['doctor', 'appointment'])
            ->orderByDesc('generated_at')
            ->get();

        return view('patient.dashboard.invoice.invoices', [
            'invoices' => $invoices,
        ]);
    }

    public function favorites(): View
    {
        $favorites = Auth::user()
            ->favoriteDoctors()
            ->with(['doctor', 'doctor.user'])
            ->get();

        return view('patient.dashboard.favorites.favorites', [
            'favorites' => $favorites,
        ]);
    }
}
