<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeBannerRequest;
use App\Services\HomeBannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeBannerController extends Controller
{
    public function __construct(protected HomeBannerService $banners) {}

    public function edit(): View
    {
        return view('admin.home.banner', [
            'banner' => $this->banners->current(),
        ]);
    }

    public function update(UpdateHomeBannerRequest $request): RedirectResponse
    {
        $this->banners->update($request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Banner updated successfully.');
    }
}
