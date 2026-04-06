<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStandardAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->session()->get('admin_id');
        $admin = is_numeric($id) ? Admin::query()->find((int) $id) : null;

        if ($admin === null || $admin->isSuperAdmin()) {
            abort(403, 'Standard admin access is required.');
        }

        return $next($request);
    }
}
