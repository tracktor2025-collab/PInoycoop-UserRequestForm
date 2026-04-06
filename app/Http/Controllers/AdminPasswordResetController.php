<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AdminPasswordResetController extends Controller
{
    public function requestForm(): View
    {
        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = $validated['email'];
        $admin = Admin::query()->where('email', $email)->first();

        if ($admin !== null && $admin->isSuperAdmin()) {
            return back()
                ->withInput($request->only('email'))
                ->with('status', __('If an account exists for that email, you will receive a reset link shortly.'));
        }

        $status = Password::broker('admins')->sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('status', __('If an account exists for that email, you will receive a reset link shortly.'));
    }

    public function resetForm(Request $request, string $token): View|RedirectResponse
    {
        $email = (string) $request->query('email', '');
        if ($email === '') {
            return redirect()
                ->route('admin.password.request')
                ->with('error', 'Invalid or expired reset link. Request a new link.');
        }

        return view('admin.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Admin::query()->where('email', $validated['email'])->first();
        if ($admin !== null && $admin->isSuperAdmin()) {
            return redirect()
                ->route('admin.login.form')
                ->with('error', 'Password reset is not available for this account.');
        }

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('admin.login.form')
                ->with('success', 'Password updated. Sign in with your new password. Two-factor authentication is unchanged.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
