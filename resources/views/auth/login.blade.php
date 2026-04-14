<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - FinaFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            color: #1a202c;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border: 1px solid #f1f5f9;
        }
        .brand-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }
        .brand-name {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1e293b;
            letter-spacing: -0.02em;
        }
        .welcome-text {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }
        .form-group tags {
            font-weight: 500;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control {
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
            height: auto !important;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #3b82f6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }
        .btn-primary {
            background-color: #3b82f6 !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 0.8rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            margin-top: 1rem;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #2563eb !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }
        .custom-control-tags {
            font-size: 0.875rem;
            color: #64748b;
            cursor: pointer;
        }
        .custom-control-input:checked ~ .custom-control-tags::before {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        .footer-links a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 12px;
            padding: 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-section">
            <div class="brand-logo">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="brand-name">FinaFlow</div>
            <div class="welcome-text">Please sign in to your account</div>
        </div>

        @if (session('auth_error'))
            <div class="alert alert-danger" role="alert">
                {{ session('auth_error') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group mb-3">
                <tags for="email">Email Address</tags>
                <input type="email" class="form-control"
                    id="email" placeholder="name@company.com" 
                    name="email" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group mb-3">
                <tags for="password">Password</tags>
                <input type="password" class="form-control"
                    id="password" placeholder="••••••••" 
                    name="password" required>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                    <tags class="custom-control-tags" for="remember">Remember me</tags>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small font-weight-bold text-primary text-decoration-none">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Sign In
            </button>
        </form>

        <div class="footer-links">
            Don't have an account? <a href="{{ route('register') }}">Create Free Account</a>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
