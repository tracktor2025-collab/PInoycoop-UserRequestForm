<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Two-factor authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: grid; place-items: center; background: linear-gradient(135deg, #1f2937, #1d4ed8); }
        .card-wrap { width: min(420px, 92vw); border-radius: 16px; }
    </style>
</head>
<body>
<div class="card card-wrap shadow">
    <div class="card-body p-4">
        <h1 class="h5 mb-2">Two-factor authentication</h1>
        <p class="text-muted small mb-4">Enter the code from your authenticator app for <strong>{{ $admin->email }}</strong>.</p>

        <form method="POST" action="{{ route('admin.two-factor.verify') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="code">6-digit code</label>
                <input id="code" type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code">
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
