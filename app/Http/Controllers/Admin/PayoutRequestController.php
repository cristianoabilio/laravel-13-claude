<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Services\Doctor\DoctorAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayoutRequestController extends Controller
{
    public function __construct(protected DoctorAccountService $accounts) {}

    public function index(): View
    {
        $payoutRequests = PayoutRequest::query()
            ->with(['doctor', 'processedBy'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.payout_requests.index', [
            'payoutRequests' => $payoutRequests,
        ]);
    }

    public function approve(PayoutRequest $payoutRequest): RedirectResponse
    {
        $this->accounts->approve($payoutRequest, Auth::guard('admin')->user());

        return back()->with('success', 'Payout request approved.');
    }

    public function cancel(PayoutRequest $payoutRequest): RedirectResponse
    {
        $this->accounts->cancel($payoutRequest, Auth::guard('admin')->user());

        return back()->with('success', 'Payout request cancelled.');
    }
}
