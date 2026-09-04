<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $doctorCountsBySpeciality = DB::table('doctor_services')
            ->join('services', 'services.id', '=', 'doctor_services.service_id')
            ->select('services.speciality_id', DB::raw('COUNT(DISTINCT doctor_services.doctor_id) as doctors_count'))
            ->groupBy('services.speciality_id')
            ->pluck('doctors_count', 'services.speciality_id');

        return view('frontend.index', [
            'specialities' => Speciality::query()
                ->orderBy('name')
                ->get()
                ->map(function (Speciality $speciality) use ($doctorCountsBySpeciality) {
                    $speciality->doctors_count = $doctorCountsBySpeciality->get($speciality->id, 0);

                    return $speciality;
                }),
            'featuredDoctors' => User::query()
                ->where('role', 'doctor')
                ->with([
                    'clinics' => fn ($query) => $query->latest()->limit(1),
                    'doctorServices' => fn ($query) => $query->orderBy('price')->limit(1),
                ])
                ->orderByRaw("availability_status = 'available' desc")
                ->latest()
                ->take(8)
                ->get(),
            'favoritedDoctorIds' => $user?->role === 'patient'
                ? $user->favoriteDoctors()->pluck('users.id')->all()
                : [],
        ]);
    }
}
