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

<!-- Accept Appointment Modal -->
<div class="modal fade" id="accept_appointment" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-content p-2">
                    <h4 class="modal-title">Accept Appointment</h4>
                    <p class="mb-4">Are you sure you want to accept this appointment request?</p>
                    <button type="button" class="btn btn-primary" id="confirm-accept-btn">Accept</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Accept Appointment Modal -->

<!-- Reject Appointment Modal -->
<div class="modal fade" id="cancel_appointment" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-content p-2">
                    <h4 class="modal-title">Reject Appointment</h4>
                    <p class="mb-4">Are you sure you want to reject this appointment request?</p>
                    <button type="button" class="btn btn-primary" id="confirm-reject-btn">Reject</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Reject Appointment Modal -->
@endsection
