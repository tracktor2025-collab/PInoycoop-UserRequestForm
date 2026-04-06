<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set new password</title>
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
        <h1 class="h4 mb-1">Set new password</h1>
        <p class="text-muted mb-4">Choose a new password. After saving, sign in as usual; two-factor authentication is unchanged.</p>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="form-label" for="email_display">Email</label>
                <input id="email_display" type="email" class="form-control" value="{{ $email }}" disabled autocomplete="username">
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">New password</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" minlength="8">
                @error('password')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" minlength="8">
            </div>
            <button type="submit" class="btn btn-primary w-100">Update password</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('admin.login.form') }}" class="text-decoration-none">Back to login</a>
        </div>
    </div>
</div>
</body>
</html>
