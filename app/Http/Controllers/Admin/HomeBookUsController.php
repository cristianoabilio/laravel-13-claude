<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeBookUsFaqRequest;
use App\Http\Requests\Admin\UpdateHomeBookUsFaqRequest;
use App\Http\Requests\Admin\UpdateHomeBookUsSectionRequest;
use App\Models\HomeBookUsFaq;
use App\Services\HomeBookUsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeBookUsController extends Controller
{
    public function __construct(protected HomeBookUsService $bookUs) {}

    public function index(): View
    {
        return view('admin.home.bookus.index', [
            'section' => $this->bookUs->section(),
            'homeBookUsFaqs' => $this->bookUs->faqs(),
        ]);
    }

    public function updateSection(UpdateHomeBookUsSectionRequest $request): RedirectResponse
    {
        $this->bookUs->updateSection(
            $request->safe()->except(['image_one', 'image_two', 'image_three']),
            [
                'image_one' => $request->file('image_one'),
                'image_two' => $request->file('image_two'),
                'image_three' => $request->file('image_three'),
            ]
        );

        return back()->with('success', 'Section updated successfully.');
    }

    public function storeFaq(StoreHomeBookUsFaqRequest $request): RedirectResponse
    {
        $this->bookUs->createFaq($request->validated());

        return back()->with('success', 'FAQ added successfully.');
    }

    public function updateFaq(UpdateHomeBookUsFaqRequest $request, HomeBookUsFaq $faq): RedirectResponse
    {
        $this->bookUs->updateFaq($faq, $request->validated());

        return back()->with('success', 'FAQ updated successfully.');
    }

    public function destroyFaq(HomeBookUsFaq $faq): RedirectResponse
    {
        $this->bookUs->deleteFaq($faq);

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
