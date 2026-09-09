<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(): View
    {
        $patients = User::query()
            ->where('role', 'patient')
            ->withMax('patientAppointments', 'appointment_date')
            ->withSum(['patientPayments as paid' => fn ($query) => $query->where('payment_status', PaymentStatus::Paid)], 'amount')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.patients.index', [
            'patients' => $patients,
        ]);
    }
}
