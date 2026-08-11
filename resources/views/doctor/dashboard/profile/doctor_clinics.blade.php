@extends('doctor.doctor_master')
@section('doctor')
<!-- Profile Settings -->
<div class="dashboard-header">
    <h3>Profile Settings</h3>
</div>

@include('doctor.dashboard.profile.menu_settings', ['activeTab' => 'clinics'])

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="dashboard-header border-0 mb-0">
    <h3>Clinics</h3>
    <ul>
        <li>
            <a href="#" class="btn btn-primary prime-btn add-clinics" data-next-index="{{ max($doctor->clinics->count(), 1) }}">Add New Clinic</a>
        </li>
    </ul>
</div>

<form action="{{ route('doctor.clinics.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="accordions clinic-infos" id="list-accord">

        @forelse ($doctor->clinics as $index => $clinic)
            <!-- Clinic Item -->
            <div class="user-accordion-item">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#clinic-{{ $clinic->id }}">
                    {{ $clinic->name ?: 'Clinic' }}
                    <span class="trash delete-trigger" data-target="deleteClinicModal{{ $clinic->id }}">Delete</span>
                </a>
                <div class="accordion-collapse collapse show" id="clinic-{{ $clinic->id }}" data-bs-parent="#list-accord">
                    <div class="content-collapse">
                        <div class="add-service-info">
                            <div class="add-info">
                                <div class="row align-items-center">
                                    <input type="hidden" name="clinics[{{ $index }}][id]" value="{{ $clinic->id }}">
                                    <div class="col-md-12">
                                        <div class="form-wrap mb-2">
                                            <div class="change-avatar img-upload">
                                                <div class="profile-img">
                                                    @if ($clinic->logo_url)
                                                        <img src="{{ $clinic->logo_url }}" alt="{{ $clinic->name }}">
                                                    @else
                                                        <i class="fa-solid fa-file-image"></i>
                                                    @endif
                                                </div>
                                                <div class="upload-img">
                                                    <h5>Logo</h5>
                                                    <div class="imgs-load d-flex align-items-center">
                                                        <div class="change-photo">
                                                            Upload New
                                                            <input type="file" name="clinics[{{ $index }}][logo]" class="upload" accept="image/jpeg,image/png">
                                                        </div>
                                                        @if ($clinic->logo)
                                                            <a href="#" class="upload-remove" data-bs-toggle="modal" data-bs-target="#removeLogoModal{{ $clinic->id }}">Remove</a>
                                                        @else
                                                            <a href="#" class="upload-remove logo-remove-local">Remove</a>
                                                        @endif
                                                    </div>
                                                    <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>
                                                    @error('clinics.'.$index.'.logo')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Clinic Name <span class="text-danger">*</span></label>
                                            <input type="text" name="clinics[{{ $index }}][name]" class="form-control" value="{{ old('clinics.'.$index.'.name', $clinic->name) }}">
                                            @error('clinics.'.$index.'.name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                            <input type="text" name="clinics[{{ $index }}][location]" class="form-control" value="{{ old('clinics.'.$index.'.location', $clinic->location) }}">
                                            @error('clinics.'.$index.'.location')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Address <span class="text-danger">*</span></label>
                                            <input type="text" name="clinics[{{ $index }}][address]" class="form-control" value="{{ old('clinics.'.$index.'.address', $clinic->address) }}">
                                            @error('clinics.'.$index.'.address')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Gallery</label>
                                            <div class="drop-file">
                                                <p>Drop files or Click to upload</p>
                                                <input type="file" name="clinics[{{ $index }}][gallery][]" class="gallery-input" accept="image/jpeg,image/png" multiple>
                                            </div>
                                            <div class="view-imgs">
                                                @foreach ($clinic->images as $image)
                                                    <div class="view-img">
                                                        <img src="{{ $image->image_url }}" alt="Gallery image">
                                                        <a href="#" class="gallery-remove-persisted" data-url="{{ route('doctor.clinics.images.destroy', $image) }}">Remove</a>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="form-text">Images will be resized to 300x300 and processed in the background - they may take a moment to appear.</p>
                                            @error('clinics.'.$index.'.gallery')
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
            <!-- /Clinic Item -->
        @empty
            <!-- Clinic Item (new) -->
            <div class="user-accordion-item clinic-content">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#clinic-new-0">Clinic</a>
                <div class="accordion-collapse collapse show" id="clinic-new-0" data-bs-parent="#list-accord">
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
                                                            <input type="file" name="clinics[0][logo]" class="upload" accept="image/jpeg,image/png">
                                                        </div>
                                                        <a href="#" class="upload-remove logo-remove-local">Remove</a>
                                                    </div>
                                                    <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Clinic Name <span class="text-danger">*</span></label>
                                            <input type="text" name="clinics[0][name]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                            <input type="text" name="clinics[0][location]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Address <span class="text-danger">*</span></label>
                                            <input type="text" name="clinics[0][address]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-wrap">
                                            <label class="col-form-label">Gallery</label>
                                            <div class="drop-file">
                                                <p>Drop files or Click to upload</p>
                                                <input type="file" name="clinics[0][gallery][]" class="gallery-input" accept="image/jpeg,image/png" multiple>
                                            </div>
                                            <div class="view-imgs"></div>
                                            <p class="form-text">Images will be resized to 300x300 and processed in the background - they may take a moment to appear.</p>
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
            <!-- /Clinic Item (new) -->
        @endforelse

    </div>

    <div class="modal-btn text-end">
        <a href="{{ route('doctor.clinics') }}" class="btn btn-gray">Cancel</a>
        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
    </div>

</form>

@foreach ($doctor->clinics as $clinic)
    <!-- Delete Clinic Modal -->
    <div class="modal fade" id="deleteClinicModal{{ $clinic->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete Clinic</h4>
                        <p class="mb-4">Are you sure you want to delete this clinic? Its gallery will be removed too.</p>
                        <form action="{{ route('doctor.clinics.destroy', $clinic) }}" method="POST">
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
    <!-- /Delete Clinic Modal -->

    @if ($clinic->logo)
        <!-- Remove Logo Modal -->
        <div class="modal fade" id="removeLogoModal{{ $clinic->id }}" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-content p-2">
                            <h4 class="modal-title">Remove Logo</h4>
                            <p class="mb-4">Are you sure you want to remove this logo?</p>
                            <form action="{{ route('doctor.clinics.logo.destroy', $clinic) }}" method="POST">
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

<!-- Remove Gallery Image Modal (shared by every gallery thumbnail) -->
<div class="modal fade" id="removeGalleryImageModal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-content p-2">
                    <h4 class="modal-title">Remove Image</h4>
                    <p class="mb-4">Are you sure you want to remove this gallery image?</p>
                    <form id="removeGalleryImageForm" action="" method="POST">
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
<!-- /Remove Gallery Image Modal -->

<!-- /Profile Settings -->
@endsection
