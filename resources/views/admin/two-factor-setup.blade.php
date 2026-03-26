<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up two-factor authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: grid; place-items: center; background: linear-gradient(135deg, #1f2937, #1d4ed8); }
        .card-wrap { width: min(460px, 94vw); border-radius: 16px; }
    </style>
</head>
<body>
<div class="card card-wrap shadow">
    <div class="card-body p-4">
        <h1 class="h5 mb-2">Set up two-factor authentication</h1>
        <p class="text-muted small mb-3">Account: <strong>{{ $admin->email }}</strong></p>
        <p class="small mb-3">Scan the QR code in Google Authenticator, Microsoft Authenticator, or another TOTP app, then enter the 6-digit code.</p>

        @if(session('error'))
            <div class="alert alert-danger small">{{ session('error') }}</div>
        @endif

        <div class="text-center mb-3 p-2 bg-light rounded">
            <img src="{{ $qrImageUrl }}" width="220" height="220" alt="QR code" class="img-fluid" loading="lazy" referrerpolicy="no-referrer">
        </div>
        <p class="small text-muted mb-2">Or enter this key manually:</p>
        <code class="d-block small p-2 bg-light rounded text-break user-select-all mb-3">{{ $secret }}</code>

        <form method="POST" action="{{ route('admin.two-factor.setup.confirm') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="code">6-digit code</label>
                <input id="code" type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code">
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Confirm and continue</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
