<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function index(): View
    {
        Storage::disk('public')->makeDirectory('cms-media');

        return view('pinoycoop_admin.media.index', [
            'files' => collect(Storage::disk('public')->allFiles('cms-media'))
                ->sortByDesc(fn (string $path) => Storage::disk('public')->lastModified($path))
                ->map(fn (string $path) => [
                    'path' => $path,
                    'name' => basename($path),
                    'url' => route('pinoycoop.media.show', ['path' => $path]),
                    'mime' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
                    'size' => Storage::disk('public')->size($path),
                    'modified' => Storage::disk('public')->lastModified($path),
                ])
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
            'file_name' => ['required', 'string', 'max:255'],
        ]);

        Storage::disk('public')->makeDirectory('cms-media');

        $file = $request->file('file');
        $originalExtension = $file->getClientOriginalExtension();
        $customFileName = trim((string) $request->input('file_name'));

        $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $customFileName) ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $safeBaseName.'.'.strtolower($originalExtension);

        $file->storeAs('cms-media', $safeFileName, 'public');

        return redirect()->route('pinoycoop.admin.media.index')->with('status', 'File uploaded.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
        ]);

        $path = trim($validated['path'], '/');

        if (! str_starts_with($path, 'cms-media/') || ! Storage::disk('public')->exists($path)) {
            return redirect()->route('pinoycoop.admin.media.index')->with('error', 'File not found.');
        }

        Storage::disk('public')->delete($path);

        return redirect()->route('pinoycoop.admin.media.index')->with('status', 'File deleted.');
    }

    public function show(string $path): StreamedResponse
    {
        $path = trim($path, '/');

        abort_if($path === '' || str_contains($path, '..') || ! str_starts_with($path, 'cms-media/'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
