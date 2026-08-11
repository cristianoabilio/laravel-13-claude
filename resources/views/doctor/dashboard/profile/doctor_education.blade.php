@extends('doctor.doctor_master')
@section('doctor')
<!-- Profile Settings -->
<div class="dashboard-header">
    <h3>Profile Settings</h3>
</div>

@include('doctor.dashboard.profile.menu_settings', ['activeTab' => 'education'])

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="dashboard-header border-0 mb-0">
    <h3>Education</h3>
    <ul>
        <li>
            <a href="#" class="btn btn-primary prime-btn add-educations" data-next-index="{{ max($doctor->educations->count(), 1) }}">Add New  Education</a>
        </li>
    </ul>
</div>

<form action="{{ route('doctor.educations.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="accordions education-infos" id="list-accord">

        @forelse ($doctor->educations as $index => $education)
            <!-- Education Item -->
            <div class="user-accordion-item">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#education-{{ $education->id }}">
                    {{ $education->institution ?: 'Education' }}
                    <span class="trash delete-trigger" data-target="deleteEducationModal{{ $education->id }}">Delete</span>
                </a>
                <div class="accordion-collapse collapse show" id="education-{{ $education->id }}" data-bs-parent="#list-accord">
                    <div class="content-collapse">
                        <div class="add-service-info">
                            <div class="add-info">
                                <div class="row align-items-center">
                                    <input type="hidden" name="educations[{{ $index }}][id]" value="{{ $education->id }}">
                                    <div class="col-md-12">
                                        <div class="form-wrap mb-2">
                                            <div class="change-avatar img-upload">
                                                <div class="profile-img">
                                                    @if ($education->logo_url)
                                                        <img src="{{ $education->logo_url }}" alt="{{ $education->institution }}">
                                                    @else
                                                        <i class="fa-solid fa-file-image"></i>
                                                    @endif
                                                </div>
                                                <div class="upload-img">
                                                    <h5>Logo</h5>
                                                    <div class="imgs-load d-flex align-items-center">
                                                        <div class="change-photo">
                                                            Upload New
                                                            <input type="file" name="educations[{{ $index }}][logo]" class="upload" accept="image/jpeg,image/png">
                                                        </div>
                                                        @if ($education->logo)
                                                            <a href="#" class="upload-remove" data-bs-toggle="modal" data-bs-target="#removeLogoModal{{ $education->id }}">Remove</a>
                                                        @else
                                                            <a href="#" class="upload-remove logo-remove-local">Remove</a>
                                                        @endif
                                                    </div>
                                                    <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>
                                                    @error('educations.'.$index.'.logo')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Name of the institution</label>
                                            <input type="text" name="educations[{{ $index }}][institution]" class="form-control" value="{{ old('educations.'.$index.'.institution', $education->institution) }}">
                                            @error('educations.'.$index.'.institution')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Course</label>
                                            <input type="text" name="educations[{{ $index }}][course]" class="form-control" value="{{ old('educations.'.$index.'.course', $education->course) }}">
                                            @error('educations.'.$index.'.course')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="educations[{{ $index }}][start_date]" class="form-control datetimepicker" autocomplete="off" value="{{ old('educations.'.$index.'.start_date', $education->start_date?->format('d/m/Y')) }}">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                            @error('educations.'.$index.'.start_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="educations[{{ $index }}][end_date]" class="form-control datetimepicker" autocomplete="off" value="{{ old('educations.'.$index.'.end_date', $education->end_date?->format('d/m/Y')) }}">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                            @error('educations.'.$index.'.end_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">No of Years <span class="text-danger">*</span></label>
                                            <input type="text" inputmode="numeric" name="educations[{{ $index }}][no_of_years]" class="form-control years-mask" value="{{ old('educations.'.$index.'.no_of_years', $education->no_of_years) }}">
                                            @error('educations.'.$index.'.no_of_years')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="3" name="educations[{{ $index }}][description]">{{ old('educations.'.$index.'.description', $education->description) }}</textarea>
                                            @error('educations.'.$index.'.description')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Education Item -->
        @empty
            <!-- Education Item (new) -->
            <div class="user-accordion-item education-content">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#education-new-0">Education</a>
                <div class="accordion-collapse collapse show" id="education-new-0" data-bs-parent="#list-accord">
                    <div class="content-collapse">
                        <div class="add-service-info">
                            <div class="add-info">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="form-wrap mb-2">
                                            <div class="change-avatar img-upload">
                                                <div class="profile-img">
                                                    <i class="fa-solid fa-file-image"></i>
                                                </div>
                                                <div class="upload-img">
                                                    <h5>Logo</h5>
                                                    <div class="imgs-load d-flex align-items-center">
                                                        <div class="change-photo">
                                                            Upload New
                                                            <input type="file" name="educations[0][logo]" class="upload" accept="image/jpeg,image/png">
                                                        </div>
                                                        <a href="#" class="upload-remove logo-remove-local">Remove</a>
                                                    </div>
                                                    <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Name of the institution</label>
                                            <input type="text" name="educations[0][institution]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Course</label>
                                            <input type="text" name="educations[0][course]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="educations[0][start_date]" class="form-control datetimepicker" autocomplete="off">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="educations[0][end_date]" class="form-control datetimepicker" autocomplete="off">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">No of Years <span class="text-danger">*</span></label>
                                            <input type="text" inputmode="numeric" name="educations[0][no_of_years]" class="form-control years-mask">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="3" name="educations[0][description]"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="#" class="reset more-item">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Education Item (new) -->
        @endforelse

    </div>

    <div class="modal-btn text-end">
        <a href="{{ route('doctor.education') }}" class="btn btn-gray">Cancel</a>
        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
    </div>

</form>

@foreach ($doctor->educations as $education)
    <!-- Delete Education Modal -->
    <div class="modal fade" id="deleteEducationModal{{ $education->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete Education</h4>
                        <p class="mb-4">Are you sure you want to delete this education entry?</p>
                        <form action="{{ route('doctor.educations.destroy', $education) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary">Delete</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Education Modal -->

    @if ($education->logo)
        <!-- Remove Logo Modal -->
        <div class="modal fade" id="removeLogoModal{{ $education->id }}" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-content p-2">
                            <h4 class="modal-title">Remove Logo</h4>
                            <p class="mb-4">Are you sure you want to remove this logo?</p>
                            <form action="{{ route('doctor.educations.logo.destroy', $education) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary">Remove</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Remove Logo Modal -->
    @endif
@endforeach

<!-- /Profile Settings -->
@endsection
