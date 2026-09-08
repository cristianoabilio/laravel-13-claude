@extends('admin.admin_master')
@section('admin')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Appointments</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Appointments</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div class="row">
        <div class="col-md-12">

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-body">
                    @if ($appointments->isEmpty())
                        <p class="text-center mb-0 py-4">No appointments have been booked yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Doctor Name</th>
                                        <th>Speciality</th>
                                        <th>Patient Name</th>
                                        <th>Appointment Time</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'bg-warning text-dark',
                                            'confirmed' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                        ];
                                    @endphp
                                    @foreach ($appointments as $appointment)
                                        @php
                                            $doctorName = $appointment->doctor->display_name ?: trim($appointment->doctor->first_name.' '.$appointment->doctor->last_name);
                                            $patientName = trim($appointment->first_name.' '.$appointment->last_name);
                                            $speciality = $appointment->doctorService?->service?->speciality?->name;
                                        @endphp
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $appointment->doctor->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="{{ $doctorName }}"></span>
                                                    Dr {{ $doctorName }}
                                                </h2>
                                            </td>
                                            <td>{{ $speciality ?: '-' }}</td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $appointment->patient->profile_photo_url ?: asset('backend/assets/img/patients/patient1.jpg') }}" alt="{{ $patientName }}"></span>
                                                    {{ $patientName }}
                                                </h2>
                                            </td>
                                            <td>
                                                {{ $appointment->appointment_date->format('j M Y') }}
                                                <span class="text-primary d-block">{{ $appointment->start_time->format('h.i A') }} - {{ $appointment->end_time->format('h.i A') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusBadges[$appointment->status->value] }}">{{ $appointment->status->label() }}</span>
                                            </td>
                                            <td>
                                                ${{ number_format((float) $appointment->total_amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $appointments->links() }}
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Recent Orders -->

        </div>
    </div>
</div>
@endsection
