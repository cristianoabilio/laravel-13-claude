<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li @class(['active' => request()->routeIs('admin.dashboard')])>
                    <a href="{{ route('admin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>
                <li @class(['active' => request()->routeIs('admin.appointments.index')])>
                    <a href="{{ route('admin.appointments.index') }}"><i class="fe fe-layout"></i> <span>Appointments</span></a>
                </li>
                <li @class(['active' => request()->routeIs('admin.specialities.*')])>
                    <a href="{{ route('admin.specialities.index') }}"><i class="fe fe-users"></i> <span>Specialities</span></a>
                </li>
                <li @class(['active' => request()->routeIs('admin.doctors.index')])>
                    <a href="{{ route('admin.doctors.index') }}"><i class="fe fe-user-plus"></i> <span>Doctors</span></a>
                </li>
                <li @class(['active' => request()->routeIs('admin.patients.index')])>
                    <a href="{{ route('admin.patients.index') }}"><i class="fe fe-user"></i> <span>Patients</span></a>
                </li>
                <li @class(['active' => request()->routeIs('admin.reviews')])>
                    <a href="{{ route('admin.reviews') }}"><i class="fe fe-star-o"></i> <span>Reviews</span></a>
                </li>
                <li @class(['active' => request()->routeIs('admin.payout_requests.*')])>
                    <a href="{{ route('admin.payout_requests.index') }}"><i class="fe fe-activity"></i> <span>Payout Requests</span></a>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fe fe-document"></i> <span> Manage Home</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li @class(['active' => request()->routeIs('admin.home.banner.edit')])><a href="{{ route('admin.home.banner.edit') }}">Banner</a></li>
                        <li @class(['active' => request()->routeIs('admin.home.services.*')])><a href="{{ route('admin.home.services.index') }}">Services</a></li>
                        <li @class(['active' => request()->routeIs('admin.home.reasons.*')])><a href="{{ route('admin.home.reasons.index') }}">Reasons</a></li>
                        <li @class(['active' => request()->routeIs('admin.home.bookus.*')])><a href="{{ route('admin.home.bookus.index') }}">Book Us</a></li>
                        <li @class(['active' => request()->routeIs('admin.testimonials.*')])><a href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
                        <li @class(['active' => request()->routeIs('admin.faqs.*')])><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>