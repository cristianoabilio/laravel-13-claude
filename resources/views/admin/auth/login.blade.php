<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Doccure</title>

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/assets/img/favicon.png') }}">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">

        <!-- Fontawesome CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/all.min.css') }}">

        <!-- Main CSS -->
        <link rel="stylesheet" href="{{ asset('admin/assets/css/custom.css') }}">

    </head>
    <body>

        <!-- Main Wrapper -->
        <div class="main-wrapper login-body">
            <div class="login-wrapper">
                <div class="container">
                    <div class="loginbox">
                        <div class="login-left">
                            <img class="img-fluid" src="{{ asset('admin/assets/img/logo-white.png') }}" alt="Logo">
                        </div>
                        <div class="login-right">
                            <div class="login-right-wrap">
                                <h1>Admin Login</h1>
                                <p class="account-subtitle">Access to our dashboard</p>

                                <x-auth-session-status class="mb-3" :status="session('status')" />

                                <!-- Form -->
                                <form method="POST" action="{{ route('admin.login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <input class="form-control" type="password" name="password" placeholder="Password" required autocomplete="current-password">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100" type="submit">Login</button>
                                    </div>
                                </form>
                                <!-- /Form -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Main Wrapper -->

        <!-- jQuery -->
        <script src="{{ asset('admin/assets/js/jquery-3.7.1.min.js') }}"></script>

        <!-- Bootstrap Core JS -->
        <script src="{{ asset('admin/assets/js/bootstrap.bundle.min.js') }}"></script>

        <!-- Custom JS -->
        <script src="{{ asset('admin/assets/js/script.js') }}"></script>

    </body>
</html>
