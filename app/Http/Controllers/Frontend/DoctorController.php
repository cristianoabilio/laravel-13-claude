<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DoctorService;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use App\Services\Frontend\DoctorProfileService;
use App\Services\Frontend\DoctorSpecialityFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(
        protected DoctorProfileService $doctorProfile,
        protected DoctorSpecialityFilterService $specialityFilter,
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

    public function speciality(Speciality $speciality, Request $request): View
    {
        $services = Service::query()
            ->where('speciality_id', $speciality->id)
            ->withCount('doctorServices')
            ->orderBy('name')
            ->get();

        $priceBounds = DoctorService::query()
            ->whereHas('service', fn ($q) => $q->where('speciality_id', $speciality->id))
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $user = Auth::user();

        return view('frontend.doctor_speciality', [
            'speciality' => $speciality,
            'doctors' => $this->specialityFilter->search($speciality, $request),
            'services' => $services,
            'minPrice' => $priceBounds?->min_price !== null ? (float) $priceBounds->min_price : 0,
            'maxPrice' => $priceBounds?->max_price !== null ? (float) $priceBounds->max_price : 0,
            'favoritedDoctorIds' => $user?->role === 'patient'
                ? $user->favoriteDoctors()->pluck('users.id')->all()
                : [],
        ]);
    }
}
