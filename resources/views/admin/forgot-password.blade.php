<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot password</title>
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
        <h1 class="h4 mb-1">Forgot password</h1>
        <p class="text-muted mb-4">Enter your admin email. If an account exists, we will send a reset link.</p>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}">
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
            <a href="{{ route('admin.login.form') }}" class="text-decoration-none">Back to login</a>
        </div>
    </div>
</div>
</body>
</html>
