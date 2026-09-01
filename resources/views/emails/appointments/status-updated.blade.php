@component('mail::message')
# Appointment {{ $appointment->status->label() }}

Hi {{ $appointment->first_name }},

Your appointment with Dr. {{ $appointment->doctor->display_name ?: trim($appointment->doctor->first_name.' '.$appointment->doctor->last_name) }}
on {{ $appointment->appointment_date->format('d M Y') }} at {{ $appointment->start_time->format('h:i A') }}
has been **{{ $appointment->status->label() }}**.

@if ($appointment->status === \App\Enums\AppointmentStatus::Confirmed)
We look forward to seeing you at your scheduled time.
@else
Please feel free to book another appointment at a time that works for you.
@endif

@component('mail::button', ['url' => route('appointments.confirmation', $appointment)])
View Appointment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
