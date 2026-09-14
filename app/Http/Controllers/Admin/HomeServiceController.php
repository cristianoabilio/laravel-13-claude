<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeServiceRequest;
use App\Http\Requests\Admin\UpdateHomeServiceRequest;
use App\Models\HomeService;
use App\Services\HomeServiceManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeServiceController extends Controller
{
    public function __construct(protected HomeServiceManager $homeServices) {}

    public function index(): View
    {
        return view('admin.home.services.index', [
            'homeServices' => $this->homeServices->list(),
        ]);
    }

    public function store(StoreHomeServiceRequest $request): RedirectResponse
    {
        $this->homeServices->create($request->validated());

        return back()->with('success', 'Service added successfully.');
    }

    public function update(UpdateHomeServiceRequest $request, HomeService $service): RedirectResponse
    {
        $this->homeServices->update($service, $request->validated());

        return back()->with('success', 'Service updated successfully.');
    }

    public function destroy(HomeService $service): RedirectResponse
    {
        $this->homeServices->delete($service);

        return back()->with('success', 'Service deleted successfully.');
    }
}
