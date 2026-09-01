@extends('doctor.doctor_master')
@section('doctor')
<div class="dashboard-header">
    <h3>Requests</h3>
    <ul>
        <li>
            <div class="dropdown header-dropdown">
                <a class="dropdown-toggle nav-tog" data-bs-toggle="dropdown" href="javascript:void(0);">
                    Last 7 Days
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="javascript:void(0);" class="dropdown-item">
                        Today
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        This Month
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        Last 7 Days
                    </a>
                </div>
            </div>
        </li>
    </ul>
</div>

<!-- Request List -->
@forelse ($appointments as $appointment)
    @include('doctor.dashboard.requests.partials.request-card', ['appointment' => $appointment])
@empty
    <div class="appointment-wrap">
        <p class="text-center mb-0 py-4">You don't have any pending appointment requests.</p>
    </div>
@endforelse
<!-- /Request List -->
@endsection
