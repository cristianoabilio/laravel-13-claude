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
                <a href="{{ route('patient.details', $appointment->patient_id) }}">
                    <img src="{{ $appointment->patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $patientName }}">
                </a>
                <div class="patient-info">
                    <p>#{{ $appointment->appointment_number }}</p>
                    <h6><a href="{{ route('patient.details', $appointment->patient_id) }}">{{ $patientName }}</a></h6>
                </div>
            </div>
        </li>
        <li class="appointment-info">
            <p><i class="isax isax-clock5"></i>{{ $appointment->appointment_date->format('d M Y') }} {{ $appointment->start_time->format('h.i A') }}</p>
            <ul class="d-flex apponitment-types">
                @if ($appointment->service_name)
                    <li>{{ $appointment->service_name }}</li>
                @endif
                <li><i class="isax {{ $typeIcons[$appointment->appointment_type->value] }} me-1"></i>{{ $appointment->appointment_type->label() }}</li>
            </ul>
        </li>
        <li class="mail-info-patient">
            <ul>
                <li><i class="isax isax-sms5"></i>{{ $appointment->email }}</li>
                @if ($appointment->phone)
                    <li><i class="isax isax-call5"></i>{{ $appointment->phone }}</li>
                @endif
            </ul>
        </li>
        @if ($showStartNow)
            <li class="appointment-action">
                <ul>
                    <li>
                        <a href="{{ route('patient.details', $appointment->patient_id) }}"><i class="isax isax-eye4"></i></a>
                    </li>
                </ul>
            </li>
            <li class="appointment-start">
                <a href="javascript:void(0);" class="start-link" data-appointment-id="{{ $appointment->id }}">Start Now</a>
            </li>
        @else
            <li class="appointment-detail-btn">
                <a href="{{ route('patient.details', $appointment->patient_id) }}" class="start-link">View Details</a>
            </li>
        @endif
    </ul>
</div>
