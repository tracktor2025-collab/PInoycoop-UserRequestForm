<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Login' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #1f2937, #1d4ed8);
        }
        .login-card {
            width: min(420px, 92vw);
            border-radius: 16px;
        }
    </style>
</head>
<body>
<div class="card login-card shadow">
    <div class="card-body p-4">
        <h1 class="h4 mb-1">{{ $title ?? 'Admin Dashboard Login' }}</h1>
        <p class="text-muted mb-4">{{ $subtitle ?? 'Sign in with your admin email and password.' }}</p>

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
        @if(($portal ?? '') === 'admin')
            <div class="text-center mt-2">
                <a href="{{ route('admin.password.request') }}" class="text-decoration-none small">Forgot password?</a>
            </div>
        @endif
        @if(! empty($switchUrl ?? null) && ! empty($switchLabel ?? null))
            <div class="text-center mt-3">
                <a href="{{ $switchUrl }}" class="text-decoration-none">{{ $switchLabel }}</a>
            </div>
        @endif
    </div>
</div>
</body>
</html>
