<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->session()->get('admin_id');
        $admin = is_numeric($id) ? Admin::query()->find((int) $id) : null;

        if ($admin === null) {
            $request->session()->forget(['admin_authenticated', 'admin_id']);

            return redirect()->route('admin.login.form')
                ->with('error', 'Please login to access the admin dashboard.');
        }

        View::share('currentAdmin', $admin);

        return $next($request);
    }
}
