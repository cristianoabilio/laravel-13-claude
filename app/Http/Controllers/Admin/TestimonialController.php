<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Http\Requests\Admin\UpdateTestimonialRequest;
use App\Models\Testimonial;
use App\Services\TestimonialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function __construct(protected TestimonialService $testimonials) {}

    public function index(): View
    {
        return view('admin.testimonials.index', [
            'testimonials' => $this->testimonials->list(),
        ]);
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $this->testimonials->create($request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Testimonial added successfully.');
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $this->testimonials->update($testimonial, $request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $this->testimonials->delete($testimonial);

        return back()->with('success', 'Testimonial deleted successfully.');
    }
}
