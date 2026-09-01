@php
    $typeIcons = [
        'clinic' => 'isax-hospital5',
        'video_call' => 'isax-video5',
        'audio_call' => 'isax-call5',
        'chat' => 'isax-messages-15',
        'home_visit' => 'isax-home-15',
    ];
    $patientName = trim($appointment->first_name.' '.$appointment->last_name);
@endphp
<div class="appointment-wrap">
    <ul>
        <li>
            <div class="patinet-information">
                <a href="javascript:void(0);">
                    <img src="{{ $appointment->patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $patientName }}">
                </a>
                <div class="patient-info">
                    <p>#{{ $appointment->appointment_number }}</p>
                    <h6><a href="javascript:void(0);">{{ $patientName }}</a><span class="badge new-tag">New</span></h6>
                </div>
            </div>
        </li>
        <li class="appointment-info">
            <p><i class="isax isax-clock5"></i>{{ $appointment->appointment_date->format('d M Y') }} {{ $appointment->start_time->format('h.i A') }}</p>
            <p class="md-text">{{ $appointment->service_name ?: $appointment->appointment_type->label() }}</p>
        </li>
        <li class="appointment-type">
            <p class="md-text">Type of Appointment</p>
            <p><i class="isax {{ $typeIcons[$appointment->appointment_type->value] }} text-blue"></i>{{ $appointment->appointment_type->label() }}</p>
        </li>
        <li>
            <ul class="request-action">
                <li>
                    <a href="javascript:void(0);" class="accept-link" data-appointment-id="{{ $appointment->id }}"><i class="fa-solid fa-check"></i>Accept</a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="reject-link" data-appointment-id="{{ $appointment->id }}"><i class="fa-solid fa-xmark"></i>Reject</a>
                </li>
            </ul>
        </li>
    </ul>
</div>
