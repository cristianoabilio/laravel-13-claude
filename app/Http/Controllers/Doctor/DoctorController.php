<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorAvailabilityRequest;
use App\Http\Requests\Doctor\UpdateDoctorLanguagesRequest;
use App\Http\Requests\Doctor\UpdateDoctorPasswordRequest;
use App\Http\Requests\Doctor\UpdateDoctorProfileRequest;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use App\Services\Doctor\DoctorBusinessHourService;
use App\Services\Doctor\DoctorPatientService;
use App\Services\Doctor\DoctorProfileService;
use App\Services\Doctor\DoctorServiceManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(
        protected DoctorProfileService $doctorProfile,
        protected DoctorBusinessHourService $businessHours,
        protected DoctorServiceManager $services,
        protected DoctorPatientService $doctorPatients,
    ) {}

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

    public function updateAvailability(UpdateDoctorAvailabilityRequest $request): JsonResponse
    {
        $doctor = $this->doctorProfile->updateAvailability(Auth::user(), $request->validated('availability_status'));

        return response()->json([
            'message' => 'Availability updated successfully.',
            'availability_status' => $doctor->availability_status,
        ]);
    }

    public function experience(): View
    {
        return view('doctor.dashboard.profile.doctor_experience', [
            'doctor' => Auth::user()->load('experiences'),
        ]);
    }

    public function education(): View
    {
        return view('doctor.dashboard.profile.doctor_education', [
            'doctor' => Auth::user()->load('educations'),
        ]);
    }

    public function clinics(): View
    {
        return view('doctor.dashboard.profile.doctor_clinics', [
            'doctor' => Auth::user()->load('clinics.images'),
        ]);
    }

    public function business(): View
    {
        return view('doctor.dashboard.profile.doctor_business_hours', [
            'businessHours' => $this->businessHours->forDoctor(Auth::user()->load('businessHours')),
        ]);
    }

    public function specialities(): View
    {
        return view('doctor.dashboard.services.specialities_services', [
            'groups' => $this->services->forDoctor(Auth::user()),
            'specialities' => Speciality::orderBy('name')->get(),
            'servicesBySpeciality' => Service::orderBy('name')->get()->groupBy('speciality_id'),
        ]);
    }

    public function changePassword(): View
    {
        return view('doctor.dashboard.profile.change_password');
    }

    public function updatePassword(UpdateDoctorPasswordRequest $request): RedirectResponse
    {
        $this->doctorProfile->updatePassword(Auth::user(), $request->validated('password'));

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Your password has been updated. Please log in again.');
    }

    public function appointments(): View
    {
        $appointments = Appointment::query()
            ->where('doctor_id', Auth::id())
            ->whereIn('status', [AppointmentStatus::Confirmed, AppointmentStatus::Cancelled, AppointmentStatus::Completed])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        return view('doctor.dashboard.appointments.doctor_appointments', [
            'upcomingAppointments' => $appointments->where('status', AppointmentStatus::Confirmed)->values(),
            'cancelledAppointments' => $appointments->where('status', AppointmentStatus::Cancelled)->sortByDesc('appointment_date')->values(),
            'completedAppointments' => $appointments->where('status', AppointmentStatus::Completed)->sortByDesc('appointment_date')->values(),
        ]);
    }

    public function patients(): View
    {
        return view('doctor.dashboard.appointments.doctor_patients', [
            'patients' => $this->doctorPatients->forDoctor(Auth::user()),
        ]);
    }

    public function patientDetails(User $patient): View
    {
        abort_if($patient->role !== 'patient', 404);
        abort_unless($this->doctorPatients->isTreatingPatient(Auth::user(), $patient), 403);

        $appointments = Appointment::query()
            ->where('patient_id', $patient->id)
            ->with('doctor')
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->get();

        $medicalRecords = $patient->medicalRecords()->orderByDesc('record_date')->get();

        $prescriptions = Prescription::query()
            ->where('patient_id', $patient->id)
            ->with(['doctor', 'items'])
            ->orderByDesc('issued_at')
            ->get();

        return view('doctor.dashboard.appointments.patient_details', [
            'patient' => $patient,
            'appointments' => $appointments,
            'medicalRecords' => $medicalRecords,
            'prescriptions' => $prescriptions,
            'lastBooking' => $appointments->max('appointment_date'),
        ]);
    }

    public function invoices(): View
    {
        $invoices = Auth::user()
            ->doctorInvoices()
            ->with(['patient', 'appointment.services', 'appointment.payment'])
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('doctor.dashboard.invoices.doctor_invoices', [
            'invoices' => $invoices,
        ]);
    }
}
