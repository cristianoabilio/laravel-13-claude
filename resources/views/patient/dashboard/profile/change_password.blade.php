@extends('patient.patient_master')
@section('patient')
<!-- Change Password -->
<div class="col-lg-8 col-xl-9">
    @include('patient.dashboard.profile.menu_settings', ['activeTab' => 'change_password'])
    <div class="card">
        <div class="card-body">
            <div class="border-bottom pb-3 mb-3">
                <h5>Change Password</h5>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('patient.change_password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="pass-group">
                                <input type="password" class="form-control pass-input-sub" name="current_password">
                                <span class="feather-eye-off toggle-password-sub"></span>
                            </div>
                            @error('current_password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="pass-group">
                                <input type="password" class="form-control pass-input" name="password">
                                <span class="feather-eye-off toggle-password"></span>
                            </div>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="pass-group">
                                <input type="password" class="form-control pass-input-sub" name="password_confirmation">
                                <span class="feather-eye-off toggle-password-sub"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-btn border-top pt-3 text-end">
                    <a href="{{ route('patient.change_password') }}" class="btn btn-md btn-light rounded-pill">Cancel</a>
                    <button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Change Password -->
@endsection