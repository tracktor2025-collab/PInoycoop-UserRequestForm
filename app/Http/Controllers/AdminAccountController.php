<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AdminAccountController extends Controller
{
    public function myAccount(Request $request): View
    {
        $admin = $this->adminFromSession($request);

        return view('admin.my-account', compact('admin'));
    }

    public function adminsIndex(Request $request): View
    {
        // Paginate existing admins (5 per page).
        $admins = Admin::query()
            ->orderBy('name')
            ->orderBy('email')
            ->paginate(5)
            ->withQueryString();

        return view('admin.account-admins', compact('admins'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $this->adminFromSession($request);

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        $admin->password = $validated['password'];
        $admin->save();

        AuditLogger::log($request, 'account.password_changed', 'Updated their own password.', Admin::class, $admin->id);

        return redirect()->route($this->accountLandingRoute($admin))->with('success', 'Your password has been updated.');
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $admin = $this->adminFromSession($request);

        $validated = $request->validate([
            'email_current_password' => ['required', 'string'],
            'new_email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'new_email_confirmation' => ['required', 'same:new_email'],
        ], [], [
            'new_email' => 'email address',
            'new_email_confirmation' => 'email confirmation',
        ]);

        if (! Hash::check($validated['email_current_password'], $admin->password)) {
            return back()->withErrors(['email_current_password' => 'The current password is incorrect.'])->withInput();
        }

        $oldEmail = $admin->email;
        $admin->email = $validated['new_email'];
        $admin->save();

        AuditLogger::log(
            $request,
            'account.email_changed',
            sprintf('Changed email from %s to %s.', $oldEmail, $admin->email),
            Admin::class,
            $admin->id,
            ['from' => $oldEmail, 'to' => $admin->email],
        );

        return redirect()->route($this->accountLandingRoute($admin))->with('success', 'Your email address has been updated.');
    }

    public function storeAdmin(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $actor = $this->adminFromSession($request);

        if (! $actor->hasEnabledTwoFactor()) {
            return redirect()->route('admin.account.admins')
                ->with('error', 'Enable two-factor authentication on your account before creating other admins.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'super_admin', 'cms_admin'])],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'two_factor_code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        $secret = $actor->two_factor_secret;
        if (! is_string($secret) || $secret === '' || ! $google2fa->verifyKey($secret, $validated['two_factor_code'])) {
            return back()->withErrors(['two_factor_code' => 'Invalid authentication code.'])->withInput();
        }

        $newAdmin = Admin::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
        ]);

        AuditLogger::log(
            $request,
            'account.admin_created',
            sprintf(
                'Created %s account %s <%s>.',
                match ($validated['role']) {
                    'super_admin' => 'super admin',
                    'cms_admin' => 'cms admin',
                    default => 'admin',
                },
                $validated['name'],
                $validated['email']
            ),
            Admin::class,
            $newAdmin->id,
            ['name' => $validated['name'], 'email' => $validated['email'], 'role' => $validated['role']],
        );

        return redirect()->route('admin.account.admins')->with('success', 'New account created successfully.');
    }

    private function adminFromSession(Request $request): Admin
    {
        $id = $request->session()->get('admin_id');
        $admin = is_numeric($id) ? Admin::query()->find((int) $id) : null;

        abort_if($admin === null, 403);

        return $admin;
    }

    private function accountLandingRoute(Admin $admin): string
    {
        return $admin->isSuperAdmin() ? 'admin.account.admins' : 'admin.account.my';
    }
}
