<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="active">
                    <a href="{{ route('admin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.appointments.index') }}"><i class="fe fe-layout"></i> <span>Appointments</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.specialities.index') }}"><i class="fe fe-users"></i> <span>Specialities</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.doctors.index') }}"><i class="fe fe-user-plus"></i> <span>Doctors</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.patients.index') }}"><i class="fe fe-user"></i> <span>Patients</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.reviews') }}"><i class="fe fe-star-o"></i> <span>Reviews</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.payout_requests.index') }}"><i class="fe fe-activity"></i> <span>Payout Requests</span></a>
                </li>
                <li>
                    <a href="profile.html"><i class="fe fe-user-plus"></i> <span>Profile</span></a>
                </li>
            </ul>
        </div>
    </div>
</div>