<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(): View
    {
        return view('pinoycoop_admin.media.index', [
            'files' => collect(Storage::disk('public')->files('cms-media'))
                ->map(fn (string $path) => [
                    'path' => $path,
                    'name' => basename($path),
                    'url' => Storage::disk('public')->url($path),
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
            'file_name' => ['required', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $originalExtension = $file->getClientOriginalExtension();
        $customFileName = $request->input('file_name');
        
        // Create a safe filename with the custom name and original extension
        $safeFileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $customFileName) . '.' . $originalExtension;
        
        $file->storeAs('cms-media', $safeFileName, 'public');

        return redirect()->route('pinoycoop.admin.media.index')->with('status', 'File uploaded.');
    }
}
