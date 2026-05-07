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
        return $this->forgotPasswordView('admin');
    }

    public function requestFormCms(): View
    {
        return $this->forgotPasswordView('cms');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        return $this->sendResetLinkForPortal($request, 'admin');
    }

    public function sendResetLinkCms(Request $request): RedirectResponse
    {
        return $this->sendResetLinkForPortal($request, 'cms');
    }

    private function sendResetLinkForPortal(Request $request, string $portal): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = $validated['email'];
        $admin = Admin::query()->where('email', $email)->first();

        if (! $this->adminCanResetThroughPortal($admin, $portal)) {
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
        return $this->resetFormForPortal($request, $token, 'admin');
    }

    public function resetFormCms(Request $request, string $token): View|RedirectResponse
    {
        return $this->resetFormForPortal($request, $token, 'cms');
    }

    private function resetFormForPortal(Request $request, string $token, string $portal): View|RedirectResponse
    {
        $email = (string) $request->query('email', '');
        $requestRoute = $portal === 'cms' ? 'admin.cms.password.request' : 'admin.password.request';
        if ($email === '') {
            return redirect()
                ->route($requestRoute)
                ->with('error', 'Invalid or expired reset link. Request a new link.');
        }

        $admin = Admin::query()->where('email', $email)->first();
        if (! $this->adminCanResetThroughPortal($admin, $portal)) {
            return redirect()
                ->route($requestRoute)
                ->with('error', 'Invalid or expired reset link. Request a new link.');
        }

        return view('admin.reset-password', [
            'token' => $token,
            'email' => $email,
            'portal' => $portal,
            'formAction' => route($portal === 'cms' ? 'admin.cms.password.update' : 'admin.password.update'),
            'backRoute' => route($portal === 'cms' ? 'admin.cms.login.form' : 'admin.login.form'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        return $this->resetForPortal($request, 'admin');
    }

    public function resetCms(Request $request): RedirectResponse
    {
        return $this->resetForPortal($request, 'cms');
    }

    private function resetForPortal(Request $request, string $portal): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Admin::query()->where('email', $validated['email'])->first();
        if (! $this->adminCanResetThroughPortal($admin, $portal)) {
            return redirect()
                ->route($portal === 'cms' ? 'admin.cms.password.request' : 'admin.password.request')
                ->with('error', 'Invalid or expired reset link. Request a new link.');
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
            $loginRoute = $admin !== null && $admin->isCmsAdmin()
                ? 'admin.cms.login.form'
                : 'admin.login.form';

            return redirect()
                ->route($loginRoute)
                ->with('success', 'Password updated. Sign in with your new password. Two-factor authentication is unchanged.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    private function forgotPasswordView(string $portal): View
    {
        return view('admin.forgot-password', [
            'portal' => $portal,
            'formAction' => route($portal === 'cms' ? 'admin.cms.password.email' : 'admin.password.email'),
            'backRoute' => route($portal === 'cms' ? 'admin.cms.login.form' : 'admin.login.form'),
        ]);
    }

    private function adminCanResetThroughPortal(?Admin $admin, string $portal): bool
    {
        if ($admin === null || $admin->isSuperAdmin()) {
            return false;
        }

        return $portal === 'cms'
            ? $admin->isCmsAdmin()
            : $admin->isStandardAdmin();
    }
}
