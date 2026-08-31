@php
    $showBookingInfo = $showBookingInfo ?? false;
@endphp
<div class="card mb-0">
    <div class="card-body">
        <div class="d-flex align-items-center flex-wrap rpw-gap-2 flex-wrap row-gap-2">
            <span class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0">
                <img src="{{ $doctor->profile_photo_url ?: asset('backend/assets/img/doctor-grid/doctor-grid-01.jpg') }}" alt="{{ $doctorName }}">
            </span>
            <div>
                <h4 class="mb-1">{{ $doctorName }}</h4>
                @if ($doctor->designation)
                    <p class="text-indigo mb-3 fw-medium">{{ $doctor->designation }}</p>
                @endif
                @if ($primaryClinic?->address)
                    <p class="mb-0"><i class="isax isax-location me-2"></i>{{ $primaryClinic->address }}</p>
                @endif
            </div>
        </div>

        @if ($showBookingInfo)
            <h6 class="mb-2 mt-4">Booking Info</h6>
            <div class="row gx-2 gy-3">
                <div class="col-lg-3 col-sm-6">
                    <div>
                        <h6 class="fs-14 fw-medium mb-1">Services</h6>
                        <p class="mb-0 summary-services">-</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div>
                        <h6 class="fs-14 fw-medium mb-1">Duration</h6>
                        <p class="mb-0 summary-duration">-</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div>
                        <h6 class="fs-14 fw-medium mb-1">Date & Time</h6>
                        <p class="mb-0 summary-datetime">-</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div>
                        <h6 class="fs-14 fw-medium mb-1">Appointment type</h6>
                        <p class="mb-0 summary-type">-</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
