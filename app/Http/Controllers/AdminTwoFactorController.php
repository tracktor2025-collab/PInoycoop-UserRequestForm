<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AdminTwoFactorController extends Controller
{
    private function dashboardRouteFor(Admin $admin, Request $request): string
    {
        $portal = (string) $request->session()->get('login_portal', '');
        if ($portal === 'super' || $admin->isSuperAdmin()) {
            return 'super.dashboard';
        }
        if ($portal === 'cms' || $admin->isCmsAdmin()) {
            return 'pinoycoop.admin.dashboard';
        }

        return 'admin.dashboard';
    }

    public function setup(Request $request, Google2FA $google2fa): View|RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if ($admin->hasEnabledTwoFactor()) {
            return redirect()->route('admin.two-factor.challenge');
        }

        $secret = null;
        $encrypted = $request->session()->get('admin_totp_enrollment_secret');
        if (is_string($encrypted) && $encrypted !== '') {
            try {
                $secret = Crypt::decryptString($encrypted);
            } catch (\Throwable) {
                $secret = null;
            }
        }

        if ($secret === null || $secret === '') {
            $secret = $google2fa->generateSecretKey();
            $request->session()->put('admin_totp_enrollment_secret', Crypt::encryptString($secret));
        }

        $issuer = (string) config('app.name', 'Admin');
        $otpauth = $google2fa->getQRCodeUrl($issuer, $admin->email, $secret);
        $qrImageUrl = 'https://quickchart.io/qr?size=220x220&text='.rawurlencode($otpauth);

        return view('admin.two-factor-setup', [
            'admin' => $admin,
            'secret' => $secret,
            'qrImageUrl' => $qrImageUrl,
        ]);
    }

    public function confirmSetup(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if ($admin->hasEnabledTwoFactor()) {
            return redirect()->route('admin.two-factor.challenge');
        }

        $encrypted = $request->session()->get('admin_totp_enrollment_secret');
        if (! is_string($encrypted) || $encrypted === '') {
            return redirect()->route('admin.two-factor.setup')
                ->with('error', 'Enrollment session expired. Scan the new QR code.');
        }

        try {
            $secret = Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return redirect()->route('admin.two-factor.setup')
                ->with('error', 'Enrollment session expired. Try again.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        if (! $google2fa->verifyKey($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Invalid authentication code.'])->withInput();
        }

        $admin->two_factor_secret = $secret;
        $admin->two_factor_confirmed_at = now();
        $admin->save();

        $this->completeTwoFactorLogin($request, $admin);

        return redirect()->route($this->dashboardRouteFor($admin, $request))
            ->with('success', 'Two-factor authentication is enabled for your account.');
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if (! $admin->hasEnabledTwoFactor()) {
            return redirect()->route('admin.two-factor.setup');
        }

        return view('admin.two-factor-challenge', ['admin' => $admin]);
    }

    public function verifyChallenge(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if (! $admin->hasEnabledTwoFactor()) {
            return redirect()->route('admin.two-factor.setup');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        $secret = $admin->two_factor_secret;
        if (! is_string($secret) || $secret === '' || ! $google2fa->verifyKey($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Invalid authentication code.'])->withInput();
        }

        $this->completeTwoFactorLogin($request, $admin);

        return redirect()->intended(route($this->dashboardRouteFor($admin, $request)));
    }

    private function pendingAdmin(Request $request): Admin
    {
        $id = $request->session()->get('pending_2fa_admin_id');
        $admin = is_numeric($id) ? Admin::query()->find((int) $id) : null;

        abort_if($admin === null, 403);

        return $admin;
    }

    private function completeTwoFactorLogin(Request $request, Admin $admin): void
    {
        $request->session()->forget(['pending_2fa_admin_id', 'admin_totp_enrollment_secret']);
        $request->session()->regenerate();
        $request->session()->put('admin_id', $admin->id);

        $portal = (string) $request->session()->get('login_portal', '');
        AuditLogger::log(
            $request,
            'auth.login',
            sprintf('Admin logged in (%s portal).', match ($portal) {
                'super' => 'super',
                'cms' => 'cms',
                default => 'standard',
            }),
            null,
            null,
            ['portal' => $portal, 'admin_email' => $admin->email],
        );
    }
}
