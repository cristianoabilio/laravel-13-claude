@extends('frontend.home_master')
@section('home')
@php
    $doctorName = 'Dr. '.($doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name));
    $typeIcons = [
        'clinic' => 'isax-hospital5',
        'video_call' => 'isax-video5',
        'audio_call' => 'isax-call5',
        'chat' => 'isax-messages-15',
        'home_visit' => 'isax-home-15',
    ];
@endphp

<!-- Terms -->
<div class="doctor-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                @if ($services->isEmpty())
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="mb-2">Booking isn't available yet</h5>
                            <p class="mb-0">{{ $doctorName }} hasn't added any bookable services yet. Please check back later.</p>
                        </div>
                    </div>
                @else
                    <div class="booking-wizard">
                        <ul class="form-wizard-steps d-sm-flex align-items-center justify-content-center" id="progressbar2">
                            <li class="progress-active">
                                <div class="profile-step">
                                    <span class="multi-steps">1</span>
                                    <div class="step-section"><h6>Specialty</h6></div>
                                </div>
                            </li>
                            <li>
                                <div class="profile-step">
                                    <span class="multi-steps">2</span>
                                    <div class="step-section"><h6>Appointment Type</h6></div>
                                </div>
                            </li>
                            <li>
                                <div class="profile-step">
                                    <span class="multi-steps">3</span>
                                    <div class="step-section"><h6>Date & Time</h6></div>
                                </div>
                            </li>
                            <li>
                                <div class="profile-step">
                                    <span class="multi-steps">4</span>
                                    <div class="step-section"><h6>Basic Information</h6></div>
                                </div>
                            </li>
                            <li>
                                <div class="profile-step">
                                    <span class="multi-steps">5</span>
                                    <div class="step-section"><h6>Payment</h6></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('booking.store', $doctor->id) }}" method="POST" enctype="multipart/form-data" id="booking-form">
                        @csrf
                        <input type="hidden" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}">

                        <div class="booking-widget multistep-form mb-5">
                            {{-- STEP 1: Specialty & Services --}}
                            <fieldset id="first">
                                <div class="card booking-card mb-0">
                                    <div class="card-header">
                                        <div class="booking-header pb-0">
                                            @include('frontend.partials.booking-doctor-summary')
                                        </div>
                                    </div>
                                    <div class="card-body booking-body">
                                        <div class="card mb-0">
                                            <div class="card-body pb-1">
                                                @if ($specialities->isNotEmpty())
                                                    <div class="mb-4 pb-4 border-bottom">
                                                        <label class="form-label">Select Speciality</label>
                                                        <select class="select" id="speciality-filter">
                                                            @foreach ($specialities as $speciality)
                                                                <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                                <h6 class="mb-3">Services</h6>
                                                <div class="row">
                                                    @foreach ($services as $doctorService)
                                                        <div class="col-lg-4 col-md-6 service-row" data-speciality-id="{{ $doctorService->service->speciality_id }}">
                                                            <div class="service-item">
                                                                <input class="form-check-input ms-0 mt-0 service-checkbox" type="checkbox"
                                                                    name="doctor_service_ids[]" id="doctor-service-{{ $doctorService->id }}"
                                                                    value="{{ $doctorService->id }}"
                                                                    data-name="{{ $doctorService->service->name }}"
                                                                    data-price="{{ $doctorService->price }}"
                                                                    data-duration="{{ $doctorService->duration_minutes }}"
                                                                    @checked(in_array((string) $doctorService->id, (array) old('doctor_service_ids', [])))>
                                                                <label class="form-check-label ms-2" for="doctor-service-{{ $doctorService->id }}">
                                                                    <span class="service-title d-block mb-1">{{ $doctorService->service->name }}</span>
                                                                    <span class="fs-14 d-block">${{ number_format((float) $doctorService->price, 2) }} &middot; {{ $doctorService->duration_minutes }} min</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-end">
                                            <a href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
                                                Select Appointment Type
                                                <i class="isax isax-arrow-right-3 ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- STEP 2: Appointment Type + Clinic --}}
                            <fieldset>
                                <div class="card booking-card mb-0">
                                    <div class="card-header">
                                        <div class="booking-header pb-0">
                                            @include('frontend.partials.booking-doctor-summary')
                                        </div>
                                    </div>
                                    <div class="card-body booking-body">
                                        <div class="card mb-0">
                                            <div class="card-body pb-1">
                                                <h6 class="mb-3">Select Appointment Type</h6>
                                                <div class="row">
                                                    @foreach (\App\Enums\AppointmentType::cases() as $type)
                                                        <div class="col-xl col-md-3 col-sm-4">
                                                            <div class="radio-select text-center">
                                                                <input class="form-check-input ms-0 mt-0" name="appointment_type" type="radio"
                                                                    id="type-{{ $type->value }}" value="{{ $type->value }}"
                                                                    @checked(old('appointment_type', 'clinic') === $type->value)>
                                                                <label class="form-check-label" for="type-{{ $type->value }}">
                                                                    <i class="isax {{ $typeIcons[$type->value] }}"></i>
                                                                    <span class="service-title d-block">{{ $type->label() }}</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="clinics-path" id="clinics-path">
                                                    <h6 class="mb-3">Select Clinics</h6>
                                                    <div>
                                                        @forelse ($doctor->clinics as $clinic)
                                                            <div class="service-item">
                                                                <input class="form-check-input ms-0 mt-0" name="clinic_id" type="radio"
                                                                    id="clinic{{ $clinic->id }}" value="{{ $clinic->id }}"
                                                                    @checked((string) old('clinic_id') === (string) $clinic->id)>
                                                                <label class="form-check-label ms-2" for="clinic{{ $clinic->id }}">
                                                                    <span class="d-flex align-items-center flex-wrap rpw-gap-2">
                                                                        <span class="d-inline-block me-2">
                                                                            <img src="{{ $clinic->logo_url ?: asset('backend/assets/img/icons/clinic-icon-01.svg') }}" class="rounded-circle" alt="" width="32" height="32">
                                                                        </span>
                                                                        <span>
                                                                            <span class="service-title d-block mb-1">{{ $clinic->name ?: 'Clinic' }}</span>
                                                                            <span class="fs-14">{{ $clinic->address ?: $clinic->location }}</span>
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @empty
                                                            <p class="text-muted">This doctor hasn't registered any clinics yet.</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                <div class="mb-3" id="home-visit-address-wrap">
                                                    <label class="form-label">Your Address for the Home Visit</label>
                                                    <input type="text" class="form-control" name="home_visit_address" value="{{ old('home_visit_address', auth()->user()->address) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
                                            <a href="javascript:void(0);" class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
                                                <i class="isax isax-arrow-left-2 me-1"></i>Back
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
                                                Select Date & Time
                                                <i class="isax isax-arrow-right-3 ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- STEP 3: Date & Time --}}
                            <fieldset>
                                <div class="card booking-card mb-0">
                                    <div class="card-header">
                                        <div class="booking-header pb-0">
                                            @include('frontend.partials.booking-doctor-summary')
                                        </div>
                                    </div>
                                    <div class="card-body booking-body">
                                        <div class="card mb-0">
                                            <div class="card-body pb-1">
                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <div class="card">
                                                            <div class="card-body p-2 pt-3">
                                                                <div id="datetimepickershow"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <div class="card booking-wizard-slots">
                                                            <div class="card-body" id="slots-container">
                                                                <p class="text-muted">Select a date to see available times.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
                                            <a href="javascript:void(0);" class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
                                                <i class="isax isax-arrow-left-2 me-1"></i>Back
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
                                                Add Basic Information
                                                <i class="isax isax-arrow-right-3 ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- STEP 4: Patient Information --}}
                            <fieldset>
                                <div class="card booking-card mb-0">
                                    <div class="card-header">
                                        <div class="booking-header pb-0">
                                            @include('frontend.partials.booking-doctor-summary', ['showBookingInfo' => true])
                                        </div>
                                    </div>
                                    <div class="card-body booking-body">
                                        <div class="card mb-0">
                                            <div class="card-body pb-1">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Phone Number</label>
                                                            <input type="text" class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Email Address</label>
                                                            <input type="text" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Symptoms</label>
                                                            <input type="text" class="form-control" name="symptoms" value="{{ old('symptoms') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Attachment (medical documents)</label>
                                                            <input type="file" class="form-control" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                                            <small class="form-text text-muted">PDF, JPG or PNG, up to 5MB each.</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Reason for Visit</label>
                                                            <textarea class="form-control" rows="3" name="reason_for_visit">{{ old('reason_for_visit') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
                                            <a href="javascript:void(0);" class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
                                                <i class="isax isax-arrow-left-2 me-1"></i>Back
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill">
                                                Select Payment
                                                <i class="isax isax-arrow-right-3 ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- STEP 5: Payment --}}
                            <fieldset>
                                <div class="card booking-card mb-0">
                                    <div class="card-header">
                                        <div class="booking-header pb-0">
                                            @include('frontend.partials.booking-doctor-summary')
                                        </div>
                                    </div>
                                    <div class="card-body booking-body">
                                        <div class="row">
                                            <div class="col-lg-6 d-flex">
                                                <div class="card flex-fill mb-3 mb-lg-0">
                                                    <div class="card-body">
                                                        <h6 class="mb-3">Payment Gateway</h6>
                                                        <div class="payment-tabs">
                                                            <ul class="nav nav-pills mb-3 row" id="pills-tab" role="tablist">
                                                                <li class="nav-item col-sm-4" role="presentation">
                                                                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab">
                                                                        <img src="{{ asset('backend/assets/img/icons/payment-icon-05.svg') }}" class="me-2" alt="">
                                                                        Credit Card
                                                                    </button>
                                                                </li>
                                                                <li class="nav-item col-sm-4" role="presentation">
                                                                    <button class="nav-link" id="pills-profile-tab" type="button" role="tab" disabled>
                                                                        <img src="{{ asset('backend/assets/img/icons/payment-icon-06.svg') }}" class="me-2" alt="">
                                                                        Paypal <small>(soon)</small>
                                                                    </button>
                                                                </li>
                                                                <li class="nav-item col-sm-4" role="presentation">
                                                                    <button class="nav-link" id="pills-contact-tab" type="button" role="tab" disabled>
                                                                        <img src="{{ asset('backend/assets/img/icons/payment-icon-07.svg') }}" class="me-2" alt="">
                                                                        Stripe <small>(soon)</small>
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content" id="pills-tabContent">
                                                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Card Holder Name</label>
                                                                        <div class="position-relative input-icon">
                                                                            <input type="text" class="form-control" name="card_holder_name" value="{{ old('card_holder_name') }}">
                                                                            <span><i class="isax isax-user"></i></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Card Number</label>
                                                                        <div class="position-relative input-icon">
                                                                            <input type="text" class="form-control" name="card_number" placeholder="4242 4242 4242 4242" autocomplete="off">
                                                                            <span><i class="isax isax-card-tick"></i></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Expire Date</label>
                                                                        <div class="position-relative input-icon">
                                                                            <input type="text" class="form-control" name="card_expiry" placeholder="MM/YY" autocomplete="off">
                                                                            <span><i class="isax isax-calendar-2"></i></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-0">
                                                                        <label class="form-label">CVV</label>
                                                                        <div class="position-relative input-icon">
                                                                            <input type="text" class="form-control" name="card_cvv" placeholder="123" autocomplete="off">
                                                                            <span><i class="isax isax-check"></i></span>
                                                                        </div>
                                                                    </div>
                                                                    <p class="form-text mt-2">This is a demo payment form - no real card network is contacted, and only the last 4 digits are ever stored.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 d-flex">
                                                <div class="card flex-fill mb-0">
                                                    <div class="card-body">
                                                        <h6 class="mb-3">Booking Info</h6>
                                                        <div class="mb-3">
                                                            <label class="form-label">Date & Time</label>
                                                            <div class="form-plain-text summary-datetime">-</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Appointment type</label>
                                                            <div class="form-plain-text summary-type">-</div>
                                                        </div>
                                                        <div class="mb-3 summary-clinic-row" style="display:none;">
                                                            <label class="form-label">Clinic</label>
                                                            <div class="form-plain-text summary-clinic">-</div>
                                                        </div>
                                                        <div class="pt-3 border-top booking-more-info">
                                                            <h6 class="mb-3">Payment Info</h6>
                                                            <div class="summary-line-services"></div>
                                                        </div>
                                                        <div class="bg-primary d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between p-3 rounded">
                                                            <h6 class="text-white">Total</h6>
                                                            <h6 class="text-white summary-total">$0.00</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
                                            <a href="javascript:void(0);" class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
                                                <i class="isax isax-arrow-left-2 me-1"></i>Back
                                            </a>
                                            <button type="submit" class="btn btn-md btn-primary-gradient inline-flex align-items-center rounded-pill">
                                                Confirm & Pay
                                                <i class="isax isax-arrow-right-3 ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </form>
                @endif

                <div class="text-center">
                    <p class="mb-0">Copyright © {{ now()->year }}. All Rights Reserved, Doccure</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Terms -->

<script>
    window.BOOKING_CONFIG = {
        slotsUrl: @json(route('booking.slots', $doctor->id)),
        closedWeekdays: @json($closedWeekdays),
    };
</script>
@endsection
