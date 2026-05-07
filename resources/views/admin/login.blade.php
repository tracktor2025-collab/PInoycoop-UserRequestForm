<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Login' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cms-primary: #00a7e1;
            --cms-deep: #2b4889;
            --cms-mid: #49619a;
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
        .login-switch a {
            font-weight: 600;
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
            overflow: hidden;
        }
        .cms-login .card-body {
            padding: 2.15rem !important;
        }
        .cms-login .login-brand {
            justify-content: center;
            margin-bottom: 2.35rem;
        }
        .cms-login .login-logo {
            width: min(100%, 270px);
        }
        .cms-login .login-kicker {
            color: var(--cms-primary);
            text-align: center;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            margin-bottom: 0.35rem;
        }
        .cms-login .login-title,
        .cms-login .login-subtitle {
            text-align: center;
        }
        .cms-login .login-title {
            color: var(--cms-ink);
            font-weight: 800;
            line-height: 1.15;
        }
        .cms-login .login-subtitle {
            color: var(--cms-muted) !important;
            margin-bottom: 1.7rem;
        }
        .cms-login .form-label {
            color: var(--cms-ink);
            font-weight: 650;
            margin-bottom: 0.4rem;
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
        .cms-login .alert {
            border-radius: 8px;
            border-width: 1px;
            font-size: 0.92rem;
        }
        @media (max-width: 520px) {
            .cms-login .card-body {
                padding: 1.55rem !important;
            }
            .cms-login .login-brand {
                margin-bottom: 1.8rem;
            }
        }
    </style>
</head>
@php($isCmsPortal = ($portal ?? '') === 'cms')
<body class="{{ $isCmsPortal ? 'cms-login' : '' }}">
<div class="card login-card shadow">
    <div class="card-body p-4">
        <div class="login-brand">
            <img
                src="{{ $isCmsPortal ? asset('pinooycoop/images/logo.png') : asset('MASS-SPECC Logo/MASS-SPECC LOGO 2.png') }}"
                alt="MASS-SPECC Cooperative Development Center"
                class="login-logo"
                decoding="async"
                loading="lazy"
            >
        </div>
        <div class="login-kicker">MASS-SPECC Cooperative Development Center</div>
        <h1 class="h4 login-title">{{ $title ?? 'Admin Dashboard Login' }}</h1>
        <p class="text-muted login-subtitle">{{ $subtitle ?? 'Sign in with your admin email and password.' }}</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ $formAction ?? route('admin.login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
                @error('email')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                @error('password')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        @if(in_array(($portal ?? ''), ['admin', 'cms'], true))
            <div class="text-center mt-2">
                <a href="{{ route(($portal ?? '') === 'cms' ? 'admin.cms.password.request' : 'admin.password.request') }}" class="text-decoration-none small">Forgot password?</a>
            </div>
        @endif
        @if(! $isCmsPortal && ! empty($switchUrl ?? null) && ! empty($switchLabel ?? null))
            <div class="text-center mt-3 login-switch">
                <a href="{{ $switchUrl }}" class="text-decoration-none">{{ $switchLabel }}</a>
            </div>
        @endif
    </div>
</div>
</body>
</html>
