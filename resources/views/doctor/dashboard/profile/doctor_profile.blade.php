@extends('doctor.doctor_master');
@section('doctor')
<!-- Profile Settings -->
<div class="dashboard-header">
    <h3>Profile Settings</h3>
</div>

@include('doctor.dashboard.profile.menu_settings', ['activeTab' => 'profile'])

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="setting-title">
    <h5>Profile</h5>
</div>

<form action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="setting-card">
        <div class="change-avatar img-upload">
            <div class="profile-img">
                @if ($doctor->profile_photo_url)
                    <img src="{{ $doctor->profile_photo_url }}" alt="{{ $doctor->display_name }}">
                @else
                    <i class="fa-solid fa-file-image"></i>
                @endif
            </div>
            <div class="upload-img">
                <h5>Profile Image</h5>
                <div class="imgs-load d-flex align-items-center">
                    <div class="change-photo">
                        Upload New
                        <input type="file" name="profile_photo" class="upload" accept="image/jpeg,image/png">
                    </div>
                    @if ($doctor->profile_photo)
                        <a href="#" class="upload-remove" data-bs-toggle="modal" data-bs-target="#removePhotoModal">Remove</a>
                    @endif
                </div>
                <p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png. It will be resized to 360x360px.</p>
                @error('profile_photo')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="setting-title">
        <h5>Information</h5>
    </div>
    <div class="setting-card">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="form-wrap">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $doctor->first_name) }}">
                    @error('first_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-wrap">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $doctor->last_name) }}">
                    @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-wrap">
                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $doctor->display_name) }}">
                    @error('display_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-wrap">
                    <label class="form-label">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $doctor->designation) }}">
                    @error('designation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-wrap">
                    <label class="form-label">Phone Numbers <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone) }}">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-wrap">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="text" name="email" class="form-control" value="{{ old('email', $doctor->email) }}">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-wrap">
                    <label class="form-label">Known Languages <span class="text-danger">*</span></label>
                    <div class="input-block input-block-new mb-0">
                        <input class="input-tags form-control" id="inputBox3" type="text" data-role="tagsinput" placeholder="Type New" name="known_languages" value="{{ old('known_languages', implode(',', $doctor->known_languages ?? [])) }}">
                        <a href="#" class="input-text save-btn known-languages-save" data-url="{{ route('doctor.profile.languages.update') }}">Save</a>
                    </div>
                    <div class="known-languages-feedback form-text"></div>
                    @error('known_languages')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="setting-title">
        <h5>Memberships</h5>
    </div>
    <div class="setting-card">
        <div class="add-info membership-infos">
            @forelse ($doctor->memberships as $index => $membership)
                <div class="row membership-content">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-wrap">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="memberships[{{ $index }}][title]" class="form-control" placeholder="Add Title" value="{{ $membership->title }}">
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="form-wrap w-100">
                                <label class="form-label">About Membership</label>
                                <input type="text" name="memberships[{{ $index }}][description]" class="form-control" value="{{ $membership->description }}">
                            </div>
                            <div class="form-wrap ms-2">
                                <label class="col-form-label d-block">&nbsp;</label>
                                <a href="javascript:void(0);" class="trash-icon trash">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="row membership-content">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-wrap">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="memberships[0][title]" class="form-control" placeholder="Add Title">
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="form-wrap w-100">
                                <label class="form-label">About Membership</label>
                                <input type="text" name="memberships[0][description]" class="form-control">
                            </div>
                            <div class="form-wrap ms-2">
                                <label class="col-form-label d-block">&nbsp;</label>
                                <a href="javascript:void(0);" class="trash-icon trash">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        @error('memberships.*.title')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="text-end">
            <a href="#" class="add-membership-info more-item" data-next-index="{{ max($doctor->memberships->count(), 1) }}">Add New</a>
        </div>
    </div>

    <div class="modal-btn text-end">
        <a href="{{ route('doctor.profile') }}" class="btn btn-gray">Cancel</a>
        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
    </div>

</form>

@if ($doctor->profile_photo)
    <!-- Remove Photo Modal -->
    <div class="modal fade" id="removePhotoModal" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Remove Profile Image</h4>
                        <p class="mb-4">Are you sure you want to remove your profile image?</p>
                        <form action="{{ route('doctor.profile.photo.destroy') }}" method="POST">
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
    <!-- /Remove Photo Modal -->
@endif
<!-- /Profile Settings -->
@endsection
