@extends('doctor.doctor_master')
@section('doctor')
<!-- Profile Settings -->
<div class="dashboard-header">
    <h3>Profile Settings</h3>
</div>

@include('doctor.dashboard.profile.menu_settings', ['activeTab' => 'experience'])

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="dashboard-header border-0 mb-0">
    <h3>Experience</h3>
    <ul>
        <li>
            <a href="#" class="btn btn-primary prime-btn add-experiences" data-next-index="{{ max($doctor->experiences->count(), 1) }}">Add New  Experience</a>
        </li>
    </ul>
</div>

<form action="{{ route('doctor.experiences.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="accordions experience-infos" id="list-accord">

        @forelse ($doctor->experiences as $index => $experience)
            <!-- Experience Item -->
            <div class="user-accordion-item">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#experience-{{ $experience->id }}">
                    {{ $experience->hospital }}
                    <span class="trash delete-trigger" data-target="deleteExperienceModal{{ $experience->id }}">Delete</span>
                </a>
                <div class="accordion-collapse collapse show" id="experience-{{ $experience->id }}" data-bs-parent="#list-accord">
                    <div class="content-collapse">
                        <div class="add-service-info">
                            <div class="add-info">
                                <div class="row align-items-center">
                                    <input type="hidden" name="experiences[{{ $index }}][id]" value="{{ $experience->id }}">
                                    <div class="col-md-12">
                                        <div class="form-wrap mb-2">
                                            <div class="change-avatar img-upload">
                                                <div class="profile-img">
                                                    @if ($experience->hospital_logo_url)
                                                        <img src="{{ $experience->hospital_logo_url }}" alt="{{ $experience->hospital }}">
                                                    @else
                                                        <i class="fa-solid fa-file-image"></i>
                                                    @endif
                                                </div>
                                                <div class="upload-img">
                                                    <h5>Hospital Logo</h5>
                                                    <div class="imgs-load d-flex align-items-center">
                                                        <div class="change-photo">
                                                            Upload New
                                                            <input type="file" name="experiences[{{ $index }}][hospital_logo]" class="upload" accept="image/jpeg,image/png">
                                                        </div>
                                                        @if ($experience->hospital_logo)
                                                            <a href="#" class="upload-remove" data-bs-toggle="modal" data-bs-target="#removeLogoModal{{ $experience->id }}">Remove</a>
                                                        @else
                                                            <a href="#" class="upload-remove logo-remove-local">Remove</a>
                                                        @endif
                                                    </div>
                                                    <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>
                                                    @error('experiences.'.$index.'.hospital_logo')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Title</label>
                                            <input type="text" name="experiences[{{ $index }}][title]" class="form-control" value="{{ old('experiences.'.$index.'.title', $experience->title) }}">
                                            @error('experiences.'.$index.'.title')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Hospital <span class="text-danger">*</span></label>
                                            <input type="text" name="experiences[{{ $index }}][hospital]" class="form-control" value="{{ old('experiences.'.$index.'.hospital', $experience->hospital) }}">
                                            @error('experiences.'.$index.'.hospital')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Year of Experience <span class="text-danger">*</span></label>
                                            <input type="text" inputmode="numeric" name="experiences[{{ $index }}][years_of_experience]" class="form-control years-mask" value="{{ old('experiences.'.$index.'.years_of_experience', $experience->years_of_experience) }}">
                                            @error('experiences.'.$index.'.years_of_experience')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                            <input type="text" name="experiences[{{ $index }}][location]" class="form-control" value="{{ old('experiences.'.$index.'.location', $experience->location) }}">
                                            @error('experiences.'.$index.'.location')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Employement </label>
                                            <select class="select" name="experiences[{{ $index }}][employment_type]">
                                                <option value="">Select</option>
                                                @foreach (\App\Enums\EmploymentType::cases() as $employmentType)
                                                    <option value="{{ $employmentType->value }}" @selected(old('experiences.'.$index.'.employment_type', $experience->employment_type?->value) === $employmentType->value)>{{ $employmentType->label() }}</option>
                                                @endforeach
                                            </select>
                                            @error('experiences.'.$index.'.employment_type')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Job Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="3" name="experiences[{{ $index }}][job_description]">{{ old('experiences.'.$index.'.job_description', $experience->job_description) }}</textarea>
                                            @error('experiences.'.$index.'.job_description')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="experiences[{{ $index }}][start_date]" class="form-control datetimepicker" autocomplete="off" value="{{ old('experiences.'.$index.'.start_date', $experience->start_date?->format('d/m/Y')) }}">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                            @error('experiences.'.$index.'.start_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="experiences[{{ $index }}][end_date]" class="form-control datetimepicker end-date" autocomplete="off" value="{{ old('experiences.'.$index.'.end_date', $experience->end_date?->format('d/m/Y')) }}" @disabled($experience->currently_working)>
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                            @error('experiences.'.$index.'.end_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">&nbsp;</label>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input currently-working" type="checkbox" name="experiences[{{ $index }}][currently_working]" value="1" @checked(old('experiences.'.$index.'.currently_working', $experience->currently_working))> I Currently Working Here
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Experience Item -->
        @empty
            <!-- Experience Item (new) -->
            <div class="user-accordion-item experience-content">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#experience-new-0">Experience</a>
                <div class="accordion-collapse collapse show" id="experience-new-0" data-bs-parent="#list-accord">
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
                                                    <h5>Hospital Logo</h5>
                                                    <div class="imgs-load d-flex align-items-center">
                                                        <div class="change-photo">
                                                            Upload New
                                                            <input type="file" name="experiences[0][hospital_logo]" class="upload" accept="image/jpeg,image/png">
                                                        </div>
                                                        <a href="#" class="upload-remove logo-remove-local">Remove</a>
                                                    </div>
                                                    <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Title</label>
                                            <input type="text" name="experiences[0][title]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Hospital <span class="text-danger">*</span></label>
                                            <input type="text" name="experiences[0][hospital]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Year of Experience <span class="text-danger">*</span></label>
                                            <input type="text" inputmode="numeric" name="experiences[0][years_of_experience]" class="form-control years-mask">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                            <input type="text" name="experiences[0][location]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Employement </label>
                                            <select class="select" name="experiences[0][employment_type]">
                                                <option value="">Select</option>
                                                @foreach (\App\Enums\EmploymentType::cases() as $employmentType)
                                                    <option value="{{ $employmentType->value }}">{{ $employmentType->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Job Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="3" name="experiences[0][job_description]"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="experiences[0][start_date]" class="form-control datetimepicker" autocomplete="off">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                            <div class="form-icon">
                                                <input type="text" name="experiences[0][end_date]" class="form-control datetimepicker end-date" autocomplete="off">
                                                <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">&nbsp;</label>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input currently-working" type="checkbox" name="experiences[0][currently_working]" value="1"> I Currently Working Here
                                                </label>
                                            </div>
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
            <!-- /Experience Item (new) -->
        @endforelse

    </div>

    <div class="modal-btn text-end">
        <a href="{{ route('doctor.experience') }}" class="btn btn-gray">Cancel</a>
        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
    </div>

</form>

@foreach ($doctor->experiences as $experience)
    <!-- Delete Experience Modal -->
    <div class="modal fade" id="deleteExperienceModal{{ $experience->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete Experience</h4>
                        <p class="mb-4">Are you sure you want to delete this experience?</p>
                        <form action="{{ route('doctor.experiences.destroy', $experience) }}" method="POST">
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
    <!-- /Delete Experience Modal -->

    @if ($experience->hospital_logo)
        <!-- Remove Logo Modal -->
        <div class="modal fade" id="removeLogoModal{{ $experience->id }}" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-content p-2">
                            <h4 class="modal-title">Remove Hospital Logo</h4>
                            <p class="mb-4">Are you sure you want to remove this hospital logo?</p>
                            <form action="{{ route('doctor.experiences.logo.destroy', $experience) }}" method="POST">
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
