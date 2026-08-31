@extends('frontend.home_master')
@section('home')
@php
    $doctorName = 'Dr. '.($appointment->doctor->display_name ?: trim($appointment->doctor->first_name.' '.$appointment->doctor->last_name));
    $qrPayload = urlencode($appointment->appointment_number);
@endphp

<!-- Terms -->
<div class="doctor-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card booking-card">
                    <div class="card-body booking-body pb-1">
                        <div class="row">
                            <div class="col-lg-8 d-flex">
                                <div class="flex-fill">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="d-flex align-items-center flex-wrap rpw-gap-2">
                                                <i class="isax isax-tick-circle5 text-success me-2"></i>
                                                Booking Confirmed
                                            </h5>
                                        </div>
                                        <div class="card-header d-flex align-items-center flex-wrap rpw-gap-2">
                                            <span class="avatar avatar-lg avatar-rounded me-2 flex-shrink-0">
                                                <img src="{{ $appointment->doctor->profile_photo_url ?: asset('backend/assets/img/doctor-grid/doctor-grid-01.jpg') }}" alt="{{ $doctorName }}">
                                            </span>
                                            <p class="mb-0">Your booking has been confirmed with <span class="text-dark">{{ $doctorName }}</span>. Please arrive <span class="text-dark">15 minutes</span> before the appointment time.</p>
                                        </div>
                                        <div class="card-body pb-1">
                                            <h6 class="mb-3">Booking Info</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Services</label>
                                                        <div class="form-plain-text">{{ $appointment->services->pluck('service_name')->implode(', ') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Duration</label>
                                                        <div class="form-plain-text">{{ $appointment->duration_minutes }} Mins</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Date & Time</label>
                                                        <div class="form-plain-text">{{ $appointment->start_time->format('h:i A') }} - {{ $appointment->end_time->format('h:i A') }}, {{ $appointment->appointment_date->format('d M Y') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Appointment type</label>
                                                        <div class="form-plain-text">{{ $appointment->appointment_type->label() }}</div>
                                                    </div>
                                                </div>
                                                @if ($appointment->clinic)
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Clinic Name & Location</label>
                                                            <div class="form-plain-text">
                                                                {{ $appointment->clinic->name }}
                                                                @if ($appointment->clinic->address)
                                                                    <a href="https://www.google.com/maps?q={{ urlencode($appointment->clinic->address) }}" target="_blank" rel="noopener" class="text-primary">View Location</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($appointment->home_visit_address)
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Home Visit Address</label>
                                                            <div class="form-plain-text">{{ $appointment->home_visit_address }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Booking Status</label>
                                                        <div class="form-plain-text text-capitalize">{{ $appointment->status->label() }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Payment Status</label>
                                                        <div class="form-plain-text text-capitalize">{{ $appointment->payment_status->label() }} @if ($appointment->payment) (Card ending {{ $appointment->payment->card_last_four }}) @endif</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="text-center">
                                            <h6 class="fs-14 mb-2">Booking Number</h6>
                                            <span class="booking-id-badge mb-3">{{ $appointment->appointment_number }}</span>
                                            <span class="d-block mb-3">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $qrPayload }}" alt="QR code for {{ $appointment->appointment_number }}" width="150" height="150">
                                            </span>
                                            <p>Scan this QR code to look up this appointment</p>
                                        </div>
                                        <div>
                                            @if ($appointment->invoice)
                                                <a href="{{ route('appointments.invoice.download', $appointment) }}" class="btn w-100 mb-3 btn-md btn-dark inline-flex align-items-center rounded-pill">
                                                    <i class="isax isax-document-download me-1"></i>
                                                    Download Invoice ({{ $appointment->invoice->invoice_number }})
                                                </a>
                                            @endif
                                            <a href="{{ route('home') }}" class="btn w-100 btn-md btn-primary-gradient inline-flex align-items-center rounded-pill">
                                                Back to Home
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('patient.settings') }}">
                        <i class="isax isax-arrow-left-2 me-1"></i>
                        Back to My Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Terms -->
@endsection
