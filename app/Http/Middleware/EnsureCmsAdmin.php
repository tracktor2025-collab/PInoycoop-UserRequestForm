<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCmsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->session()->get('admin_id');
        $admin = is_numeric($id) ? Admin::query()->find((int) $id) : null;
        $portal = (string) $request->session()->get('login_portal', '');

        if ($admin === null || $portal !== 'cms') {
            return redirect()->route('admin.cms.login.form')
                ->with('error', 'Please login to access the Pinoycoop CMS admin.');
        }

        if (! $admin->isCmsAdmin()) {
            abort(403, 'CMS admin access is required.');
        }

        return $next($request);
    }
}
