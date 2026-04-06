@extends('admin.layout')

@section('title', 'My Account')

@section('content')
    @push('styles')
        <style>
            .section-card-title {
                font-size: 0.9rem;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
                color: #5f6b87;
                margin-bottom: 0.9rem;
            }
            .subtle-note {
                font-size: 0.85rem;
                color: #6f7b98;
            }
            .info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 0.75rem;
            }
            .info-item {
                border: 1px solid #e9edf5;
                border-radius: 0.65rem;
                padding: 0.8rem 0.9rem;
                background: #fbfcff;
            }
            .info-label {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                color: #7a86a3;
                margin-bottom: 0.2rem;
            }
            .info-value {
                font-size: 0.95rem;
                color: #22304d;
                font-weight: 600;
                margin: 0;
            }
        </style>
    @endpush

    <div class="mb-3">
        <h1 class="page-title">My Account</h1>
        <p class="page-subtitle">View your admin profile details and update your account settings.</p>
    </div>

    <div class="dashboard-card p-4 mb-4">
        <h2 class="section-card-title">My admin information</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Full name</div>
                <p class="info-value">{{ $currentAdmin->name ?? 'N/A' }}</p>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <p class="info-value">{{ $currentAdmin->email ?? 'N/A' }}</p>
            </div>
            <div class="info-item">
                <div class="info-label">Role</div>
                <p class="info-value">{{ isset($currentAdmin) && $currentAdmin->isSuperAdmin() ? 'Super Admin' : 'Admin' }}</p>
            </div>
            <div class="info-item">
                <div class="info-label">Position</div>
                <p class="info-value">{{ $currentAdmin->position ?? 'Not set' }}</p>
            </div>
            <div class="info-item">
                <div class="info-label">Department</div>
                <p class="info-value">{{ $currentAdmin->department ?? 'Not set' }}</p>
            </div>
            <div class="info-item">
                <div class="info-label">Contact number</div>
                <p class="info-value">{{ $currentAdmin->contact_number ?? 'Not set' }}</p>
            </div>
            <div class="info-item">
                <div class="info-label">Two-factor authentication</div>
                <p class="info-value">{{ isset($currentAdmin) && $currentAdmin->hasEnabledTwoFactor() ? 'Enabled' : 'Setup pending' }}</p>
            </div>
        </div>
    </div>

    <div class="dashboard-card p-4 mb-4">
        <h2 class="section-card-title">My account settings</h2>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="border rounded-3 p-3 h-100">
                    <h3 class="h6 mb-3">Change your password</h3>
                    <p class="subtle-note mb-3">Use your current password to set a new one.</p>
                    <form method="POST" action="{{ route('admin.account.password.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="current_password">Current password</label>
                            <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">New password</label>
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" minlength="8">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">At least 8 characters.</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password_confirmation">Confirm new password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary">Update password</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="border rounded-3 p-3 h-100">
                    <h3 class="h6 mb-3">Change your email</h3>
                    <p class="subtle-note mb-3">Current sign-in address: <strong>{{ $currentAdmin->email ?? '' }}</strong></p>
                    <form method="POST" action="{{ route('admin.account.email.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email_current_password">Current password</label>
                            <input id="email_current_password" type="password" name="email_current_password" class="form-control @error('email_current_password') is-invalid @enderror" required autocomplete="current-password">
                            @error('email_current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new_email">New email</label>
                            <input id="new_email" type="email" name="new_email" class="form-control @error('new_email') is-invalid @enderror" value="{{ old('new_email') }}" required maxlength="255" autocomplete="email">
                            @error('new_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="new_email_confirmation">Confirm new email</label>
                            <input id="new_email_confirmation" type="email" name="new_email_confirmation" class="form-control @error('new_email_confirmation') is-invalid @enderror" value="{{ old('new_email_confirmation') }}" required maxlength="255" autocomplete="email">
                            @error('new_email_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
