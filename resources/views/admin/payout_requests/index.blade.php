@extends('admin.admin_master')
@section('admin')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Payout Requests</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payout Requests</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if ($payoutRequests->isEmpty())
                        <p class="text-center mb-0 py-4">No payout requests have been submitted yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Doctor</th>
                                        <th>Requested Date</th>
                                        <th>Account No</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Processed By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                        ];
                                    @endphp
                                    @foreach ($payoutRequests as $payoutRequest)
                                        @php
                                            $doctorName = $payoutRequest->doctor->display_name ?: trim($payoutRequest->doctor->first_name.' '.$payoutRequest->doctor->last_name);
                                        @endphp
                                        <tr>
                                            <td>#PR-{{ str_pad($payoutRequest->id, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2">
                                                        <img class="avatar-img rounded-circle" src="{{ $payoutRequest->doctor->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg') }}" alt="{{ $doctorName }}">
                                                    </span>
                                                    Dr {{ $doctorName }}
                                                </h2>
                                            </td>
                                            <td>{{ $payoutRequest->created_at->format('d M Y') }}</td>
                                            <td>
                                                {{ $payoutRequest->bank_name }}<br>
                                                <small class="text-muted">{{ $payoutRequest->account_number }}</small>
                                            </td>
                                            <td>${{ number_format((float) $payoutRequest->amount, 2) }}</td>
                                            <td><span class="badge {{ $statusBadges[$payoutRequest->status->value] }}">{{ $payoutRequest->status->label() }}</span></td>
                                            <td>
                                                @if ($payoutRequest->processedBy)
                                                    {{ trim($payoutRequest->processedBy->first_name.' '.$payoutRequest->processedBy->last_name) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($payoutRequest->status === \App\Enums\PayoutRequestStatus::Pending)
                                                    <div class="actions">
                                                        <form action="{{ route('admin.payout_requests.approve', $payoutRequest) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm bg-success-light">
                                                                <i class="fe fe-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.payout_requests.cancel', $payoutRequest) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm bg-danger-light">
                                                                <i class="fe fe-x"></i> Cancel
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ $payoutRequest->processed_at?->format('d M Y') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $payoutRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
