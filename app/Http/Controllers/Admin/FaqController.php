<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use App\Services\FaqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(protected FaqService $faqs) {}

    public function index(): View
    {
        return view('admin.faqs.index', [
            'faqs' => $this->faqs->list(),
        ]);
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        $this->faqs->create($request->validated());

        return back()->with('success', 'FAQ added successfully.');
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $this->faqs->update($faq, $request->validated());

        return back()->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $this->faqs->delete($faq);

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
