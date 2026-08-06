<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Doctor Register - Doccure</title>

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

					<!-- Doctor Register -->
					<div class="row justify-content-center">
						<div class="col-lg-4 col-md-6">
							<div class="account-content">
								<div class="account-info">
									<div class="login-title">
										<h3>Doctor Register</h3>
										<p>Create your doctor account to start accepting appointments.</p>
									</div>

									<form method="POST" action="{{ route('doctor.register') }}">
										@csrf

										<div class="mb-3">
											<label class="form-label">First Name</label>
											<input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required autofocus>
											@error('first_name')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3">
											<label class="form-label">Last Name</label>
											<input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" required>
											@error('last_name')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3">
											<label class="form-label">Email</label>
											<input type="email" name="email" value="{{ old('email') }}" class="form-control" required autocomplete="username">
											@error('email')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3">
											<label class="form-label">Phone</label>
											<input class="form-control" name="phone" type="text" value="{{ old('phone') }}" required>
											@error('phone')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3">
											<div class="form-group-flex">
												<label class="form-label">Create Password</label>
											</div>
											<div class="pass-group">
												<input type="password" name="password" class="form-control pass-input" required autocomplete="new-password">
												<span class="feather-eye-off toggle-password"></span>
											</div>
											@error('password')
												<span class="text-danger">{{ $message }}</span>
											@enderror
										</div>
										<div class="mb-3">
											<button class="btn btn-primary-gradient w-100" type="submit">Sign Up</button>
										</div>
										<div class="account-signup">
											<p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- /Doctor Register -->

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
