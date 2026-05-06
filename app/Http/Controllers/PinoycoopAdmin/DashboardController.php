<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pageCount = Page::count();
        $publishedPageCount = Page::where('is_published', true)->count();
        $draftPageCount = Page::where('is_published', false)->count();
        $builderReadyCount = Page::whereNotNull('content')->where('content', '!=', '')->count();
        Storage::disk('public')->makeDirectory('cms-media');
        $mediaCount = count(Storage::disk('public')->files('cms-media'));

        return view('pinoycoop_admin.dashboard', [
            'pageCount' => $pageCount,
            'publishedPageCount' => $publishedPageCount,
            'draftPageCount' => $draftPageCount,
            'builderReadyCount' => $builderReadyCount,
            'mediaCount' => $mediaCount,
            'recentPages' => Page::query()
                ->latest()
                ->limit(6)
                ->get(['title', 'slug', 'is_published', 'updated_at']),
        ]);
    }
}
