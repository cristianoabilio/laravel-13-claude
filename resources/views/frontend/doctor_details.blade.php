@extends('frontend.home_master')
@section('home')
@php
    $doctorName = 'Dr. '.($doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name));
    $isAvailable = $doctor->availability_status === 'available';
@endphp
<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="isax isax-home-15"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ __('Doctors') }}</li>
                        <li class="breadcrumb-item active">{{ $doctorName }}</li>
                    </ol>
                    <h2 class="breadcrumb-title">{{ __('Doctor Profile') }}</h2>
                </nav>
            </div>
        </div>
    </div>
    <div class="breadcrumb-bg">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-icon.png') }}" alt="img" class="breadcrumb-bg-03">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-icon.png') }}" alt="img" class="breadcrumb-bg-04">
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content">
    <div class="container">

        <!-- Doctor Widget -->
        <div class="card doc-profile-card">
            <div class="card-body">
                <div class="doctor-widget doctor-profile-two">
                    <div class="doc-info-left">
                        <div class="doctor-img">
                            <img src="{{ $doctor->profile_photo_url ?: asset('backend/assets/img/doctors/doc-profile-02.jpg') }}" class="img-fluid" alt="{{ $doctorName }}">
                        </div>
                        <div class="doc-info-cont">
                            <span class="badge {{ $isAvailable ? 'doc-avail-badge' : 'bg-danger-light' }}"><i class="fa-solid fa-circle"></i>{{ $isAvailable ? __('Available') : __('Not Available') }} </span>
                            <h4 class="doc-name">
                                {{ $doctorName }}
                                @if ($doctor->email_verified_at)
                                    <img src="{{ asset('backend/assets/img/icons/badge-check.svg') }}" alt="Verified">
                                @endif
                                @if ($specialities->isNotEmpty())
                                    <span class="badge doctor-role-badge"><i class="fa-solid fa-circle"></i>{{ $specialities->first()->name }}</span>
                                @elseif ($doctor->designation)
                                    <span class="badge doctor-role-badge"><i class="fa-solid fa-circle"></i>{{ $doctor->designation }}</span>
                                @endif
                            </h4>
                            @if ($credentialsLine)
                                <p>{{ $credentialsLine }}</p>
                            @endif
                            @if (! empty($doctor->known_languages))
                                <p>{{ __('Speaks :languages', ['languages' => implode(', ', $doctor->known_languages)]) }}</p>
                            @endif
                            @if ($primaryClinic?->address)
                                <p class="address-detail">
                                    <span class="loc-icon"><i class="feather-map-pin"></i></span>{{ $primaryClinic->address }}
                                    <a href="https://www.google.com/maps?q={{ urlencode($primaryClinic->address) }}" target="_blank" rel="noopener" class="view-text">( {{ __('View Location') }} )</a>
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="doc-info-right">
                        <ul class="doctors-activities">

                                <li>
                                    @if ($currentExperience)
                                    <div class="hospital-info">
                                        <span class="list-icon"><img src="{{ asset('backend/assets/img/icons/watch-icon.svg') }}" alt="Img"></span>
                                        <p>{{ $currentExperience->employment_type->label() }}@if ($currentExperience->hospital), {{ $currentExperience->hospital }}@endif</p>
                                    </div>
                                    @endif
                                    <ul class="sub-links">
                                        <li><a href="javascript:void(0);" class="fav-icon {{ $isFavorited ? 'selected' : '' }}" data-doctor-id="{{ $doctor->id }}"><i class="feather-heart"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="feather-share-2"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="feather-link"></i></a></li>
                                    </ul>
                                </li>

                            @if ($primaryClinic)
                                <li>
                                    <div class="hospital-info">
                                        <span class="list-icon"><img src="{{ asset('backend/assets/img/icons/building-icon.svg') }}" alt="Img"></span>
                                        <p>{{ $primaryClinic->name ?: __('Clinic') }}</p>
                                    </div>
                                    @if ($isAvailable)
                                        <h5 class="accept-text"><span><i class="feather-check"></i></span>{{ __('Accepting New Patients') }}</h5>
                                    @endif
                                </li>
                            @endif
                            <li>
                                <ul class="contact-doctors">
                                    <li><a href="javascript:void(0);"><span><img src="{{ asset('backend/assets/img/icons/device-message2.svg') }}" alt="Img"></span>{{ __('Chat') }}</a></li>
                                    <li><a href="javascript:void(0);"><span class="bg-violet"><i class="feather-phone-forwarded"></i></span>{{ __('Audio Call') }}</a></li>
                                    <li><a href="javascript:void(0);"><span class="bg-indigo"><i class="fa-solid fa-video"></i></span>{{ __('Video Call') }}</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="doc-profile-card-bottom">
                    @if ($yearsInPractice)
                        <ul>
                            <li>
                                <span class="bg-dark-blue"><img src="{{ asset('backend/assets/img/icons/bullseye.svg') }}" alt="Img"></span>
                                {{ trans_choice('In Practice for :count Year|In Practice for :count Years', $yearsInPractice, ['count' => $yearsInPractice]) }}
                            </li>
                        </ul>
                    @endif
                    <div class="bottom-book-btn">
                        <p><span>{{ __('Price : :price', ['price' => $priceRange ?: __('Contact for pricing')]) }} </span>@if ($priceRange) {{ __('for a Session') }} @endif</p>
                        <div class="clinic-booking">
                            <a class="apt-btn" href="{{ route('doctor.booking', $doctor->id) }}">{{ __('Book Appointment') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Doctor Widget -->

        <div class="doctors-detailed-info">
            <ul class="information-title-list">
                <li class="active">
                    <a href="#doc_bio">{{ __('Doctor Bio') }}</a>
                </li>
                <li>
                    <a href="#experience">{{ __('Experience') }}</a>
                </li>
                <li>
                    <a href="#services">{{ __('Treatments') }}</a>
                </li>
                <li>
                    <a href="#speciality">{{ __('Speciality') }}</a>
                </li>
                <li>
                    <a href="#availability">{{ __('Availability') }}</a>
                </li>
                <li>
                    <a href="#clinic">{{ __('Clinics') }}</a>
                </li>
                <li>
                    <a href="#membership">{{ __('Memberships') }}</a>
                </li>
                <li>
                    <a href="#bussiness_hour">{{ __('Business Hours') }}</a>
                </li>
                <li>
                    <a href="#review">{{ __('Review') }}</a>
                </li>
            </ul>
            <div class="doc-information-main">
                <div class="doc-information-details bio-detail" id="doc_bio">
                    <div class="detail-title">
                        <h4>{{ __('Doctor Bio') }}</h4>
                    </div>
                    <p>{{ $bio }}</p>
                </div>
                <div class="doc-information-details" id="experience">
                    <div class="detail-title">
                        <h4>{{ __('Practice Experience') }}</h4>
                    </div>
                    @forelse ($doctor->experiences as $experience)
                        <div class="experience-info {{ $loop->last ? 'mb-0' : '' }}">
                            <div class="experience-logo">
                                <span>
                                    @if ($experience->hospital_logo_url)
                                        <img src="{{ $experience->hospital_logo_url }}" alt="Img">
                                    @else
                                        <img src="{{ asset('backend/assets/img/icons/experience-logo-01.svg') }}" alt="Img">
                                    @endif
                                </span>
                            </div>
                            <div class="experience-content {{ $loop->last ? 'mb-0' : '' }}">
                                <h5>{{ $experience->hospital }}@if ($experience->title), {{ $experience->title }}@endif</h5>
                                <ul class="ent-list">
                                    <li>{{ $experience->employment_type->label() }}</li>
                                    @if ($experience->location)
                                        <li>{{ $experience->location }}</li>
                                    @endif
                                </ul>
                                <ul class="date-list">
                                    <li>{{ $experience->date_range_label }}</li>
                                    <li>{{ $experience->duration_label }}</li>
                                </ul>
                                @if ($experience->job_description)
                                    <p>{{ $experience->job_description }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p>{{ __('No practice experience added yet.') }}</p>
                    @endforelse
                </div>
                <div class="doc-information-details" id="speciality">
                    <div class="detail-title">
                        <h4>{{ __('Speciality') }}</h4>
                    </div>
                    @if ($specialities->isNotEmpty())
                        <ul class="special-links">
                            @foreach ($specialities as $speciality)
                                <li><a href="javascript:void(0);">{{ $speciality->name }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        <p>{{ __('No specialities added yet.') }}</p>
                    @endif
                </div>
                <div class="doc-information-details" id="services">
                    <div class="detail-title">
                        <h4>{{ __('Services & Pricing') }}</h4>
                    </div>
                    @if ($services->isNotEmpty())
                        <ul class="special-links">
                            @foreach ($services as $doctorService)
                                <li><a href="javascript:void(0);">{{ $doctorService->service->name }} <span>${{ number_format((float) $doctorService->price, 2) }}</span></a></li>
                            @endforeach
                        </ul>
                    @else
                        <p>{{ __('No services added yet.') }}</p>
                    @endif
                </div>
                <div class="doc-information-details" id="availability">
                    <div class="detail-title slider-nav d-flex justify-content-between align-items-center">
                        <h4>{{ __('Availability') }}</h4>
                        @if ($availabilitySlots->isNotEmpty())
                            <div class="nav nav-container slide-2"></div>
                        @endif
                    </div>
                    @if ($availabilitySlots->isNotEmpty())
                        <div class="availability-slots-slider owl-carousel">
                            @foreach ($availabilitySlots as $slot)
                                <div class="availability-date">
                                    <div class="book-date">
                                        <h6>{{ $slot['date']->format('D d M') }}</h6>
                                        <span>{{ $slot['from']->format('h:i A') }} - {{ $slot['to']->format('h:i A') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>{{ __('No availability configured yet.') }}</p>
                    @endif
                </div>
                <div class="doc-information-details" id="clinic">
                    <div class="detail-title">
                        <h4>{{ __('Clinics & Locations') }}</h4>
                    </div>
                    @forelse ($doctor->clinics as $clinic)
                        <div class="clinic-loc {{ $loop->last ? 'mb-0' : '' }}">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <div class="clinic-info">
                                        <div class="clinic-img">
                                            <img src="{{ $clinic->logo_url ?: asset('backend/assets/img/clinic/clinic-11.jpg') }}" alt="{{ $clinic->name }}">
                                        </div>
                                        <div class="detail-clinic">
                                            <h5>{{ $clinic->name ?: __('Clinic') }}</h5>
                                            <p>{{ $clinic->address ?: $clinic->location }}</p>
                                        </div>
                                    </div>
                                </div>
                                @if ($clinic->address)
                                    <div class="col-lg-5">
                                        <div class="contact-map d-flex">
                                            <iframe src="https://www.google.com/maps?q={{ urlencode($clinic->address) }}&output=embed" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p>{{ __('No clinics added yet.') }}</p>
                    @endforelse
                </div>
                <div class="doc-information-details" id="membership">
                    <div class="detail-title">
                        <h4>{{ __('Membership') }}</h4>
                    </div>
                    @forelse ($doctor->memberships as $membership)
                        <div class="member-ship-info {{ $loop->last ? 'mb-0' : '' }}">
                            <span class="mem-check"><i class="fa-solid fa-check"></i></span>
                            <p><strong>{{ $membership->title }}</strong>@if ($membership->description) - {{ $membership->description }}@endif</p>
                        </div>
                    @empty
                        <p>{{ __('No memberships added yet.') }}</p>
                    @endforelse
                </div>
                <div class="doc-information-details" id="bussiness_hour">
                    <div class="detail-title">
                        <h4>{{ __('Business Hours') }}</h4>
                    </div>
                    <div class="hours-business">
                        <ul>
                            @foreach ($businessHours as $hour)
                                <li>
                                    @if ($hour->day === $todayBusinessHour?->day)
                                        <div class="today-hours">
                                            <h6>{{ __('Today') }}</h6>
                                            <span>{{ now()->format('j M Y') }}</span>
                                        </div>
                                        <div class="availed">
                                            <span class="badge {{ $hour->is_open ? 'doc-avail-badge' : 'bg-danger-light' }}"><i class="fa-solid fa-circle"></i>{{ $hour->is_open ? __('Available') : __('Closed') }} </span>
                                            @if ($hour->is_open)
                                                <p>{{ $hour->from_time->format('h:i A') }} - {{ $hour->to_time->format('h:i A') }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <h6>{{ $hour->day->label() }}</h6>
                                        <p>{{ $hour->is_open ? $hour->from_time->format('h:i A').' - '.$hour->to_time->format('h:i A') : __('Closed') }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="doc-information-details" id="review">
                    <div class="detail-title d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h4>{{ __('Reviews (:count)', ['count' => $reviewsCount]) }}</h4>
                        @if ($reviewsCount > 0)
                            <div class="star-rated">
                                <span>{{ $averageRating }}</span>
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= round($averageRating) ? 'filled' : '' }}"></i>
                                @endfor
                            </div>
                        @endif
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($doctorReviews->isEmpty())
                        <p>{{ __('No reviews yet.') }}</p>
                    @else
                        <div class="widget review-listing">
                            <ul class="comments-list">
                                @foreach ($doctorReviews as $review)
                                    @php
                                        $reviewerName = $review->patient->display_name ?: trim($review->patient->first_name.' '.$review->patient->last_name);
                                    @endphp
                                    <li>
                                        <div class="comment">
                                            <img class="avatar avatar-sm rounded-circle" alt="{{ $reviewerName }}" src="{{ $review->patient->profile_photo_url ?: asset('backend/assets/img/patients/patient.jpg') }}">
                                            <div class="comment-body">
                                                <div class="meta-data">
                                                    <span class="comment-author">{{ $reviewerName }}</span>
                                                    <span class="comment-date">{{ $review->created_at->diffForHumans() }}</span>
                                                    <div class="review-count rating">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p class="comment-content">{{ $review->comment }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @auth
                        @if (auth()->user()->role === 'patient')
                            <div class="write-review">
                                @if ($hasReviewed)
                                    <p class="mb-0">{{ __('You have already reviewed this doctor.') }}</p>
                                @elseif (! $hasCompletedAppointment)
                                    <p class="mb-0">{{ __("You didn't meet with this doctor.") }}</p>
                                @else
                                    <h4>{{ __('Write a review for') }} <strong>Dr. {{ $doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name) }}</strong></h4>
                                    <form action="{{ route('doctor.reviews.store', $doctor->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="mb-2">{{ __('Rating') }}</label>
                                            <div class="star-rating">
                                                <input id="star-5" type="radio" name="rating" value="5" @checked(old('rating') == 5)>
                                                <label for="star-5" title="5 stars"><i class="active fa fa-star"></i></label>
                                                <input id="star-4" type="radio" name="rating" value="4" @checked(old('rating') == 4)>
                                                <label for="star-4" title="4 stars"><i class="active fa fa-star"></i></label>
                                                <input id="star-3" type="radio" name="rating" value="3" @checked(old('rating') == 3)>
                                                <label for="star-3" title="3 stars"><i class="active fa fa-star"></i></label>
                                                <input id="star-2" type="radio" name="rating" value="2" @checked(old('rating') == 2)>
                                                <label for="star-2" title="2 stars"><i class="active fa fa-star"></i></label>
                                                <input id="star-1" type="radio" name="rating" value="1" @checked(old('rating') == 1)>
                                                <label for="star-1" title="1 star"><i class="active fa fa-star"></i></label>
                                            </div>
                                            @error('rating')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-2">{{ __('Your review') }}</label>
                                            <textarea name="comment" class="form-control" rows="4">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="submit-section">
                                            <button type="submit" class="btn btn-primary submit-btn">{{ __('Add Review') }}</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="write-review">
                            <p class="mb-0"><a href="{{ route('login') }}">{{ __('Log in') }}</a> {{ __('as a patient to write a review.') }}</p>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /Page Content -->

@if ($errors->any() || session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var reviewSection = document.getElementById('review');
            if (reviewSection) {
                reviewSection.scrollIntoView({ block: 'start' });
            }
        });
    </script>
@endif
@endsection
