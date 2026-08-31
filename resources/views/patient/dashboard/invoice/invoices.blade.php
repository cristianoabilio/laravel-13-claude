@extends('patient.patient_master')
@section('patient')

<!-- Invoices -->
<div class="col-lg-8 col-xl-9">

    <div class="dashboard-header">
        <h3>Invoices</h3>
        <ul class="header-list-btns">
            <li>
                <div class="input-block dash-search-input">
                    <input type="text" class="form-control" placeholder="Search">
                    <span class="search-icon"><i class="isax isax-search-normal"></i></span>
                </div>
            </li>
        </ul>
    </div>

        <div class="custom-table">
            <div class="table-responsive">
                <table class="table table-center mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Appointment Date</th>
                            <th>Booked on</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            @php
                                $doctorName = 'Dr '.($invoice->doctor->display_name ?: trim($invoice->doctor->first_name.' '.$invoice->doctor->last_name));
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('appointments.confirmation', $invoice->appointment) }}" class="link-primary">#{{ $invoice->invoice_number }}</a>
                                </td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="{{ route('doctor.details', $invoice->doctor_id) }}" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-3" src="{{ $invoice->doctor->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg') }}" alt="{{ $doctorName }}">
                                        </a>
                                        <a href="{{ route('doctor.details', $invoice->doctor_id) }}">{{ $doctorName }}</a>
                                    </h2>
                                </td>
                                <td>{{ $invoice->appointment->appointment_date->format('d M Y') }}</td>
                                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>${{ number_format((float) $invoice->total, 2) }}</td>
                                <td>
                                    <div class="action-item">
                                        <a href="{{ route('appointments.confirmation', $invoice->appointment) }}" title="View">
                                            <i class="isax isax-link-2"></i>
                                        </a>
                                        <a href="{{ route('appointments.invoice.download', $invoice->appointment) }}" title="Download Invoice">
                                            <i class="isax isax-import"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">You don't have any invoices yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</div>
<!-- /Invoices -->


@endsection
