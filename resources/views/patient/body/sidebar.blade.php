<div class="col-lg-4 col-xl-3 theiaStickySidebar">

    <!-- Profile Sidebar -->
    <div class="profile-sidebar patient-sidebar profile-sidebar-new">
        <div class="widget-profile pro-widget-content">
            <div class="profile-info-widget">
                <a href="{{ route('patient.settings') }}" class="booking-doc-img">
                    <img src="{{ auth()->user()->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-06.jpg') }}" alt="User Image">
                </a>
                <div class="profile-det-info">
                    <h3><a href="{{ route('patient.settings') }}">{{ trim(auth()->user()->first_name.' '.auth()->user()->last_name) }}</a></h3>
                    <div class="patient-details">
                        <h5 class="mb-0">Patient ID : {{ auth()->user()->patient_id }}</h5>
                    </div>
                    @if (auth()->user()->gender || auth()->user()->age)
                        <span>
                            {{ ucfirst(auth()->user()->gender ?? '') }}
                            @if (auth()->user()->gender && auth()->user()->age)
                                <i class="fa-solid fa-circle"></i>
                            @endif
                            {{ auth()->user()->age }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="dashboard-widget">
            <nav class="dashboard-menu">
                <ul>
                    <li class="active">
                        <a href="patient-dashboard.html">
                            <i class="isax isax-category-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="patient-appointments.html">
                            <i class="isax isax-calendar-1"></i>
                            <span>My Appointments</span>
                        </a>
                    </li>
                    <li>
                        <a href="favourites.html">
                            <i class="isax isax-star-1"></i>
                            <span>Favourites</span>
                        </a>
                    </li>
                    <li>
                        <a href="dependent.html">
                            <i class="isax isax-user-octagon"></i>
                            <span>Dependants</span>
                        </a>
                    </li>
                    <li>
                        <a href="medical-records.html">
                            <i class="isax isax-note-21"></i>
                            <span>Medical Records</span>
                        </a>
                    </li>
                    <li>
                        <a href="patient-accounts.html">
                            <i class="isax isax-wallet-2"></i>
                            <span>Wallet</span>
                        </a>
                    </li>
                    <li>
                        <a href="patient-invoices.html">
                            <i class="isax isax-document-text"></i>
                            <span>Invoices</span>
                        </a>
                    </li>
                    <li>
                        <a href="chat.html">
                            <i class="isax isax-messages-1"></i>
                            <span>Message</span>
                            <small class="unread-msg">7</small>
                        </a>
                    </li>
                    <li>
                        <a href="medical-details.html">
                            <i class="isax isax-note-1"></i>
                            <span>Vitals</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('patient.settings') }}">
                            <i class="isax isax-setting-2"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('patient.logout') }}">
                            <i class="isax isax-logout"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- /Profile Sidebar -->

</div>