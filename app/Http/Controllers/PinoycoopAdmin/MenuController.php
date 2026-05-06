<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('pinoycoop_admin.menus.index', [
            'menus' => Menu::query()
                ->withCount([
                    'allItems',
                    'allItems as active_items_count' => fn ($query) => $query->where('is_active', true),
                ])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('pinoycoop_admin.menus.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $menu = Menu::create($data);

        return redirect()->route('pinoycoop.admin.menus.edit', $menu)->with('status', 'Menu created. Add your first menu item.');
    }

    public function edit(Menu $menu): View
    {
        return view('pinoycoop_admin.menus.edit', [
            'menu' => $menu->load(['allItems.page', 'allItems.parent']),
            'pages' => Page::query()
                ->where('is_published', true)
                ->orderBy('title')
                ->get(['id', 'title', 'slug']),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name'], $menu->id);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $menu->update($data);

        return redirect()->route('pinoycoop.admin.menus.index')->with('status', 'Menu updated.');
    }

    public function toggle(Menu $menu): RedirectResponse
    {
        $menu->update(['is_active' => ! $menu->is_active]);

        return redirect()->route('pinoycoop.admin.menus.index')->with('status', 'Menu status updated.');
    }

    public function duplicate(Menu $menu): RedirectResponse
    {
        $copy = $menu->replicate(['slug']);
        $copy->name = $menu->name . ' Copy';
        $copy->slug = $this->uniqueSlug($menu->slug . '-copy');
        $copy->is_active = false;
        $copy->save();

        $idMap = [];

        foreach ($menu->allItems()->orderBy('parent_id')->orderBy('sort_order')->get() as $item) {
            $newItem = $item->replicate();
            $newItem->menu_id = $copy->id;
            $newItem->parent_id = $item->parent_id ? ($idMap[$item->parent_id] ?? null) : null;
            $newItem->save();
            $idMap[$item->id] = $newItem->id;
        }

        return redirect()->route('pinoycoop.admin.menus.edit', $copy)->with('status', 'Menu duplicated as inactive.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('pinoycoop.admin.menus.index')->with('status', 'Menu deleted.');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'menu';
        $slug = $base;
        $suffix = 2;

        while (Menu::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
