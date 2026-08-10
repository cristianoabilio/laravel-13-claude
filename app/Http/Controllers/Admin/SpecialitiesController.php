<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSpecialityRequest;
use App\Http\Requests\Admin\UpdateSpecialityRequest;
use App\Models\Speciality;
use App\Services\SpecialityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SpecialitiesController extends Controller
{
    public function __construct(protected SpecialityService $specialities) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.specialities.index', [
            'specialities' => $this->specialities->list(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecialityRequest $request): RedirectResponse
    {
        $this->specialities->create($request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Speciality added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecialityRequest $request, Speciality $speciality): RedirectResponse
    {
        $this->specialities->update($speciality, $request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Speciality updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speciality $speciality): RedirectResponse
    {
        $this->specialities->delete($speciality);

        return back()->with('success', 'Speciality deleted successfully.');
    }
}
