@extends('doctor.doctor_master')
@section('doctor')
    <div class="dashboard-header">
        <h3>Appointments</h3>
    </div>
    <div class="appointment-tab-head">
        <div class="appointment-tabs">
            <ul class="nav nav-pills inner-tab " id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-upcoming-tab" data-bs-toggle="pill" data-bs-target="#pills-upcoming" type="button" role="tab" aria-controls="pills-upcoming" aria-selected="true">Upcoming<span>{{ $upcomingAppointments->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-cancel-tab" data-bs-toggle="pill" data-bs-target="#pills-cancel" type="button" role="tab" aria-controls="pills-cancel" aria-selected="false">Cancelled<span>{{ $cancelledAppointments->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-complete-tab" data-bs-toggle="pill" data-bs-target="#pills-complete" type="button" role="tab" aria-controls="pills-complete" aria-selected="false">Completed<span>{{ $completedAppointments->count() }}</span></button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content appointment-tab-content">
        <div class="tab-pane fade show active" id="pills-upcoming" role="tabpanel" aria-labelledby="pills-upcoming-tab">
            @forelse ($upcomingAppointments as $appointment)
                @include('doctor.dashboard.appointments.partials.appointment-card', ['appointment' => $appointment, 'showStartNow' => true])
            @empty
                <div class="appointment-wrap">
                    <p class="text-center mb-0 py-4">You don't have any upcoming appointments.</p>
                </div>
            @endforelse
        </div>
        <div class="tab-pane fade" id="pills-cancel" role="tabpanel" aria-labelledby="pills-cancel-tab">
            @forelse ($cancelledAppointments as $appointment)
                @include('doctor.dashboard.appointments.partials.appointment-card', ['appointment' => $appointment, 'showStartNow' => false])
            @empty
                <div class="appointment-wrap">
                    <p class="text-center mb-0 py-4">You don't have any cancelled appointments.</p>
                </div>
            @endforelse
        </div>
        <div class="tab-pane fade" id="pills-complete" role="tabpanel" aria-labelledby="pills-complete-tab">
            @forelse ($completedAppointments as $appointment)
                @include('doctor.dashboard.appointments.partials.appointment-card', ['appointment' => $appointment, 'showStartNow' => false])
            @empty
                <div class="appointment-wrap">
                    <p class="text-center mb-0 py-4">You don't have any completed appointments.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Start Appointment Modal -->
    <div class="modal fade" id="start_appointment" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Start Appointment</h4>
                        <p class="mb-4">Are you sure you want to start this appointment? It will be marked as completed.</p>
                        <button type="button" class="btn btn-primary" id="confirm-start-btn">Start Now</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Start Appointment Modal -->
@endsection
