@extends('patient.patient_master')
@section('patient')

<div class="col-lg-8 col-xl-9">
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
            </ul>
        </div>
    </div>

    <div class="tab-content appointment-tab-content">
        <div class="tab-pane fade show active" id="pills-upcoming" role="tabpanel" aria-labelledby="pills-upcoming-tab">
            <!-- Appointment List -->
            @forelse ($upcomingAppointments as $appointment)
                @include('patient.dashboard.appointments.partials.appointment-card', ['appointment' => $appointment, 'cancelled' => false])
            @empty
                <div class="appointment-wrap">
                    <p class="text-center mb-0 py-4">You don't have any upcoming appointments yet.</p>
                </div>
            @endforelse
            <!-- /Appointment List -->
        </div>
        <div class="tab-pane fade" id="pills-cancel" role="tabpanel" aria-labelledby="pills-cancel-tab">
            <!-- Appointment List -->
            @forelse ($cancelledAppointments as $appointment)
                @include('patient.dashboard.appointments.partials.appointment-card', ['appointment' => $appointment, 'cancelled' => true])
            @empty
                <div class="appointment-wrap">
                    <p class="text-center mb-0 py-4">You don't have any cancelled appointments.</p>
                </div>
            @endforelse
            <!-- /Appointment List -->
        </div>
    </div>

</div>

@endsection
