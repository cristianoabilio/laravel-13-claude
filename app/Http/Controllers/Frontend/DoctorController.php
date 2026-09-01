<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Frontend\DoctorProfileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(
        protected DoctorProfileService $doctorProfile,
    ) {}

    public function details(int $doctorId): View
    {
        $doctor = User::query()
            ->where('role', 'doctor')
            ->with([
                'experiences',
                'educations',
                'memberships',
                'clinics.images',
                'businessHours',
            ])
            ->findOrFail($doctorId);

        $user = Auth::user();

        return view('frontend.doctor_details', [
            'doctor' => $doctor,
            ...$this->doctorProfile->present($doctor),
            'isFavorited' => $user?->role === 'patient'
                && $user->favoriteDoctors()->where('users.id', $doctor->id)->exists(),
        ]);
    }

    public function booking(int $doctorId): View
    {
        $doctor = User::query()
            ->where('role', 'doctor')
            ->with(['clinics', 'businessHours'])
            ->findOrFail($doctorId);

        $presented = $this->doctorProfile->present($doctor);

        return view('frontend.doctor_booking', [
            'doctor' => $doctor,
            ...$presented,
            'closedWeekdays' => $presented['businessHours']
                ->filter(fn ($hour) => ! $hour->is_open)
                ->map(fn ($hour) => array_search($hour->day->value, [
                    'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday',
                ]))
                ->values(),
        ]);
    }
}
