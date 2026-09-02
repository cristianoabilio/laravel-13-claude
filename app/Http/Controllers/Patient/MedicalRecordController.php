<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreMedicalRecordRequest;
use App\Http\Requests\Patient\UpdateMedicalRecordRequest;
use App\Models\MedicalRecord;
use App\Services\Patient\MedicalRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function __construct(protected MedicalRecordService $medicalRecords) {}

    public function store(StoreMedicalRecordRequest $request): RedirectResponse
    {
        $this->medicalRecords->create(Auth::user(), $request->safe()->except('file'), $request->file('file'));

        return back()->with('success', 'Medical record added successfully.');
    }

    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        abort_if($medicalRecord->patient_id !== Auth::id(), 403);

        $this->medicalRecords->update($medicalRecord, $request->safe()->except('file'), $request->file('file'));

        return back()->with('success', 'Medical record updated successfully.');
    }

    public function destroy(MedicalRecord $medicalRecord): RedirectResponse
    {
        abort_if($medicalRecord->patient_id !== Auth::id(), 403);

        $this->medicalRecords->delete($medicalRecord);

        return back()->with('success', 'Medical record deleted successfully.');
    }
}
