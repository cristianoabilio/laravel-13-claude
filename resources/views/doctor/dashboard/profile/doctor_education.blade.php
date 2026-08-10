@extends('doctor.doctor_master')
@section('doctor')
<!-- Profile Settings -->
<div class="dashboard-header">
    <h3>Profile Settings</h3>
</div>

@include('doctor.dashboard.profile.menu_settings', ['activeTab' => 'education'])

<div class="dashboard-header border-0 mb-0">
    <h3>Education</h3>
    <ul>
        <li>
            <a href="#" class="btn btn-primary prime-btn add-educations">Add New  Education</a>
        </li>
    </ul>
</div>

<form action="doctor-education-settings.html">
    <div class="accordions education-infos" id="list-accord">

        <!-- Education Item -->
        <div class="user-accordion-item">
            <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#education1">Education<span>Delete</span></a>
            <div class="accordion-collapse collapse show" id="education1" data-bs-parent="#list-accord">
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
                                                        <input type="file" class="upload">
                                                    </div>
                                                    <a href="#" class="upload-remove">Remove</a>
                                                </div>
                                                <p class="form-text">Your Image should Below 4 MB, Accepted format jpg,png,svg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Name of the institution</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Course</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                        <div class="form-icon">
                                            <input type="text" class="form-control datetimepicker">
                                            <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                        <div class="form-icon">
                                            <input type="text" class="form-control datetimepicker">
                                            <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">No of Years <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="3"></textarea>
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
        <!-- /Education Item -->

        <!-- Education Item -->
        <div class="user-accordion-item">
            <a href="#" class="collapsed accordion-wrap" data-bs-toggle="collapse" data-bs-target="#education2">Cambridge (MBBS)<span>Delete</span></a>
            <div class="accordion-collapse collapse" id="education2" data-bs-parent="#list-accord">
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
                                                        <input type="file" class="upload">
                                                    </div>
                                                    <a href="#" class="upload-remove">Remove</a>
                                                </div>
                                                <p class="form-text">Your Image should Below 4 MB, Accepted format jpg,png,svg</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Name of the institution</label>
                                        <input type="text" class="form-control" value="Cambridge">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Course</label>
                                        <input type="text" class="form-control" value="MBBS">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Start Date <span class="text-danger">*</span></label>
                                        <div class="form-icon">
                                            <input type="text" class="form-control datetimepicker" value="12-6-2000">
                                            <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">End Date <span class="text-danger">*</span></label>
                                        <div class="form-icon">
                                            <input type="text" class="form-control datetimepicker" value="09-05-2005">
                                            <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-wrap">
                                        <label class="col-form-label">No of Years <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="5">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-wrap">
                                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="3"></textarea>
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
        <!-- /Education Item -->

    </div>

    <div class="modal-btn text-end">
        <a href="#" class="btn btn-gray">Cancel</a>
        <button class="btn btn-primary prime-btn">Save Changes</button>
    </div>
</form>
<!-- /Profile Settings -->
@endsection