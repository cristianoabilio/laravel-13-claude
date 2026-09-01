@extends('patient.patient_master')
@section('patient')

<div class="col-lg-8 col-xl-9">

    <div class="dashboard-header">
        <h3>Favourites</h3>
        <ul class="header-list-btns">
            <li>
                <div class="input-block dash-search-input">
                    <input type="text" class="form-control" placeholder="Search">
                    <span class="search-icon"><i class="isax isax-search-normal"></i></span>
                </div>
            </li>
        </ul>
    </div>

    <!-- Favourites -->
    <div class="row">
        @forelse ($favoriteDoctors as $doctor)
            @php
                $doctorName = 'Dr. '.($doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name));
                $clinic = $doctor->clinics->first();
                $lastBooked = $lastBookedDates[$doctor->id] ?? null;
            @endphp
            <div class="col-md-6 col-lg-4 d-flex">
                <div class="profile-widget patient-favour flex-fill">
                    <div class="fav-head">
                        <a href="javascript:void(0)" class="fav-btn favourite-btn" data-doctor-id="{{ $doctor->id }}">
                            <span class="favourite-icon favourite"><i class="isax isax-heart5"></i></span>
                        </a>
                        <div class="doc-img">
                            <a href="{{ route('doctor.details', $doctor->id) }}">
                                <img class="img-fluid" alt="{{ $doctorName }}" src="{{ $doctor->profile_photo_url ?: asset('backend/assets/img/doctor-grid/doctor-grid-01.jpg') }}">
                            </a>
                        </div>
                        <div class="pro-content">
                            <h3 class="title">
                                <a href="{{ route('doctor.details', $doctor->id) }}">{{ $doctorName }}</a>
                                @if ($doctor->email_verified_at)
                                    <i class="isax isax-tick-circle5 verified"></i>
                                @endif
                            </h3>
                            @if ($doctor->designation)
                                <p class="speciality">{{ $doctor->designation }}</p>
                            @endif
                            <ul class="available-info">
                                @if ($clinic?->location)
                                    <li>
                                        <i class="isax isax-location5 me-1"></i><span>Location :</span> {{ $clinic->location }}
                                    </li>
                                @endif
                            </ul>
                            @if ($lastBooked)
                                <div class="last-book">
                                    <p>Last Book on {{ date('d M Y', strtotime($lastBooked)) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="fav-footer">
                        <div class="row row-sm">
                            <div class="col-6">
                                <a href="{{ route('doctor.details', $doctor->id) }}" class="btn btn-md btn-light w-100">View Details</a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('doctor.booking', $doctor->id) }}" class="btn btn-md btn-outline-primary w-100">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <p class="text-center py-4 mb-0">You haven't favourited any doctors yet.</p>
            </div>
        @endforelse
    </div>
    <!-- /Favourites -->

</div>


@endsection
