<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags untuk SEO dan responsivitas -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>S-Payroll | Login</title>

    <!-- File CSS -->
    <!-- CoreUI CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/css/coreui.min.css" rel="stylesheet">
    <!-- CSS kustom untuk halaman login -->
    <link rel="stylesheet" href="{{ asset('assets/dashboard/style/login.css') }}">
    <!-- Favicon aplikasi -->
    <link rel="icon" type="image/svg+xml" sizes="16x16" href="{{ asset('assets/images/logo.svg') }}">

    <!-- Font Awesome untuk ikon -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Google Font - Nunito -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-wrapper">
                    <img src="{{ asset('assets/images/logo-brand.svg') }}" alt="S-Payroll Logo">
                </div>

                <p class="logo-tagline">Payroll system built for<br><strong>remote-first companies</strong></p>

                {{-- Live Demo Banner --}}
                <div class="demo-banner">
                    <div class="demo-banner-badge">Live Demo</div>
                    <p class="demo-banner-desc">
                        This is a live demo of <strong>S-Payroll</strong> - an open source, self-hosted payroll system you can deploy on your own server.
                    </p>
                    <div class="demo-banner-links">
                        <a href="https://github.com/Sultonisky/Spayroll" target="_blank" rel="noopener noreferrer" class="demo-link demo-link-github">
                            <i class="fab fa-github me-1"></i> GitHub
                        </a>
                        <a href="https://github.com/Sultonisky/Spayroll#readme" target="_blank" rel="noopener noreferrer" class="demo-link demo-link-docs">
                            <i class="fas fa-book me-1"></i> Documentation
                        </a>
                        <a href="https://saweria.co/sultonisky" target="_blank" rel="noopener noreferrer" class="demo-link demo-link-donate">
                            <i class="fas fa-thumbs-up"></i>Donate
                        </a>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="login-header">
                    <p class="welcome-text">Welcome back to S-Payroll!<br><span>Sign in to your account</span></p>
                </div>

                <div class="login-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <div class="input-group-custom">
                                <span class="input-icon">
                                    <i class="far fa-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-input" placeholder="Enter your email..." required autofocus />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-group-custom">
                                <span class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-input" id="pw" placeholder="••••••••" required />
                                <button type="button" class="password-toggle" onclick="togglePw()">
                                    <i id="toggle-icon" class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-container">
                                <input type="checkbox" name="remember" id="remember">
                                <span class="checkmark"></span>
                                Remember me
                            </label>
                        </div>

                        <button class="btn-login" type="submit">
                            Sign In to Dashboard
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>

                <div class="login-footer">
                    <p>&copy; {{ date('Y') }} S-Payroll - Open Source Self-Hosted Payroll System.<br>
                        Built by <a href="https://github.com/Sultonisky" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-body">Mohammad Sultoni</a>
                        &bull;
                        <a href="https://github.com/Sultonisky/SPayroll#readme" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-body">
                            <i class="fab fa-github"></i> Source Code
                        </a>
                    </p>
                    <p class="footer-legal">
                        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                        &bull;
                        <a href="{{ route('terms-of-service') }}">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/js/coreui.bundle.min.js"></script>
    <script src="{{ asset('assets/dashboard/js/login.js') }}" defer></script>
</body>

</html>
