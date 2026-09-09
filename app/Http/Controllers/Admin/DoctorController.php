<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = User::query()
            ->where('role', 'doctor')
            ->with('doctorServices.service.speciality')
            ->withSum(['doctorPayments as earned' => fn ($query) => $query->where('payment_status', PaymentStatus::Paid)], 'amount')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.doctors.index', [
            'doctors' => $doctors,
        ]);
    }
}
