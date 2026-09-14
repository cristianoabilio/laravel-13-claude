<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeReasonRequest;
use App\Http\Requests\Admin\UpdateHomeReasonRequest;
use App\Http\Requests\Admin\UpdateHomeReasonSectionRequest;
use App\Models\HomeReason;
use App\Services\HomeReasonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeReasonController extends Controller
{
    public function __construct(protected HomeReasonService $reasons) {}

    public function index(): View
    {
        return view('admin.home.reasons.index', [
            'section' => $this->reasons->section(),
            'homeReasons' => $this->reasons->list(),
        ]);
    }

    public function updateSection(UpdateHomeReasonSectionRequest $request): RedirectResponse
    {
        $this->reasons->updateSection($request->validated());

        return back()->with('success', 'Section updated successfully.');
    }

    public function store(StoreHomeReasonRequest $request): RedirectResponse
    {
        $this->reasons->create($request->validated());

        return back()->with('success', 'Reason added successfully.');
    }

    public function update(UpdateHomeReasonRequest $request, HomeReason $reason): RedirectResponse
    {
        $this->reasons->update($reason, $request->validated());

        return back()->with('success', 'Reason updated successfully.');
    }

    public function destroy(HomeReason $reason): RedirectResponse
    {
        $this->reasons->delete($reason);

        return back()->with('success', 'Reason deleted successfully.');
    }
}
