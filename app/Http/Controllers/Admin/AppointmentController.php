<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::query()
            ->with(['doctor', 'patient', 'doctorService.service.speciality'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(15);

        return view('admin.appointments.index', [
            'appointments' => $appointments,
        ]);
    }
}
