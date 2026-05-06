<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    public function index(): View
    {
        return view('pinoycoop_admin.page-builder.index', [
            'pages' => Page::query()->latest()->get(),
            'publishedCount' => Page::query()->where('is_published', true)->count(),
            'draftCount' => Page::query()->where('is_published', false)->count(),
        ]);
    }

    public function create(): View
    {
        return view('pinoycoop_admin.page-builder.form', [
            'page' => null,
            'mediaFiles' => $this->mediaFiles(),
            'blocks' => $this->defaultBlocks(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedPageData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['content'] = $this->buildContent($request->input('builder_blocks', []));
        $data['builder_settings'] = $this->builderSettings($request);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? now() : null;
        [$data['image_blob'], $data['image_mime']] = $this->resolveImagePayload($request);

        $page = Page::create($this->pageColumns($data));

        return redirect()->route('pinoycoop.admin.page-builder.edit', $page)->with('status', 'Page built successfully.');
    }

    public function edit(Page $page): View
    {
        return view('pinoycoop_admin.page-builder.form', [
            'page' => $page,
            'mediaFiles' => $this->mediaFiles(),
            'blocks' => $this->parseBlocks($page->content),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validatedPageData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $page->id);
        $data['content'] = $this->buildContent($request->input('builder_blocks', []));
        $data['builder_settings'] = $this->builderSettings($request);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['published_at'] = $data['is_published'] ? ($page->published_at ?: now()) : null;

        if ($request->boolean('remove_image')) {
            $data['image_blob'] = null;
            $data['image_mime'] = null;
        }

        if ($request->hasFile('image') || $request->filled('media_path')) {
            [$data['image_blob'], $data['image_mime']] = $this->resolveImagePayload($request);
        }

        $page->update($this->pageColumns($data));

        return redirect()->route('pinoycoop.admin.page-builder.edit', $page)->with('status', 'Page updated in builder.');
    }

    private function pageColumns(array $data): array
    {
        return collect($data)->only([
            'title',
            'slug',
            'subcontext',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'builder_settings',
            'content',
            'image_blob',
            'image_mime',
            'template',
            'is_published',
            'published_at',
        ])->all();
    }

    private function validatedPageData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'subcontext' => ['nullable', 'string', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'layout_width' => ['nullable', 'string', 'in:default,wide,narrow'],
            'show_recent_posts' => ['nullable', 'boolean'],
            'enable_article_actions' => ['nullable', 'boolean'],
            'template' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:20480'],
            'media_path' => ['nullable', 'string'],
            'remove_image' => ['nullable', 'boolean'],
            'builder_blocks' => ['nullable', 'array'],
            'builder_blocks.*.type' => ['required_with:builder_blocks', 'string', 'in:heading,paragraph,image,gallery,quote,cta,divider'],
            'builder_blocks.*.text' => ['nullable', 'string', 'max:5000'],
            'builder_blocks.*.image' => ['nullable', 'string'],
            'builder_blocks.*.images' => ['nullable', 'array'],
            'builder_blocks.*.images.*' => ['nullable', 'string'],
        ]);
    }

    private function builderSettings(Request $request): array
    {
        return [
            'layout_width' => $request->input('layout_width', 'default'),
            'show_recent_posts' => $request->boolean('show_recent_posts', true),
            'enable_article_actions' => $request->boolean('enable_article_actions', true),
        ];
    }

    private function defaultBlocks(): array
    {
        return [
            ['type' => 'heading', 'text' => 'Section heading'],
            ['type' => 'paragraph', 'text' => 'Write the opening paragraph here.'],
        ];
    }

    private function buildContent(array $blocks): string
    {
        return collect($blocks)->map(function (array $block) {
            $type = $block['type'] ?? 'paragraph';
            $text = trim((string) ($block['text'] ?? ''));
            $image = trim((string) ($block['image'] ?? ''));
            $images = collect($block['images'] ?? [])->map(fn ($path) => trim((string) $path))->filter()->values();

            return match ($type) {
                'heading' => $text ? '# '.$text : null,
                'image' => $image ? '[IMAGE] '.$image.($text ? "\n".$text : '') : null,
                'gallery' => $images->isNotEmpty() ? '[GALLERY] '.$images->implode('|').($text ? "\n".$text : '') : null,
                'quote' => $text ? '> '.$text : null,
                'cta' => $text ? '[CTA] '.$text : null,
                'divider' => '---',
                default => $text ?: null,
            };
        })->filter()->implode("\n\n");
    }

    private function parseBlocks(?string $content): array
    {
        $parts = preg_split('/\R{2,}/', trim((string) $content)) ?: [];
        $blocks = collect($parts)->map(function (string $part) {
            $part = trim($part);

            if ($part === '---') {
                return ['type' => 'divider', 'text' => ''];
            }

            if (Str::startsWith($part, '[IMAGE] ')) {
                $lines = preg_split('/\R/', $part) ?: [];

                return ['type' => 'image', 'image' => trim(Str::after(array_shift($lines), '[IMAGE] ')), 'text' => trim(implode("\n", $lines))];
            }

            if (Str::startsWith($part, '[GALLERY] ')) {
                $lines = preg_split('/\R/', $part) ?: [];
                $images = collect(explode('|', Str::after(array_shift($lines), '[GALLERY] ')))->map(fn (string $path) => trim($path))->filter()->values()->all();

                return ['type' => 'gallery', 'images' => $images, 'text' => trim(implode("\n", $lines))];
            }

            if (Str::startsWith($part, '# ')) {
                return ['type' => 'heading', 'text' => Str::after($part, '# ')];
            }

            if (Str::startsWith($part, '> ')) {
                return ['type' => 'quote', 'text' => Str::after($part, '> ')];
            }

            if (Str::startsWith($part, '[CTA] ')) {
                return ['type' => 'cta', 'text' => Str::after($part, '[CTA] ')];
            }

            return ['type' => 'paragraph', 'text' => $part];
        })->values()->all();

        return $blocks ?: $this->defaultBlocks();
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
            return [file_get_contents($request->file('image')->getRealPath()), $request->file('image')->getMimeType()];
        }

        $mediaPath = $request->input('media_path');

        if ($mediaPath && Storage::disk('public')->exists($mediaPath)) {
            return [Storage::disk('public')->get($mediaPath), Storage::disk('public')->mimeType($mediaPath)];
        }

        return [null, null];
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'page';
        $slug = $base;
        $suffix = 2;

        while (Page::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
