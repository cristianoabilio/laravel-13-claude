@extends('frontend.home_master')
@section('home')
@php
    $selectedServices = array_map('intval', (array) request()->query('services', []));
    $selectedGenders = (array) request()->query('gender', []);
    $selectedExperience = array_map('intval', (array) request()->query('experience', []));
    $isAvailableOnly = request()->boolean('available');
    $hasActiveFilters = request()->query('search')
        || $selectedServices !== []
        || $selectedGenders !== []
        || $selectedExperience !== []
        || $isAvailableOnly
        || request()->filled('min_price')
        || request()->filled('max_price');
@endphp

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="isax isax-home-15"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Specialities</li>
                        <li class="breadcrumb-item active">{{ $speciality->name }}</li>
                    </ol>
                    <h2 class="breadcrumb-title">{{ $speciality->name }} Doctors</h2>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<div class="content mt-5">
    <div class="container">
        <div class="row">
            <div class="col-xl-3">
                <div class="card filter-lists">
                    <div class="card-header">
                        <div class="d-flex align-items-center filter-head justify-content-between">
                            <h4>Filter</h4>
                            @if ($hasActiveFilters)
                                <a href="{{ route('doctor.all.speciality', $speciality) }}" class="text-secondary text-decoration-underline">Clear All</a>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('doctor.all.speciality', $speciality) }}" method="GET">
                        <div class="card-body p-0">
                            <div class="accordion-item border-bottom">
                                <div class="accordion-body pt-3">
                                    <div class="filter-input">
                                        <div class="position-relative input-icon">
                                            <input type="text" name="search" class="form-control" placeholder="Search doctor" value="{{ request()->query('search') }}">
                                            <span><i class="isax isax-search-normal-1"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($services->isNotEmpty())
                                <div class="accordion-item border-bottom">
                                    <div class="accordion-header">
                                        <div class="d-flex align-items-center w-100 pt-3 px-3">
                                            <h5>Services</h5>
                                        </div>
                                    </div>
                                    <div class="accordion-body pt-3">
                                        @foreach ($services as $service)
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="services[]" value="{{ $service->id }}" id="service-{{ $service->id }}" @checked(in_array($service->id, $selectedServices))>
                                                    <label class="form-check-label" for="service-{{ $service->id }}">
                                                        {{ $service->name }}
                                                    </label>
                                                </div>
                                                <span class="filter-badge">{{ $service->doctor_services_count }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="accordion-item border-bottom">
                                <div class="accordion-header">
                                    <div class="d-flex align-items-center w-100 pt-3 px-3">
                                        <h5>Gender</h5>
                                    </div>
                                </div>
                                <div class="accordion-body pt-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="gender[]" value="male" id="gender-male" @checked(in_array('male', $selectedGenders))>
                                            <label class="form-check-label" for="gender-male">Male</label>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="gender[]" value="female" id="gender-female" @checked(in_array('female', $selectedGenders))>
                                            <label class="form-check-label" for="gender-female">Female</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-bottom">
                                <div class="accordion-header">
                                    <div class="d-flex align-items-center w-100 pt-3 px-3">
                                        <h5>Availability</h5>
                                    </div>
                                </div>
                                <div class="accordion-body pt-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="available" value="1" id="available-now" @checked($isAvailableOnly)>
                                            <label class="form-check-label" for="available-now">Available Now</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($maxPrice > 0)
                                <div class="accordion-item border-bottom">
                                    <div class="accordion-header">
                                        <div class="d-flex align-items-center w-100 pt-3 px-3">
                                            <h5>Pricing</h5>
                                        </div>
                                    </div>
                                    <div class="accordion-body pt-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="number" step="0.01" min="0" name="min_price" class="form-control" placeholder="Min" value="{{ request()->query('min_price') }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" step="0.01" min="0" name="max_price" class="form-control" placeholder="Max" value="{{ request()->query('max_price') }}">
                                            </div>
                                        </div>
                                        <p class="mb-0 mt-2">Range : ${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="accordion-item border-bottom">
                                <div class="accordion-header">
                                    <div class="d-flex align-items-center w-100 pt-3 px-3">
                                        <h5>Experience</h5>
                                    </div>
                                </div>
                                <div class="accordion-body pt-3">
                                    @foreach ([2 => '2+ Years', 5 => '5+ Years', 10 => '10+ Years'] as $years => $label)
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="experience[]" value="{{ $years }}" id="experience-{{ $years }}" @checked(in_array($years, $selectedExperience))>
                                                <label class="form-check-label" for="experience-{{ $years }}">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="p-3">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill">Apply Filters</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="showing-details d-flex align-items-center justify-content-between mb-4">
                    <p class="mb-0">Showing {{ $doctors->total() }} {{ Str::plural('Doctor', $doctors->total()) }} in {{ $speciality->name }}</p>
                </div>

                @if ($doctors->isEmpty())
                    <p class="text-center py-5">No doctors match these filters yet. Try adjusting or clearing them.</p>
                @else
                    <div class="row">
                        @foreach ($doctors as $doctor)
                            <div class="col-xxl-4 col-lg-6 mb-4">
                                @include('frontend.layouts.doctor-grid-card', ['doctor' => $doctor, 'favoritedDoctorIds' => $favoritedDoctorIds])
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        {{ $doctors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
