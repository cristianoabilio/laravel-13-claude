<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreReviewRequest;
use App\Models\User;
use App\Services\Frontend\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviews) {}

    public function store(StoreReviewRequest $request, int $doctorId): RedirectResponse
    {
        $doctor = User::where('role', 'doctor')->findOrFail($doctorId);

        $this->reviews->create(Auth::user(), $doctor, $request->validated());

        return back()->with('success', 'Thank you for your review!');
    }
}
