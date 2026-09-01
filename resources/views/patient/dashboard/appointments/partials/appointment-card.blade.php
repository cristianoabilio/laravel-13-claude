@php
    $doctorName = 'Dr '.($appointment->doctor->display_name ?: trim($appointment->doctor->first_name.' '.$appointment->doctor->last_name));
    $statusMeta = match ($appointment->status) {
        \App\Enums\AppointmentStatus::Pending => ['label' => 'Upcoming', 'class' => 'badge-warning'],
        \App\Enums\AppointmentStatus::Confirmed => ['label' => 'Confirmed', 'class' => 'badge-success'],
        \App\Enums\AppointmentStatus::Cancelled => ['label' => 'Rejected', 'class' => 'badge-danger'],
        \App\Enums\AppointmentStatus::Completed => ['label' => 'Completed', 'class' => 'badge-success'],
    };
@endphp
<div class="appointment-wrap">
    <ul>
        <li>
            <div class="patinet-information">
                <a href="{{ route('appointments.confirmation', $appointment) }}">
                    <img src="{{ $appointment->doctor->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg') }}" alt="{{ $doctorName }}">
                </a>
                <div class="patient-info">
                    <p>#{{ $appointment->appointment_number }}</p>
                    <h6><a href="{{ route('appointments.confirmation', $appointment) }}">{{ $doctorName }}</a><span class="badge status-tag {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span></h6>
                </div>
            </div>
        </li>
        <li class="appointment-info">
            <p><i class="isax isax-clock5"></i>{{ $appointment->appointment_date->format('d M Y') }} {{ $appointment->start_time->format('h.i A') }}</p>
            <ul class="d-flex apponitment-types">
                @if ($appointment->service_name)
                    <li>{{ $appointment->service_name }}</li>
                @endif
                <li>{{ $appointment->appointment_type->label() }}</li>
            </ul>
        </li>
        <li class="mail-info-patient">
            <ul>
                <li><i class="isax isax-sms5"></i>{{ $appointment->doctor->email }}</li>
                @if ($appointment->doctor->phone)
                    <li><i class="isax isax-call5"></i>{{ $appointment->doctor->phone }}</li>
                @endif
            </ul>
        </li>
        <li class="appointment-action">
            <ul>
                <li>
                    <a href="{{ route('appointments.confirmation', $appointment) }}"><i class="isax isax-eye4"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);"><i class="isax isax-messages-25"></i></a>
                </li>
            </ul>
        </li>
        @unless ($cancelled)
            <li class="appointment-detail-btn">
                <a href="{{ route('appointments.confirmation', $appointment) }}" class="btn btn-md btn-primary-gradient"><i class="isax isax-calendar-tick5 me-1"></i>View Details</a>
            </li>
        @endunless
    </ul>
</div>
