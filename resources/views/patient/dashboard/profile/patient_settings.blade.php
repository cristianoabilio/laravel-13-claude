@extends('patient.patient_master')
@section('patient')
<div class="col-lg-8 col-xl-9">
    @include('patient.dashboard.profile.menu_settings', ['activeTab' => 'profile'])
    <div class="card">
        <div class="card-body">
            <div class="border-bottom pb-3 mb-3">
                <h5>Profile Settings</h5>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('patient.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="setting-card">
                    <label class="form-label mb-2">Profile Photo</label>
                    <div class="change-avatar img-upload">
                        <div class="profile-img">
                            @if ($patient->profile_photo_url)
                                <img src="{{ $patient->profile_photo_url }}" alt="{{ $patient->first_name }}">
                            @else
                                <i class="fa-solid fa-file-image"></i>
                            @endif
                        </div>
                        <div class="upload-img">
                            <div class="imgs-load d-flex align-items-center">
                                <div class="change-photo">
                                    Upload New
                                    <input type="file" name="profile_photo" class="upload" accept="image/jpeg,image/png">
                                </div>
                                @if ($patient->profile_photo)
                                    <a href="#" class="upload-remove" data-bs-toggle="modal" data-bs-target="#removePhotoModal">Remove</a>
                                @endif
                            </div>
                            <p>Your Image should Below 4 MB, Accepted format jpg,png. It will be resized to 360x360px.</p>
                            @error('profile_photo')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="setting-title">
                    <h6>Information</h6>
                </div>
                <div class="setting-card">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $patient->first_name) }}">
                                @error('first_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $patient->last_name) }}">
                                @error('last_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <div class="form-icon">
                                    <input type="text" name="date_of_birth" class="form-control datetimepicker" autocomplete="off" placeholder="dd/mm/yyyy" value="{{ old('date_of_birth', $patient->date_of_birth?->format('d/m/Y')) }}">
                                    <span class="icon"><i class="isax isax-calendar-1"></i></span>
                                </div>
                                @error('date_of_birth')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email) }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Blood Group <span class="text-danger">*</span></label>
                                @php $bloodGroup = old('blood_group', $patient->blood_group); @endphp
                                <select class="select" name="blood_group">
                                    <option value="" @selected(! $bloodGroup)>Select</option>
                                    @foreach (['A+ve', 'A-ve', 'B+ve', 'B-ve', 'AB+ve', 'AB-ve', 'O+ve', 'O-ve'] as $group)
                                        <option value="{{ $group }}" @selected($bloodGroup === $group)>{{ $group }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="setting-title">
                    <h6>Address</h6>
                </div>
                <div class="setting-card">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $patient->address) }}">
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $patient->city) }}">
                                @error('city')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" value="{{ old('state', $patient->state) }}">
                                @error('state')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', $patient->country) }}">
                                @error('country')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $patient->pincode) }}">
                                @error('pincode')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-btn text-end">
                    <a href="{{ route('patient.settings') }}" class="btn btn-md btn-light rounded-pill">Cancel</a>
                    <button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($patient->profile_photo)
    <!-- Remove Photo Modal -->
    <div class="modal fade" id="removePhotoModal" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Remove Profile Image</h4>
                        <p class="mb-4">Are you sure you want to remove your profile image?</p>
                        <form action="{{ route('patient.settings.photo.destroy') }}" method="POST">
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
@endsection
