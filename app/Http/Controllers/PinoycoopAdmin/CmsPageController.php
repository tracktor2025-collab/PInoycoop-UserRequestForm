<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CmsPageController extends Controller
{
    public function index(): View
    {
        return view('pinoycoop_admin.pages.index', [
            'pages' => Page::query()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('pinoycoop_admin.pages.create', [
            'mediaFiles' => $this->mediaFiles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:20480'],
            'media_path' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value && (! str_starts_with($value, 'cms-media/') || ! Storage::disk('public')->exists($value))) {
                    $fail('Please select a valid image from the media library.');
                }
            }],
            'template' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? now() : null;
        [$data['image_blob'], $data['image_mime']] = $this->resolveImagePayload($request);

        Page::create($data);

        return redirect()->route('pinoycoop.admin.pages.index')->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        return view('pinoycoop_admin.pages.edit', [
            'page' => $page,
            'mediaFiles' => $this->mediaFiles(),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,' . $page->id],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:20480'],
            'media_path' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value && (! str_starts_with($value, 'cms-media/') || ! Storage::disk('public')->exists($value))) {
                    $fail('Please select a valid image from the media library.');
                }
            }],
            'remove_image' => ['nullable', 'boolean'],
            'template' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published']
            ? ($page->published_at ?: now())
            : null;

        if ((bool) ($data['remove_image'] ?? false)) {
            $data['image_blob'] = null;
            $data['image_mime'] = null;
        }

        if ($request->hasFile('image') || $request->filled('media_path')) {
            [$data['image_blob'], $data['image_mime']] = $this->resolveImagePayload($request);
        }

        $page->update($data);

        return redirect()->route('pinoycoop.admin.pages.index')->with('status', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('pinoycoop.admin.pages.index')->with('status', 'Page deleted.');
    }

    private function mediaFiles()
    {
        Storage::disk('public')->makeDirectory('cms-media');

        return collect(Storage::disk('public')->allFiles('cms-media'))
            ->filter(fn (string $path) => str_starts_with(Storage::disk('public')->mimeType($path) ?? '', 'image/'))
            ->map(fn (string $path) => [
                'path' => $path,
                'name' => basename($path),
                'url' => route('pinoycoop.media.show', ['path' => $path]),
            ])
            ->values();
    }

    private function resolveImagePayload(Request $request): array
    {
        if ($request->hasFile('image')) {
            return [
                file_get_contents($request->file('image')->getRealPath()),
                $request->file('image')->getMimeType(),
            ];
        }

        $mediaPath = $request->input('media_path');

        if ($mediaPath && Storage::disk('public')->exists($mediaPath)) {
            return [
                Storage::disk('public')->get($mediaPath),
                Storage::disk('public')->mimeType($mediaPath),
            ];
        }

        return [null, null];
    }
}
