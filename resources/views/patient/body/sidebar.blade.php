<div class="col-lg-4 col-xl-3 theiaStickySidebar">

    <!-- Profile Sidebar -->
    <div class="profile-sidebar patient-sidebar profile-sidebar-new">
        <div class="widget-profile pro-widget-content">
            <div class="profile-info-widget">
                <a href="profile-settings.html" class="booking-doc-img">
                    <img src="{{ asset('backend/assets/img/doctors-dashboard/profile-06.jpg') }}" alt="User Image">
                </a>
                <div class="profile-det-info">
                    <h3><a href="profile-settings.html">Hendrita Hayes</a></h3>
                    <div class="patient-details">
                        <h5 class="mb-0">Patient ID : PT254654</h5>
                    </div>
                    <span>Female <i class="fa-solid fa-circle"></i> 32 years 03 Months</span>
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
                        <a href="profile-settings.html">
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