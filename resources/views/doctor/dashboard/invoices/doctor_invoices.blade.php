@extends('doctor.doctor_master')
@section('doctor')

<div class="dashboard-header">
<h3>Invoices</h3>
</div>

<div class="search-header">
    <div class="search-field">
        <input type="text" class="form-control" placeholder="Search">
        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
    </div>
</div>

@if ($invoices->isEmpty())
    <div class="custom-table">
        <p class="text-center mb-0 py-4">You don't have any invoices yet.</p>
    </div>
@else
    <div class="custom-table">
        <div class="table-responsive">
            <table class="table table-center mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Appointment Date</th>
                        <th>Booked on</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        @php
                            $patientName = $invoice->patient->display_name ?: trim($invoice->patient->first_name.' '.$invoice->patient->last_name);
                        @endphp
                        <tr>
                            <td><a href="javascript:void(0);" class="text-blue-600" data-bs-toggle="modal" data-bs-target="#invoice_view_{{ $invoice->id }}">#{{ $invoice->invoice_number }}</a></td>
                            <td>
                                <h2 class="table-avatar">
                                    <span class="avatar avatar-sm me-2">
                                        <img class="avatar-img rounded-3" src="{{ $invoice->patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $patientName }}">
                                    </span>
                                    {{ $patientName }}
                                </h2>
                            </td>
                            <td>{{ $invoice->appointment->appointment_date->format('d M Y') }}</td>
                            <td>{{ $invoice->created_at->format('d M Y') }}</td>
                            <td>${{ number_format((float) $invoice->total, 2) }}</td>
                            <td>
                                <div class="action-item">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#invoice_view_{{ $invoice->id }}" title="View">
                                        <i class="isax isax-link-2"></i>
                                    </a>
                                    <a href="{{ route('appointments.invoice.download', $invoice->appointment_id) }}" title="Download Invoice">
                                        <i class="isax isax-printer5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $invoices->links('vendor.pagination.doccure') }}
@endif

@foreach ($invoices as $invoice)
    @php
        $payment = $invoice->appointment->payment;
    @endphp
    <!--View Invoice -->
    <div class="modal fade custom-modals" id="invoice_view_{{ $invoice->id }}">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">View Invoice</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="prescribe-download">
                        <h5>{{ ($invoice->generated_at ?? $invoice->created_at)->format('d M Y') }}</h5>
                        <ul>
                            <li><a href="{{ route('appointments.invoice.download', $invoice->appointment_id) }}" class="btn btn-primary prime-btn">Download</a></li>
                        </ul>
                    </div>
                    <div class="view-prescribe invoice-content">
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="invoice-details">
                                        <strong>Invoice No : </strong> #{{ $invoice->invoice_number }}<br>
                                        <strong>Appointment : </strong> #{{ $invoice->appointment->appointment_number }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="invoice-details">
                                        <strong>Status : </strong> {{ ucfirst($invoice->status) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-{{ $payment ? '4' : '6' }}">
                                    <div class="invoice-info">
                                        <h6 class="customer-text">Billing From</h6>
                                        <p class="invoice-details invoice-details-two">
                                            {{ 'Dr '.(auth()->user()->display_name ?: trim(auth()->user()->first_name.' '.auth()->user()->last_name)) }} <br>
                                            @if ($invoice->appointment->clinic)
                                                {{ $invoice->appointment->clinic->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-{{ $payment ? '4' : '6' }}">
                                    <div class="invoice-info @unless($payment) invoice-info2 @endunless">
                                        <h6 class="customer-text">Billing To</h6>
                                        <p class="invoice-details invoice-details-two">
                                            {{ trim($invoice->appointment->first_name.' '.$invoice->appointment->last_name) }} <br>
                                            {{ $invoice->appointment->email }}
                                        </p>
                                    </div>
                                </div>
                                @if ($payment)
                                    <div class="col-md-4">
                                        <div class="invoice-info invoice-info2">
                                            <h6 class="customer-text">Payment Method</h6>
                                            <p class="invoice-details">
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} <br>
                                                @if ($payment->card_last_four)
                                                    &bull;&bull;&bull;&bull; {{ $payment->card_last_four }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item invoice-table-wrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Invoice Details</h6>
                                    <div class="table-responsive">
                                        <table class="invoice-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Duration</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($invoice->appointment->services as $line)
                                                    <tr>
                                                        <td>{{ $line->service_name }}</td>
                                                        <td>{{ $line->duration_minutes }} mins</td>
                                                        <td>${{ number_format((float) $line->price, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td>{{ $invoice->appointment->service_name ?: 'Consultation' }}</td>
                                                        <td>{{ $invoice->appointment->duration_minutes }} mins</td>
                                                        <td>${{ number_format((float) $invoice->appointment->consultation_fee, 2) }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4 ms-auto">
                                    <div class="table-responsive">
                                        <table class="invoice-table-two table">
                                            <tbody>
                                                <tr>
                                                    <th>Subtotal:</th>
                                                    <td><span>${{ number_format((float) $invoice->subtotal, 2) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Tax:</th>
                                                    <td><span>${{ number_format((float) $invoice->tax, 2) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Discount:</th>
                                                    <td><span>-${{ number_format((float) $invoice->discount, 2) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Total Amount:</th>
                                                    <td><span>${{ number_format((float) $invoice->total, 2) }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /View Invoice -->
@endforeach
@endsection
