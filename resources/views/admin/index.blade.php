@extends('admin.admin_master')
@section('admin')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Welcome Admin!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary border-primary">
                            <i class="fe fe-users"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $doctorsCount }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Doctors</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-success">
                            <i class="fe fe-credit-card"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $patientsCount }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">

                        <h6 class="text-muted">Patients</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-danger border-danger">
                            <i class="fe fe-money"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $appointmentsCount }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">

                        <h6 class="text-muted">Appointment</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-warning border-warning">
                            <i class="fe fe-folder"></i>
                        </span>
                        <div class="dash-count">
                            <h3>${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">

                        <h6 class="text-muted">Revenue</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6">

            <!-- Sales Chart -->
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title">Revenue</h4>
                </div>
                <div class="card-body">
                    @if ($revenueChartData->isEmpty())
                        <p class="text-center mb-0 py-4">No paid revenue recorded yet.</p>
                    @else
                        <div id="morrisArea"></div>
                    @endif
                </div>
            </div>
            <!-- /Sales Chart -->

        </div>
        <div class="col-md-12 col-lg-6">

            <!-- Invoice Chart -->
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title">Status</h4>
                </div>
                <div class="card-body">
                    @if ($statusChartData->isEmpty())
                        <p class="text-center mb-0 py-4">No doctor or patient sign-ups recorded yet.</p>
                    @else
                        <div id="morrisLine"></div>
                    @endif
                </div>
            </div>
            <!-- /Invoice Chart -->

        </div>
    </div>
    <div class="row">
        <div class="col-md-6 d-flex">

            <!-- Recent Doctors -->
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title">Doctors List</h4>
                </div>
                <div class="card-body">
                    @if ($doctors->isEmpty())
                        <p class="text-center mb-0 py-4">No doctors have registered yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Doctor Name</th>
                                        <th>Speciality</th>
                                        <th>Earned</th>
                                        <th>Reviews</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($doctors as $doctor)
                                        @php
                                            $doctorName = $doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name);
                                            $specialities = $doctor->doctorServices
                                                ->pluck('service.speciality.name')
                                                ->filter()
                                                ->unique()
                                                ->implode(', ');
                                            $averageRating = round((float) ($doctor->average_rating ?? 0));
                                        @endphp
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $doctor->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="{{ $doctorName }}"></span>
                                                    Dr {{ $doctorName }}
                                                </h2>
                                            </td>
                                            <td>{{ $specialities ?: '-' }}</td>
                                            <td>${{ number_format((float) ($doctor->earned ?? 0), 2) }}</td>
                                            <td>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fe {{ $i <= $averageRating ? 'fe-star text-warning' : 'fe-star-o text-secondary' }}"></i>
                                                @endfor
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Recent Doctors -->

        </div>
        <div class="col-md-6 d-flex">

            <!-- Recent Patients -->
            <div class="card  card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title">Patients List</h4>
                </div>
                <div class="card-body">
                    @if ($patients->isEmpty())
                        <p class="text-center mb-0 py-4">No patients have registered yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Phone</th>
                                        <th>Last Visit</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patients as $patient)
                                        @php
                                            $patientName = $patient->display_name ?: trim($patient->first_name.' '.$patient->last_name);
                                        @endphp
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $patient->profile_photo_url ?: asset('backend/assets/img/patients/patient1.jpg') }}" alt="{{ $patientName }}"></span>
                                                    {{ $patientName }}
                                                </h2>
                                            </td>
                                            <td>{{ $patient->phone ?: '-' }}</td>
                                            <td>{{ $patient->patient_appointments_max_appointment_date ? \Illuminate\Support\Carbon::parse($patient->patient_appointments_max_appointment_date)->format('d M Y') : '-' }}</td>
                                            <td>${{ number_format((float) ($patient->paid ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Recent Patients -->

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <!-- Recent Appointments -->
            <div class="card card-table">
                <div class="card-header">
                    <h4 class="card-title">Appointment List</h4>
                </div>
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
                                        <th>Apointment Time</th>
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
                    @endif
                </div>
            </div>
            <!-- /Recent Appointments -->

        </div>
    </div>

</div>

@if ($revenueChartData->isNotEmpty() || $statusChartData->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Morris === 'undefined') {
                return;
            }

            @if ($revenueChartData->isNotEmpty())
                window.mA = Morris.Area({
                    element: 'morrisArea',
                    data: @json($revenueChartData),
                    xkey: 'year',
                    ykeys: ['revenue'],
                    labels: ['Revenue'],
                    lineColors: ['#1b5a90'],
                    lineWidth: 2,
                    fillOpacity: 0.5,
                    gridTextSize: 10,
                    hideHover: 'auto',
                    resize: true,
                    redraw: true,
                });
            @endif

            @if ($statusChartData->isNotEmpty())
                window.mL = Morris.Line({
                    element: 'morrisLine',
                    data: @json($statusChartData),
                    xkey: 'year',
                    ykeys: ['doctors', 'patients'],
                    labels: ['Doctors', 'Patients'],
                    lineColors: ['#1b5a90', '#ff9d00'],
                    lineWidth: 1,
                    gridTextSize: 10,
                    hideHover: 'auto',
                    resize: true,
                    redraw: true,
                });
            @endif

            window.addEventListener('resize', function () {
                if (window.mA) { window.mA.redraw(); }
                if (window.mL) { window.mL.redraw(); }
            });
        });
    </script>
@endif
@endsection
