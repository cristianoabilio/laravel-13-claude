<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateEducationsRequest;
use App\Models\Education;
use App\Services\Doctor\DoctorEducationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DoctorEducationController extends Controller
{
    public function __construct(protected DoctorEducationService $educations) {}

    public function update(UpdateEducationsRequest $request): RedirectResponse
    {
        $this->educations->upsert(Auth::user(), $request->validated('educations', []));

        return back()->with('success', 'Education saved successfully.');
    }

    public function destroy(Education $education): RedirectResponse
    {
        abort_if($education->doctor_id !== Auth::id(), 403);

        $this->educations->delete($education);

        return back()->with('success', 'Education deleted successfully.');
    }

    public function destroyLogo(Education $education): RedirectResponse
    {
        abort_if($education->doctor_id !== Auth::id(), 403);

        $this->educations->removeLogo($education);

        return back()->with('success', 'Logo removed successfully.');
    }
}
