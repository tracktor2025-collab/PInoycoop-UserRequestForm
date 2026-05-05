<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pageCount = Page::count();
        $publishedPageCount = Page::where('is_published', true)->count();
        $menuCount = Menu::count();
        $activeMenuCount = Menu::where('is_active', true)->count();
        $menuItemCount = MenuItem::count();

        return view('pinoycoop_admin.dashboard', [
            'pageCount' => $pageCount,
            'publishedPageCount' => $publishedPageCount,
            'menuCount' => $menuCount,
            'activeMenuCount' => $activeMenuCount,
            'menuItemCount' => $menuItemCount,
            'recentPages' => Page::query()
                ->latest()
                ->limit(6)
                ->get(['title', 'slug', 'is_published', 'updated_at']),
        ]);
    }
}
