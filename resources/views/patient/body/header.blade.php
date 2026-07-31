<header class="header header-custom header-fixed inner-header relative">
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
                <a href="index.html" class="navbar-brand logo">
                    <img src="{{ asset('backend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                </a>
            </div>
            <div class="header-menu">
                <div class="main-menu-wrapper">
                    <div class="menu-header">
                        <a href="index.html" class="menu-logo">
                            <img src="{{ asset('backend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                        </a>
                        <a id="menu_close" class="menu-close" href="javascript:void(0);">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    <ul class="main-nav">
                                                    <li class="has-submenu megamenu">
                        <a href="javascript:void(0);">Home <i class="fas fa-chevron-down"></i></a>

                        </li>
                        <li class="has-submenu">
                            <a href="javascript:void(0);">Doctors <i class="fas fa-chevron-down"></i></a>
                            <ul class="submenu">
                                <li><a href="doctor-dashboard.html">Doctor Dashboard</a></li>
                                <li><a href="appointments.html">Appointments</a></li>
                                <li><a href="available-timings.html">Available Timing</a></li>
                                <li><a href="my-patients.html">Patients List</a></li>
                                <li><a href="patient-profile.html">Patients Profile</a></li>
                                <li><a href="chat-doctor.html">Chat</a></li>
                                <li><a href="invoices.html">Invoices</a></li>
                                <li><a href="doctor-profile-settings.html">Profile Settings</a></li>
                                <li><a href="reviews.html">Reviews</a></li>
                                <li><a href="doctor-register.html">Doctor Register</a></li>
                                <li class="has-submenu">
                                    <a href="doctor-blog.html">Blog</a>
                                    <ul class="submenu">
                                        <li><a href="doctor-blog.html">Blog</a></li>
                                        <li><a href="blog-details.html">Blog view</a></li>
                                        <li><a href="doctor-add-blog.html">Add Blog</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="has-submenu active">
                            <a href="javascript:void(0);">Patients <i class="fas fa-chevron-down"></i></a>
                            <ul class="submenu">
                                <li class="active"><a href="patient-dashboard.html">Patient Dashboard</a></li>
                                <li class="has-submenu">
                                    <a href="javascript:void(0);">Doctors</a>
                                    <ul class="submenu inner-submenu">
                                        <li><a href="map-grid.html">Map Grid</a></li>
                                        <li><a href="map-list.html">Map List</a></li>
                                        <li><a href="map-list-availability.html">Map with Availability</a></li>
                                    </ul>
                                </li>
                                <li class="has-submenu">
                                    <a href="javascript:void(0);">Search Doctor</a>
                                    <ul class="submenu inner-submenu">
                                        <li><a href="search.html">Search Doctor 1</a></li>
                                        <li><a href="search-2.html">Search Doctor 2</a></li>
                                    </ul>
                                </li>
                                <li class="has-submenu">
                                    <a href="javascript:void(0);">Doctor Profile</a>
                                    <ul class="submenu inner-submenu">
                                        <li><a href="doctor-profile.html">Doctor Profile 1</a></li>
                                        <li><a href="doctor-profile-2.html">Doctor Profile 2</a></li>
                                    </ul>
                                </li>
                                <li class="has-submenu">
                                    <a href="javascript:void(0);">Booking</a>
                                    <ul class="submenu inner-submenu">
                                        <li><a href="booking.html">Booking</a></li>
                                        <li><a href="booking-1.html">Booking 1</a></li>
                                        <li><a href="booking-2.html">Booking 2</a></li>
                                        <li><a href="booking-popup.html">Booking Popup</a></li>
                                    </ul>
                                </li>
                                <li><a href="checkout.html">Checkout</a></li>
                                <li><a href="booking-success.html">Booking Success</a></li>
                                <li><a href="favourites.html">Favourites</a></li>
                                <li><a href="chat.html">Chat</a></li>
                                <li><a href="profile-settings.html">Profile Settings</a></li>
                                <li><a href="change-password.html">Change Password</a></li>
                            </ul>
                        </li>

                        <li class="has-submenu">
                            <a href="#">Blog <i class="fas fa-chevron-down"></i></a>
                            <ul class="submenu">
                                <li><a href="blog-list.html">Blog List</a></li>
                                <li><a href="blog-grid.html">Blog Grid</a></li>
                                <li><a href="blog-details.html">Blog Details</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">
                            <a href="#">Admin <i class="fas fa-chevron-down"></i></a>
                            <ul class="submenu">
                                <li><a href="admin/index.html" target="_blank">Admin</a></li>
                                <li><a href="pharmacy/index.html" target="_blank">Pharmacy Admin</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <ul class="nav header-navbar-rht">
                    <li class="searchbar">
                        <a href="javascript:void(0);"><i class="feather-search"></i></a>
                        <div class="togglesearch">
                            <form action="search.html">
                                <div class="input-group">
                                    <input type="text" class="form-control">
                                    <button type="submit" class="btn">Search</button>
                                </div>
                            </form>
                        </div>
                    </li>

                    <li class="header-theme noti-nav">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="theme-toggle">
                            <i class="isax isax-sun-1"></i>
                        </a>
                        <a href="javascript:void(0);" id="light-mode-toggle" class="theme-toggle activate">
                            <i class="isax isax-moon"></i>
                        </a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown noti-nav me-3 pe-0">
                        <a href="#" class="dropdown-toggle active-dot active-dot-danger nav-link p-0" data-bs-toggle="dropdown">
                            <i class="isax isax-notification-bing"></i>
                        </a>
                        <div class="dropdown-menu notifications dropdown-menu-end ">
                            <div class="topnav-dropdown-header">
                                <span class="notification-title">Notifications</span>
                            </div>
                            <div class="noti-content">
                                <ul class="notification-list">
                                    <li class="notification-message">
                                        <a href="#">
                                            <div class="notify-block d-flex">
                                                <span class="avatar">
                                                    <img class="avatar-img" alt="Ruby perin" src="{{ asset('backend/assets/img/clients/client-01.jpg') }}">
                                                </span>
                                                <div class="media-body">
                                                    <h6>Travis Tremble <span class="notification-time">18.30 PM</span></h6>
                                                    <p class="noti-details">Sent a amount of $210 for his Appointment  <span class="noti-title">Dr.Ruby perin </span></p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="notification-message">
                                        <a href="#">
                                            <div class="notify-block d-flex">
                                                <span class="avatar">
                                                    <img class="avatar-img" alt="Hendry Watt" src="{{ asset('backend/assets/img/clients/client-02.jpg') }}">
                                                </span>
                                                <div class="media-body">
                                                    <h6>Travis Tremble <span class="notification-time">12 Min Ago</span></h6>
                                                    <p class="noti-details"> has booked her appointment to  <span class="noti-title">Dr. Hendry Watt</span></p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="notification-message">
                                        <a href="#">
                                            <div class="notify-block d-flex">
                                                <div class="avatar">
                                                    <img class="avatar-img" alt="Maria Dyen" src="{{ asset('backend/assets/img/clients/client-03.jpg') }}">
                                                </div>
                                                <div class="media-body">
                                                    <h6>Travis Tremble <span class="notification-time">6 Min Ago</span></h6>
                                                    <p class="noti-details"> Sent a amount  $210 for his Appointment   <span class="noti-title">Dr.Maria Dyen</span></p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="notification-message">
                                        <a href="#">
                                            <div class="notify-block d-flex">
                                                <div class="avatar avatar-sm">
                                                    <img class="avatar-img" alt="client-image" src="{{ asset('backend/assets/img/clients/client-04.jpg') }}">
                                                </div>
                                                <div class="media-body">
                                                    <h6>Travis Tremble <span class="notification-time">8.30 AM</span></h6>
                                                    <p class="noti-details"> Send a message to his doctor</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <!-- /Notifications -->

                    <!-- Messages -->
                    <li class="nav-item noti-nav me-3 pe-0">
                        <a href="chat.html" class="dropdown-toggle nav-link active-dot active-dot-success p-0">
                            <i class="isax isax-message-2"></i>
                        </a>
                    </li>
                    <!-- /Messages -->

                    <!-- Cart -->
                    <li class="nav-item dropdown noti-nav view-cart-header me-3 pe-0">
                        <a href="#" class="dropdown-toggle nav-link active-dot active-dot-purple p-0 position-relative" data-bs-toggle="dropdown">
                            <i class="isax isax-shopping-cart"></i>
                        </a>
                        <div class="dropdown-menu notifications dropdown-menu-end">
                            <div class="shopping-cart">
                            <ul class="shopping-cart-items list-unstyled">
                                <li class="clearfix">
                                    <div class="close-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                                    <a href="product-description.html"><img class="avatar-img rounded" src="{{ asset('backend/assets/img/products/product.jpg') }}" alt="User Image"></a>
                                    <a href="product-description.html" class="item-name">Benzaxapine Croplex</a>
                                    <span class="item-price">$849.99</span>
                                    <span class="item-quantity">Quantity: 01</span>
                                </li>

                                <li class="clearfix">
                                    <div class="close-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                                    <a href="product-description.html"><img class="avatar-img rounded" src="{{ asset('backend/assets/img/products/product1.jpg') }}" alt="User Image"></a>
                                    <a href="product-description.html" class="item-name">Ombinazol Bonibamol</a>
                                    <span class="item-price">$1,249.99</span>
                                    <span class="item-quantity">Quantity: 01</span>
                                </li>

                                <li class="clearfix">
                                    <div class="close-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                                    <a href="product-description.html"><img class="avatar-img rounded" src="{{ asset('backend/assets/img/products/product2.jpg') }}" alt="User Image"></a>
                                    <a href="product-description.html" class="item-name">Dantotate Dantodazole</a>
                                    <span class="item-price">$129.99</span>
                                    <span class="item-quantity">Quantity: 01</span>
                                </li>
                            </ul>
                            <div class="booking-summary pt-3">
                                <div class="booking-item-wrap">
                                    <ul class="booking-date">
                                        <li>Subtotal <span>$5,877.00</span></li>
                                        <li>Shipping <span>$25.00</span></li>
                                        <li>Tax <span>$0.00</span></li>
                                        <li>Total <span>$5.2555</span></li>
                                    </ul>
                                    <div class="booking-total">
                                        <ul class="booking-total-list text-align">
                                            <li>
                                                <div class="clinic-booking pt-3">
                                                    <a class="apt-btn" href="cart.html">View Cart</a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="clinic-booking pt-3">
                                                    <a class="apt-btn" href="product-checkout.html">Checkout</a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </li>
                    <!-- /Cart -->

                    <!-- User Menu -->
                    <li class="nav-item dropdown has-arrow logged-item">
                        <a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle" src="{{ asset('backend/assets/img/doctors-dashboard/profile-06.jpg') }}" width="31" alt="Darren Elder">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="user-header">
                                <div class="avatar avatar-sm">
                                    <img src="{{ asset('backend/assets/img/doctors-dashboard/profile-06.jpg') }}" alt="User Image" class="avatar-img rounded-circle">
                                </div>
                                <div class="user-text">
                                    <h6>Hendrita Hayes</h6>
                                    <p class="text-muted mb-0">Patient</p>
                                </div>
                            </div>
                            <a class="dropdown-item" href="patient-dashboard.html">Dashboard</a>
                            <a class="dropdown-item" href="profile-settings.html">Profile Settings</a>
                            <a class="dropdown-item" href="{{ route('patient.logout') }}">Logout</a>
                        </div>
                    </li>
                    <!-- /User Menu -->
                </ul>
            </div>
        </nav>
    </div>
</header>
<!-- /Header -->

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center inner-banner">
            <div class="col-md-12 col-12 text-center">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="isax isax-home-15"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Patient</li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <h2 class="breadcrumb-title">Patient Dashboard</h2>
                </nav>
            </div>
        </div>
    </div>
    <div class="breadcrumb-bg">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-icon.png') }}" alt="img" class="breadcrumb-bg-03">
        <img src="{{ asset('backend/assets/img/bg/breadcrumb-icon.png') }}" alt="img" class="breadcrumb-bg-04">
    </div>
</div>
<!-- /Breadcrumb -->