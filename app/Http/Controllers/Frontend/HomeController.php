<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('frontend.index', [
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
