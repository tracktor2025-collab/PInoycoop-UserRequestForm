<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePendingAdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->session()->get('pending_2fa_admin_id');
        if (! is_numeric($id) || (int) $id < 1) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Please sign in to continue.');
        }

        if (! Admin::query()->whereKey((int) $id)->exists()) {
            $request->session()->forget(['pending_2fa_admin_id', 'admin_totp_enrollment_secret']);

            return redirect()->route('admin.login.form')
                ->with('error', 'Your session expired. Please sign in again.');
        }

        return $next($request);
    }
}
