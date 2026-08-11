<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateExperiencesRequest;
use App\Models\Experience;
use App\Services\Doctor\DoctorExperienceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DoctorExperienceController extends Controller
{
    public function __construct(protected DoctorExperienceService $experiences) {}

    public function update(UpdateExperiencesRequest $request): RedirectResponse
    {
        $this->experiences->upsert(Auth::user(), $request->validated('experiences', []));

        return back()->with('success', 'Experience saved successfully.');
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        abort_if($experience->doctor_id !== Auth::id(), 403);

        $this->experiences->delete($experience);

        return back()->with('success', 'Experience deleted successfully.');
    }

    public function destroyLogo(Experience $experience): RedirectResponse
    {
        abort_if($experience->doctor_id !== Auth::id(), 403);

        $this->experiences->removeLogo($experience);

        return back()->with('success', 'Hospital logo removed successfully.');
    }
}
