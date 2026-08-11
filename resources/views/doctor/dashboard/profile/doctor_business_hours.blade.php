@extends('doctor.doctor_master')
@section('doctor')
<!-- Profile Settings -->
<div class="dashboard-header">
    <h3>Profile Settings</h3>
</div>

@include('doctor.dashboard.profile.menu_settings', ['activeTab' => 'business'])

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('doctor.business.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="business-wrap">
        <h4>Select Business days</h4>
        <ul class="business-nav">
            @foreach ($businessHours as $businessHour)
                <li>
                    <a class="tab-link @if(old('business_hours.'.$businessHour->day->value.'.is_open', $businessHour->is_open)) active @endif" data-tab="day-{{ $businessHour->day->value }}">{{ $businessHour->day->label() }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="accordions business-info" id="list-accord">

        @foreach ($businessHours as $businessHour)
            @php $isOpen = old('business_hours.'.$businessHour->day->value.'.is_open', $businessHour->is_open); @endphp
            <!-- Business Hours -->
            <div class="user-accordion-item tab-items @if($isOpen) active @endif" id="day-{{ $businessHour->day->value }}">
                <a href="#" class="accordion-wrap @if($businessHour->day !== \App\Enums\DayOfWeek::Monday) collapsed @endif" data-bs-toggle="collapse" data-bs-target="#{{ $businessHour->day->value }}">{{ $businessHour->day->label() }}<span class="edit">Edit</span></a>
                <div class="accordion-collapse collapse @if($businessHour->day === \App\Enums\DayOfWeek::Monday) show @endif" id="{{ $businessHour->day->value }}" data-bs-parent="#list-accord">
                    <div class="content-collapse pb-0">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="form-wrap">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input day-open-toggle" type="checkbox" name="business_hours[{{ $businessHour->day->value }}][is_open]" value="1" @checked($isOpen)> Open on {{ $businessHour->day->label() }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-wrap">
                                    <label class="col-form-label">From <span class="text-danger">*</span></label>
                                    <div class="form-icon">
                                        <input type="text" name="business_hours[{{ $businessHour->day->value }}][from]" class="form-control timepicker1" autocomplete="off" value="{{ old('business_hours.'.$businessHour->day->value.'.from', $businessHour->from_time?->format('h:i A')) }}" @disabled(! $isOpen)>
                                        <span class="icon"><i class="fa-solid fa-clock"></i></span>
                                    </div>
                                    @error('business_hours.'.$businessHour->day->value.'.from')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-wrap">
                                    <label class="col-form-label">To <span class="text-danger">*</span></label>
                                    <div class="form-icon">
                                        <input type="text" name="business_hours[{{ $businessHour->day->value }}][to]" class="form-control timepicker1" autocomplete="off" value="{{ old('business_hours.'.$businessHour->day->value.'.to', $businessHour->to_time?->format('h:i A')) }}" @disabled(! $isOpen)>
                                        <span class="icon"><i class="fa-solid fa-clock"></i></span>
                                    </div>
                                    @error('business_hours.'.$businessHour->day->value.'.to')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Business Hours -->
        @endforeach

    </div>

    <div class="modal-btn text-end">
        <a href="{{ route('doctor.business') }}" class="btn btn-gray">Cancel</a>
        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
    </div>

</form>
<!-- /Profile Settings -->
@endsection
