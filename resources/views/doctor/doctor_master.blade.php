<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="utf-8">
		<title>Doccure</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
		<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
		<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="og:type" content="website">
		<meta property="og:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta property="og:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta property="og:image" content="{{ asset('backend/assets/img/preview-banner.jpg') }}">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta name="twitter:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta name="twitter:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="twitter:image" content="{{ asset('backend/assets/img/preview-banner.jpg') }}">

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ asset('backend/assets/img/favicon.png') }}" type="image/x-icon">

		<!-- Apple Touch Icon -->
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/assets/img/apple-touch-icon.png') }}">

		<!-- Theme Settings Js -->
		<script src="{{ asset('backend/assets/js/theme-script.js') }}"></script>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">

		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

		<!-- Iconsax CSS-->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/iconsax.css') }}">

		<!-- select CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/select2/css/select2.min.css') }}">

		<!-- Feathericon CSS -->
    	<link rel="stylesheet" href="{{ asset('backend/assets/css/feather.css') }}">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}">

		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">

	</head>
	<body>

		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Header -->
			@include('doctor.body.header')
			<!-- /Header -->

			<!-- Breadcrumb -->
            @include('doctor.body.breadcrumb')
			<!-- /Breadcrumb -->

			<!-- Page Content -->
			<div class="content">
				<div class="container">

					<div class="row">
						<div class="col-lg-4 col-xl-3 theiaStickySidebar">

							<!-- Profile Sidebar -->
							@include('doctor.body.sidebar')
							<!-- /Profile Sidebar -->

						</div>

						<div class="col-lg-8 col-xl-9">

							@yield('doctor')

						</div>
					</div>

				</div>

			</div>
			<!-- /Page Content -->

			<!-- Footer Section -->
			@include('doctor.body.footer')
			<!-- /Footer Section -->

		</div>
		<!-- /Main Wrapper -->

		<!-- jQuery -->
		<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

		<!-- Bootstrap Core JS -->
		<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

		<!-- Sticky Sidebar JS -->
        <script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
        <script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

		<!-- select JS -->
		<script src="{{ asset('backend/assets/plugins/select2/js/select2.min.js') }}"></script>

		<!-- Apexchart JS -->
		<script src="{{ asset('backend/assets/plugins/apex/apexcharts.min.js') }}"></script>
		<script src="{{ asset('backend/assets/plugins/apex/chart-data.js') }}"></script>


		<!-- Bootstrap Tagsinput JS -->
		<script src="{{ asset('backend/assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}"></script>

		<!-- Profile Settings JS -->
		<script src="{{ asset('backend/assets/js/profile-settings.js') }}"></script>

		<!-- Circle Progress JS -->
		<script src="{{ asset('backend/assets/js/circle-progress.min.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('backend/assets/js/script.js') }}"></script>

	</body>
</html>