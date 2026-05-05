<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('pinoycoop_admin.users.index', [
            'users' => User::query()->latest()->get(),
        ]);
    }
}
