<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Login - Doccure</title>

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ asset('backend/assets/img/favicon.png') }}" type="image/x-icon">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">

		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

		<!-- Iconsax CSS-->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/iconsax.css') }}">

		<!-- Feathericon CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/feather.css') }}">

		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">

	</head>
	<body class="login-body">

		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Page Content -->
			<div class="login-content-info">
				<div class="container">

					<!-- Login Email -->
					<div class="row justify-content-center">
						<div class="col-lg-4 col-md-6">
							<div class="account-content">
								<div class="account-info">
									<div class="login-title">
										<h3>Sign in</h3>
										<p>Sign in to your Doccure account.</p>
									</div>

									<x-auth-session-status class="mb-3" :status="session('status')" />

									<form method="POST" action="{{ route('login') }}">
										@csrf

										<div class="mb-3">
											<label class="form-label">E-mail</label>
											<input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
											@error('email')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3">
											<div class="form-group-flex">
												<label class="form-label">Password</label>
												@if (Route::has('password.request'))
													<a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
												@endif
											</div>
											<div class="pass-group">
												<input type="password" name="password" class="form-control pass-input" required autocomplete="current-password">
												<span class="feather-eye-off toggle-password"></span>
											</div>
											@error('password')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3 form-check-box">
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" id="remember" name="remember">
												<label class="form-check-label" for="remember">
													Remember Me
												</label>
											</div>
										</div>
										<div class="mb-3">
											<button class="btn btn-primary-gradient w-100" type="submit">Sign in</button>
										</div>
										<div class="account-signup">
											<p>Don't have an account? <a href="{{ route('register') }}">Sign up as Patient</a></p>
											<p>Are you a doctor? <a href="{{ route('doctor.register') }}">Register here</a></p>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- /Login Email -->

				</div>
			</div>
			<!-- /Page Content -->

		</div>
		<!-- /Main Wrapper -->

		<!-- jQuery -->
		<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

		<!-- Bootstrap Bundle JS -->
		<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

		<!-- Feather Icon JS -->
		<script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('backend/assets/js/script.js') }}"></script>

	</body>
</html>
