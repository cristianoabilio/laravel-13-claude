@extends('doctor.doctor_master')
@section('doctor')
@php
    $patientName = $patient->display_name ?: trim($patient->first_name.' '.$patient->last_name);
    $age = $patient->date_of_birth?->age;
    $statusMeta = [
        \App\Enums\AppointmentStatus::Pending->value => 'badge-warning',
        \App\Enums\AppointmentStatus::Confirmed->value => 'badge-success',
        \App\Enums\AppointmentStatus::Cancelled->value => 'badge-danger',
        \App\Enums\AppointmentStatus::Completed->value => 'badge-success',
    ];
@endphp
<div class="appointment-patient">

    <div class="dashboard-header">
        <h3><a href="{{ route('doctor.patients') }}"><i class="fa-solid fa-arrow-left"></i> Patient Details</a></h3>
    </div>

    <div class="patient-wrap">
        <div class="patient-info">
            <img src="{{ $patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $patientName }}">
            <div class="user-patient">
                <h6>#P{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</h6>
                <h5>{{ $patientName }}</h5>
                <ul>
                    @if ($age !== null)
                        <li>Age : {{ $age }}</li>
                    @endif
                    @if ($patient->gender)
                        <li>{{ ucfirst($patient->gender) }}</li>
                    @endif
                    @if ($patient->blood_group)
                        <li>{{ $patient->blood_group }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="patient-book">
            <p><i class="isax isax-calendar-1"></i>Last Booking</p>
            <p>{{ $lastBooking ? \Illuminate\Support\Carbon::parse($lastBooking)->format('d M Y') : '-' }}</p>
        </div>
    </div>

    <!-- Appoitment Tabs -->
    <div class="appointment-tabs user-tab">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" href="#pat_appointments" data-bs-toggle="tab">Appointments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#prescription" data-bs-toggle="tab">Prescription</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#medical" data-bs-toggle="tab">Medical Records</a>
            </li>
        </ul>
    </div>
    <!-- /Appoitment Tabs -->

    <div class="tab-content pt-0">

        <!-- Appointment Tab -->
        <div id="pat_appointments" class="tab-pane fade show active">
            @if ($appointments->isEmpty())
                <p class="text-center mb-0 py-4">No appointments found for this patient.</p>
            @else
                <div class="custom-table">
                    <div class="table-responsive">
                        <table class="table table-center mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Doctor</th>
                                    <th>Appt Date</th>
                                    <th>Booking Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>#{{ $appointment->appointment_number }}</td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <span class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-3" src="{{ $appointment->doctor->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg') }}" alt="User Image">
                                                </span>
                                                {{ 'Dr '.($appointment->doctor->display_name ?: trim($appointment->doctor->first_name.' '.$appointment->doctor->last_name)) }}
                                            </h2>
                                        </td>
                                        <td>{{ $appointment->appointment_date->format('d M Y') }}</td>
                                        <td>{{ $appointment->created_at->format('d M Y') }}</td>
                                        <td>${{ number_format((float) $appointment->total_amount, 2) }}</td>
                                        <td><span class="badge {{ $statusMeta[$appointment->status->value] }} status-badge">{{ $appointment->status->label() }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <!-- /Appointment Tab -->

        <!-- Prescription Tab -->
        <div class="tab-pane fade" id="prescription">
            <div class="search-header">
                <div></div>
                <div>
                    <a href="javascript:void(0);" class="btn btn-primary prime-btn" data-bs-toggle="modal" data-bs-target="#add_prescription">Add New Prescription</a>
                </div>
            </div>

            @if ($prescriptions->isEmpty())
                <p class="text-center mb-0 py-4">No prescriptions have been added for this patient yet.</p>
            @else
                <div class="custom-table">
                    <div class="table-responsive">
                        <table class="table table-center mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Prescriped By</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prescriptions as $prescription)
                                    <tr>
                                        <td><a href="javascript:void(0);" class="text-blue-600" data-bs-toggle="modal" data-bs-target="#view_prescription_{{ $prescription->id }}">#{{ $prescription->prescription_number }}</a></td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <span class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-3" src="{{ $prescription->doctor->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg') }}" alt="User Image">
                                                </span>
                                                {{ 'Dr '.($prescription->doctor->display_name ?: trim($prescription->doctor->first_name.' '.$prescription->doctor->last_name)) }}
                                            </h2>
                                        </td>
                                        <td>{{ $prescription->issued_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="action-item">
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_prescription_{{ $prescription->id }}">
                                                    <i class="isax isax-link-2"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <!-- /Prescription Tab -->

        <!-- Medical Records Tab -->
        <div class="tab-pane fade" id="medical">
            @if ($medicalRecords->isEmpty())
                <p class="text-center mb-0 py-4">No medical records found for this patient.</p>
            @else
                <div class="custom-table">
                    <div class="table-responsive">
                        <table class="table table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medicalRecords as $record)
                                    <tr>
                                        <td>
                                            <span class="lab-icon">
                                                <span><i class="fa-solid fa-paperclip"></i></span>{{ $record->title }}
                                            </span>
                                        </td>
                                        <td>{{ $record->record_date->format('d M Y') }}</td>
                                        <td>{{ Str::limit($record->comments, 60) }}</td>
                                        <td>
                                            @if ($record->file_path)
                                                <div class="action-item">
                                                    <a href="{{ $record->file_url }}" target="_blank" rel="noopener">
                                                        <i class="isax isax-import"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <!-- /Medical Records Tab -->
    </div>
</div>

@foreach ($prescriptions as $prescription)
    <!--View Prescription -->
    <div class="modal fade custom-modals" id="view_prescription_{{ $prescription->id }}">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">View Prescription</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="prescribe-download">
                        <h5>{{ $prescription->issued_at->format('d M Y') }}</h5>
                    </div>
                    <div class="view-prescribe invoice-content">
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="invoice-details">
                                        <strong>Prescription ID :</strong> #{{ $prescription->prescription_number }} <br>
                                        <strong>Issued:</strong> {{ $prescription->issued_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="invoice-info">
                                        <h6 class="customer-text">Doctor Details</h6>
                                        <p class="invoice-details invoice-details-two">
                                            {{ 'Dr '.($prescription->doctor->display_name ?: trim($prescription->doctor->first_name.' '.$prescription->doctor->last_name)) }} <br>
                                            {{ $prescription->doctor->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="invoice-info invoice-info2">
                                        <h6 class="customer-text">Patient Details</h6>
                                        <p class="invoice-details">
                                            {{ $patientName }} <br>
                                            {{ $patient->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item invoice-table-wrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Prescription  Details</h6>
                                    <div class="table-responsive">
                                        <table class="invoice-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Medicine Name</th>
                                                    <th>Dosage</th>
                                                    <th>Frequency</th>
                                                    <th>Duration</th>
                                                    <th>Timings</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($prescription->items as $item)
                                                    <tr>
                                                        <td>{{ $item->medicine_name }}</td>
                                                        <td>{{ $item->dosage }}</td>
                                                        <td>{{ $item->frequency }}</td>
                                                        <td>{{ $item->duration }}</td>
                                                        <td>{{ $item->timings }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        @if ($prescription->other_information)
                            <div class="other-info">
                                <h4>Other information</h4>
                                <p class="text-muted mb-0">{{ $prescription->other_information }}</p>
                            </div>
                        @endif
                        @if ($prescription->follow_up)
                            <div class="other-info">
                                <h4>Follow Up</h4>
                                <p class="text-muted mb-0">{{ $prescription->follow_up }}</p>
                            </div>
                        @endif
                        <div class="prescriber-info">
                            <h6>{{ 'Dr '.($prescription->doctor->display_name ?: trim($prescription->doctor->first_name.' '.$prescription->doctor->last_name)) }}</h6>
                            <p>{{ $prescription->doctor->designation }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /View Prescription -->
@endforeach

<!-- Add Prescription -->
<div class="modal fade custom-modals" id="add_prescription">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add Prescription</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form action="{{ route('doctor.prescriptions.store', $patient) }}" method="POST" id="add-prescription-form">
                @csrf
                <input type="hidden" name="form_context" value="add_prescription">
                <div class="modal-body">
                    <div class="patient-wrap">
                        <div class="patient-info mt-0">
                            <img src="{{ $patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $patientName }}">
                            <div class="user-patient">
                                <h6>#P{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</h6>
                                <h5>{{ $patientName }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="add-info" id="prescription-items">
                        <div class="row prescripe-cont" data-item-row>
                            <div class="col-xl-2 xol-lg-3 col-md-6">
                                <div class="form-wrap">
                                    <label class="col-form-label">Medicine Name <span class="text-danger">*</span></label>
                                    <input type="text" name="items[0][medicine_name]" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2 xol-lg-3 col-md-6">
                                <div class="form-wrap">
                                    <label class="col-form-label">Dosage</label>
                                    <input type="text" name="items[0][dosage]" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2 xol-lg-3 col-md-6">
                                <div class="form-wrap">
                                    <label class="col-form-label">Frequency</label>
                                    <input type="text" name="items[0][frequency]" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2 xol-lg-3 col-md-6">
                                <div class="form-wrap">
                                    <label class="col-form-label">Duration</label>
                                    <input type="text" name="items[0][duration]" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2 xol-lg-3 col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="form-wrap w-100">
                                        <label class="col-form-label">Timings</label>
                                        <input type="text" name="items[0][timings]" class="form-control" placeholder="e.g. Before Meal">
                                    </div>
                                    <div class="form-wrap ms-2">
                                        <label class="col-form-label d-block">&nbsp;</label>
                                        <a href="javascript:void(0);" class="trash remove-item"><i class="isax isax-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="javascript:void(0);" class="more-item" id="add-prescription-row">Add More</a>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Other Information</label>
                                <textarea name="other_information" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="col-form-label">Follow Up</label>
                                <textarea name="follow_up" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-btn text-end">
                        <a href="javascript:void(0);" class="btn btn-gray" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Prescription -->

@if ($errors->any() && old('form_context'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById(@json(old('form_context')));
            if (modalEl && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    </script>
@endif
@endsection
