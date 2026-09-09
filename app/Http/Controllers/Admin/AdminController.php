<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $revenueByYear = Payment::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->whereNotNull('paid_at')
            ->selectRaw('YEAR(paid_at) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $usersByYear = User::query()
            ->whereIn('role', ['doctor', 'patient'])
            ->selectRaw('YEAR(created_at) as year, role, COUNT(*) as total')
            ->groupBy('year', 'role')
            ->orderBy('year')
            ->get()
            ->groupBy('year');

        $revenueChartData = $revenueByYear
            ->map(fn ($row) => [
                'year' => (string) $row->year,
                'revenue' => (float) $row->total,
            ])
            ->values();

        $statusChartData = $usersByYear
            ->map(fn ($rows, $year) => [
                'year' => (string) $year,
                'doctors' => (float) ($rows->firstWhere('role', 'doctor')->total ?? 0),
                'patients' => (float) ($rows->firstWhere('role', 'patient')->total ?? 0),
            ])
            ->values();

        $doctors = User::query()
            ->where('role', 'doctor')
            ->with('doctorServices.service.speciality')
            ->withSum(['doctorPayments as earned' => fn ($query) => $query->where('payment_status', PaymentStatus::Paid)], 'amount')
            ->withAvg('doctorReviews as average_rating', 'rating')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $patients = User::query()
            ->where('role', 'patient')
            ->withMax('patientAppointments', 'appointment_date')
            ->withSum(['patientPayments as paid' => fn ($query) => $query->where('payment_status', PaymentStatus::Paid)], 'amount')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $appointments = Appointment::query()
            ->with(['doctor', 'patient', 'doctorService.service.speciality'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('admin.index', [
            'doctorsCount' => User::where('role', 'doctor')->count(),
            'patientsCount' => User::where('role', 'patient')->count(),
            'appointmentsCount' => Appointment::count(),
            'totalRevenue' => (float) Payment::where('payment_status', PaymentStatus::Paid)->sum('amount'),
            'revenueChartData' => $revenueChartData,
            'statusChartData' => $statusChartData,
            'doctors' => $doctors,
            'patients' => $patients,
            'appointments' => $appointments,
        ]);
    }
}
