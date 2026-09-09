@extends('admin.admin_master')
@section('admin')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">List of Doctors</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Doctor</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
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
                                        <th>Member Since</th>
                                        <th>Earned</th>
                                        <th>Availability</th>
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
                                            $isAvailable = $doctor->availability_status === 'available';
                                        @endphp
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $doctor->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="{{ $doctorName }}"></span>
                                                    Dr {{ $doctorName }}
                                                </h2>
                                            </td>
                                            <td>{{ $specialities ?: '-' }}</td>
                                            <td>{{ $doctor->created_at->format('j M Y') }} <br><small>{{ $doctor->created_at->format('h.i A') }}</small></td>
                                            <td>${{ number_format((float) ($doctor->earned ?? 0), 2) }}</td>
                                            <td>
                                                <span class="badge {{ $isAvailable ? 'bg-success' : 'bg-secondary' }}">{{ $isAvailable ? 'Available' : 'Not Available' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $doctors->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
