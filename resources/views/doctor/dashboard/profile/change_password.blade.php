@extends('doctor.doctor_master')
@section('doctor')
<div class="dashboard-header">
    <h3>Change Password</h3>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('doctor.change_password.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card pass-card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-block input-block-new">
                        <label class="form-label">Old Password</label>
                        <input type="password" class="form-control" name="current_password">
                        @error('current_password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-block input-block-new">
                        <label class="form-label">New Password</label>
                        <div class="pass-group">
                            <input type="password" class="form-control pass-input" name="password">
                            <span class="feather-eye-off toggle-password"></span>
                        </div>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-block input-block-new mb-0">
                        <label class="form-label">Confirm Password</label>
                        <div class="pass-group">
                            <input type="password" class="form-control pass-input-sub" name="password_confirmation">
                            <span class="feather-eye-off toggle-password-sub"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-set-button">
        <a href="{{ route('doctor.change_password') }}" class="btn btn-light">Cancel</a>
        <button class="btn btn-primary" type="submit">Save Changes</button>
    </div>
</form>
@endsection