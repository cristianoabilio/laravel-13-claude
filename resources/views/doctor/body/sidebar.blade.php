<div class="profile-sidebar doctor-sidebar profile-sidebar-new">
    <div class="widget-profile pro-widget-content">
        <div class="profile-info-widget">
            <a href="{{ route('doctor.profile') }}" class="booking-doc-img">
                <img src="{{ auth()->user()->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/doctor-profile-img.jpg') }}" alt="User Image">
            </a>
            <div class="profile-det-info">
                <h3><a href="{{ route('doctor.profile') }}">{{ auth()->user()->display_name ?: trim(auth()->user()->first_name.' '.auth()->user()->last_name) }}</a></h3>
                @if (auth()->user()->designation)
                    <div class="patient-details">
                        <h5 class="mb-0">{{ auth()->user()->designation }}</h5>
                    </div>
                @endif
                <span class="badge doctor-role-badge"><i class="fa-solid fa-circle"></i>{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
    </div>
    <div class="doctor-available-head">
        <div class="input-block input-block-new">
            <label class="form-label">Availability <span class="text-danger">*</span></label>
            <select class="select form-control">
                <option value="available" @selected(auth()->user()->availability_status === 'available')>I am Available Now</option>
                <option value="not_available" @selected(auth()->user()->availability_status === 'not_available')>Not Available</option>
            </select>
        </div>
    </div>
    <div class="dashboard-widget">
        <nav class="dashboard-menu">
            <ul>
                <li class="active">
                    <a href="{{ route('doctor.dashboard') }}">
                        <i class="isax isax-category-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="doctor-request.html">
                        <i class="isax isax-clipboard-tick"></i>
                        <span>Requests</span>
                        <small class="unread-msg">2</small>
                    </a>
                </li>
                <li>
                    <a href="appointments.html">
                        <i class="isax isax-calendar-1"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="available-timings.html">
                        <i class="isax isax-calendar-tick"></i>
                        <span>Available Timings</span>
                    </a>
                </li>
                <li>
                    <a href="my-patients.html">
                        <i class="fa-solid fa-user-injured"></i>
                        <span>My Patients</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('doctor.specialities') }}">
                        <i class="isax isax-clock"></i>
                        <span>Specialties & Services</span>
                    </a>
                </li>
                <li>
                    <a href="reviews.html">
                        <i class="isax isax-star-1"></i>
                        <span>Reviews</span>
                    </a>
                </li>
                <li>
                    <a href="accounts.html">
                        <i class="isax isax-profile-tick"></i>
                        <span>Accounts</span>
                    </a>
                </li>
                <li>
                    <a href="invoices.html">
                        <i class="isax isax-document-text"></i>
                        <span>Invoices</span>
                    </a>
                </li>
                <li>
                    <a href="doctor-payment.html">
                        <i class="fa-solid fa-money-bill-1"></i>
                        <span>Payout Settings</span>
                    </a>
                </li>
                <li>
                    <a href="chat-doctor.html">
                        <i class="isax isax-messages-1"></i>
                        <span>Message</span>
                        <small class="unread-msg">7</small>
                    </a>
                </li>
                <li>
                    <a href="{{ route('doctor.profile') }}">
                        <i class="isax isax-setting-2"></i>
                        <span>Profile Settings</span>
                    </a>
                </li>
                <li>
                    <a href="social-media.html">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Social Media</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('doctor.change_password') }}">
                        <i class="isax isax-key"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <i class="isax isax-logout"></i>
                        <span>Logout</span>
                    </a>
                    <form id="sidebar-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</div>