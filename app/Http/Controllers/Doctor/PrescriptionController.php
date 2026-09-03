<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StorePrescriptionRequest;
use App\Models\User;
use App\Services\Doctor\DoctorPatientService;
use App\Services\Doctor\PrescriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function __construct(
        protected PrescriptionService $prescriptions,
        protected DoctorPatientService $doctorPatients,
    ) {}

    public function store(StorePrescriptionRequest $request, User $patient): RedirectResponse
    {
        abort_if($patient->role !== 'patient', 404);
        abort_unless($this->doctorPatients->isTreatingPatient(Auth::user(), $patient), 403);

        $this->prescriptions->create(Auth::user(), $patient, $request->validated());

        return back()->with('success', 'Prescription added successfully.');
    }
}
