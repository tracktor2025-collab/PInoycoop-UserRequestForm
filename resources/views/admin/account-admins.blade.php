@extends('admin.layout')

@section('title', 'Admin accounts')

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
        <h1 class="page-title">Admin accounts</h1>
        <p class="page-subtitle">Manage your profile, keep admin information organized, and maintain all admin users in one place.</p>
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

    <div class="dashboard-card p-4 mb-4">
        <h2 class="section-card-title">Admin management</h2>
        <div class="border rounded-3 p-3">
            <h3 class="h6 mb-2">Add admin account</h3>
            <p class="subtle-note mb-3">Fill in admin profile information and authorize with your authenticator code.</p>
            <form method="POST" action="{{ route('admin.account.admins.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="255" autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email_new">Email</label>
                        <input id="email_new" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required maxlength="255" autocomplete="off">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label" for="role">Role</label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="position">Position</label>
                        <input id="position" type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" maxlength="255" placeholder="e.g. IT Supervisor">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="department">Department</label>
                        <input id="department" type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}" maxlength="255" placeholder="e.g. Information Technology">
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="contact_number">Contact number</label>
                        <input id="contact_number" type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" maxlength="50" placeholder="e.g. +63 900 000 0000">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label" for="password_new">Password</label>
                        <input id="password_new" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="password_confirmation_new">Confirm password</label>
                        <input id="password_confirmation_new" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" minlength="8">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="two_factor_code">Authenticator code</label>
                        <input id="two_factor_code" type="text" name="two_factor_code" class="form-control @error('two_factor_code') is-invalid @enderror" value="{{ old('two_factor_code') }}" required autocomplete="one-time-code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}">
                        @error('two_factor_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Create admin</button>
                </div>
            </form>
        </div>
    </div>

    <div class="dashboard-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h6 mb-0">Existing admins</h2>
            <span class="subtle-note">{{ $admins->total() }} total account{{ $admins->total() === 1 ? '' : 's' }}</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Contact</th>
                    <th>2FA</th>
                    <th>Added</th>
                </tr>
                </thead>
                <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td>
                            {{ $admin->name }}
                            @if(isset($currentAdmin) && $currentAdmin->id === $admin->id)
                                <span class="badge text-bg-secondary ms-1">You</span>
                            @endif
                        </td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            @if($admin->isSuperAdmin())
                                <span class="badge text-bg-primary">Super Admin</span>
                            @else
                                <span class="badge text-bg-secondary">Admin</span>
                            @endif
                        </td>
                        <td>{{ $admin->position ?: '-' }}</td>
                        <td>{{ $admin->department ?: '-' }}</td>
                        <td>{{ $admin->contact_number ?: '-' }}</td>
                        <td>
                            @if($admin->hasEnabledTwoFactor())
                                <span class="badge text-bg-success">On</span>
                            @else
                                <span class="badge text-bg-warning text-dark">Setup pending</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $admin->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No admin accounts yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $admins->links() }}
        </div>
    </div>
@endsection
