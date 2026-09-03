@extends('doctor.doctor_master')
@section('doctor')

<div class="dashboard-header">
    <h3>My Patients</h3>
</div>
<div class="appointment-tab-head">
    <div class="appointment-tabs">
        <ul class="nav nav-pills inner-tab " id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-upcoming-tab" data-bs-toggle="pill" data-bs-target="#pills-upcoming" type="button" role="tab" aria-controls="pills-upcoming" aria-selected="true">Active<span>{{ $patients->count() }}</span></button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content appointment-tab-content grid-patient">
    <div class="tab-pane fade show active" id="pills-upcoming" role="tabpanel" aria-labelledby="pills-upcoming-tab">
        @if ($patients->isEmpty())
            <p class="text-center mb-0 py-4">You don't have any patients yet.</p>
        @else
            <div class="row">
                @foreach ($patients as $patient)
                    @php
                        $age = $patient->date_of_birth?->age;
                        $location = collect([$patient->city, $patient->country])->filter()->implode(', ');
                    @endphp
                    <!-- Appointment Grid -->
                    <div class="col-xl-4 col-lg-6 col-md-6 d-flex">
                        <div class="appointment-wrap appointment-grid-wrap">
                            <ul>
                                <li>
                                    <div class="appointment-grid-head">
                                        <div class="patinet-information">
                                            <a href="{{ route('patient.details', $patient) }}">
                                                <img src="{{ $patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $patient->display_name ?: trim($patient->first_name.' '.$patient->last_name) }}">
                                            </a>
                                            <div class="patient-info">
                                                <p>#P{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</p>
                                                <h6><a href="{{ route('patient.details', $patient) }}">{{ $patient->display_name ?: trim($patient->first_name.' '.$patient->last_name) }}</a></h6>
                                                <ul>
                                                    @if ($age !== null)
                                                        <li>Age : {{ $age }}</li>
                                                    @endif
                                                    @if ($patient->gender)
                                                        <li>{{ ucfirst($patient->gender) }}</li>
                                                    @endif
                                                    @if ($patient->blood_group)
                                                        <li>{{ $patient->blood_group }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="appointment-info">
                                    @if ($location)
                                        <p class="mb-0"><i class="isax isax-location5"></i>{{ $location }}</p>
                                    @endif
                                </li>
                                <li class="appointment-action">
                                    <div class="patient-book">
                                        <p><i class="isax isax-calendar-1"></i>Last Booking <span>{{ $patient->last_booking_date ? \Illuminate\Support\Carbon::parse($patient->last_booking_date)->format('d M Y') : '-' }}</span></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /Appointment Grid -->
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
