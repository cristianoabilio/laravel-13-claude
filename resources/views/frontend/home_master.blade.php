<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1" >
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
		<title>Doccure</title>

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ asset('backend/assets/img/favicon.png') }}" type="image/x-icon">

		<!-- Apple Touch Icon -->
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/assets/img/apple-touch-icon.png') }}">

		<!-- Theme Settings Js -->
		<script src="{{ asset('backend/assets/js/theme-script.js') }}"></script>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">

		<link rel="stylesheet" href="{{ asset('backend/assets/css/animate.css') }}">

		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

		<!-- Iconsax CSS-->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/iconsax.css') }}">

		<!-- Feathericon CSS -->
    	<link rel="stylesheet" href="{{ asset('backend/assets/css/feather.css') }}">

    	<!-- Datepicker CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap-datetimepicker.min.css') }}">

		<!-- Owl Carousel CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/owl.carousel.min.css') }}">

		<!-- Animation CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/aos.css') }}">

		<!-- select CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/select2/css/select2.min.css') }}">

		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">

	</head>
	<body>

		<!-- Main Wrapper -->
		<div class="main-wrapper">



			<!-- Header -->
			@include('frontend.body.header')
			<!-- /Header -->

			@yield('home')

			<!-- Footer Section -->
			@include('frontend.body.footer')
			<!-- /Footer Section -->

			<!-- Cursor -->
			<div class="mouse-cursor cursor-outer"></div>
			<div class="mouse-cursor cursor-inner"></div>
			<!-- /Cursor -->

		</div>
		<!-- /Main Wrapper -->

		<!-- jQuery -->
		<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

		<!-- Bootstrap Bundle JS -->
		<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

		<!-- Feather Icon JS -->
		<script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>

		<!-- select JS -->
		<script src="{{ asset('backend/assets/plugins/select2/js/select2.min.js') }}"></script>

		<!-- Datepicker JS -->
		<script src="{{ asset('backend/assets/js/moment.min.js') }}"></script>
		<script src="{{ asset('backend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

		<!-- Owl Carousel JS -->
		<script src="{{ asset('backend/assets/js/owl.carousel.min.js') }}"></script>

		<!-- Counter JS -->
		<script src="{{ asset('backend/assets/js/counter.js') }}"></script>

		<!-- Animation JS -->
		<script src="{{ asset('backend/assets/js/aos.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('backend/assets/js/script.js') }}"></script>

	</body>
</html>