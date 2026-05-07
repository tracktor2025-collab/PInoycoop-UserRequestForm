<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cms-primary: #00a7e1;
            --cms-ink: #20304f;
            --cms-muted: #64748b;
            --cms-line: #dbe5ee;
            --cms-bg: #eef4f8;
        }
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at top left, #e0edff 0, #f7f9ff 42%, #ffffff 100%);
            color: #0f172a;
            padding: 1.5rem;
        }
        .login-card {
            width: min(420px, 92vw);
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(10px);
        }
        .login-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        .login-logo {
            width: min(100%, 320px);
            height: auto;
            display: block;
            object-fit: contain;
        }
        .login-kicker {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1d4ed8;
            text-align: center;
        }
        .login-title {
            text-align: center;
            margin-bottom: 0.25rem;
        }
        .login-subtitle {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        body.cms-login {
            background:
                radial-gradient(circle at 18% 18%, rgba(0, 167, 225, 0.18), transparent 24rem),
                radial-gradient(circle at 86% 12%, rgba(43, 72, 137, 0.14), transparent 20rem),
                linear-gradient(145deg, #f8fbfd 0%, var(--cms-bg) 100%);
            color: var(--cms-ink);
            font-family: "Segoe UI", Arial, sans-serif;
        }
        .cms-login .login-card {
            width: min(460px, 92vw);
            border-radius: 8px;
            border-color: var(--cms-line);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 65px rgba(32, 48, 79, 0.14);
        }
        .cms-login .card-body {
            padding: 2.15rem !important;
        }
        .cms-login .login-logo {
            width: min(100%, 270px);
        }
        .cms-login .login-kicker {
            color: var(--cms-primary);
        }
        .cms-login .form-control {
            border-color: #d2dee9;
            border-radius: 8px;
            padding: 0.72rem 0.78rem;
            background: #fbfdff;
            color: var(--cms-ink);
        }
        .cms-login .form-control:focus {
            border-color: rgba(0, 167, 225, 0.58);
            box-shadow: 0 0 0 4px rgba(0, 167, 225, 0.13);
        }
        .cms-login .btn-primary {
            border: 0;
            border-radius: 8px;
            background: var(--cms-primary);
            padding: 0.76rem 1rem;
            font-weight: 800;
            box-shadow: 0 14px 30px rgba(0, 167, 225, 0.22);
        }
        .cms-login .btn-primary:hover,
        .cms-login .btn-primary:focus {
            background: #0b80bb;
            box-shadow: 0 16px 34px rgba(11, 128, 187, 0.26);
        }
    </style>
</head>
@php($isCmsPortal = ($portal ?? '') === 'cms')
<body class="{{ $isCmsPortal ? 'cms-login' : '' }}">
<div class="card login-card shadow">
    <div class="card-body p-4">
        <div class="login-brand">
            <img
                src="{{ $isCmsPortal ? asset('pinooycoop/images/logo.png') : asset('MASS-SPECC Logo/MASS-SPECC Logo.png') }}"
                alt="MASS-SPECC Cooperative Development Center"
                class="login-logo"
                decoding="async"
                loading="lazy"
            >
        </div>
        <div class="login-kicker">MASS-SPECC Cooperative Development Center</div>
        <h1 class="h4 login-title">{{ $isCmsPortal ? 'Pinoycoop Password Reset' : 'Forgot password' }}</h1>
        <p class="text-muted login-subtitle">{{ $isCmsPortal ? 'Enter your CMS admin email to receive a password reset link.' : 'Enter your admin email to receive a reset link.' }}</p>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ $formAction ?? route('admin.password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                @error('email')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Send reset link</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ $backRoute ?? route('admin.login.form') }}" class="text-decoration-none">Back to login</a>
        </div>
    </div>
</div>
</body>
</html>
