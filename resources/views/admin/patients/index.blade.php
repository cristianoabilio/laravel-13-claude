@extends('admin.admin_master')
@section('admin')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">List of Patient</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Patient</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if ($patients->isEmpty())
                        <p class="text-center mb-0 py-4">No patients have registered yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient ID</th>
                                        <th>Patient Name</th>
                                        <th>Age</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Last Visit</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patients as $patient)
                                        @php
                                            $patientName = $patient->display_name ?: trim($patient->first_name.' '.$patient->last_name);
                                            $age = $patient->date_of_birth?->age;
                                            $address = collect([$patient->address, $patient->city, $patient->state, $patient->pincode])->filter()->implode(', ');
                                        @endphp
                                        <tr>
                                            <td>#PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $patient->profile_photo_url ?: asset('backend/assets/img/patients/patient1.jpg') }}" alt="{{ $patientName }}"></span>
                                                    {{ $patientName }}
                                                </h2>
                                            </td>
                                            <td>{{ $age ?? '-' }}</td>
                                            <td>{{ $address ?: '-' }}</td>
                                            <td>{{ $patient->phone ?: '-' }}</td>
                                            <td>{{ $patient->patient_appointments_max_appointment_date ? \Illuminate\Support\Carbon::parse($patient->patient_appointments_max_appointment_date)->format('d M Y') : '-' }}</td>
                                            <td>${{ number_format((float) ($patient->paid ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $patients->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
