<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('pinoycoop_admin.menus.index', [
            'menus' => Menu::query()->withCount('allItems')->latest()->get(),
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:menus,slug'],
            'location' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Menu::create($data);

        return redirect()->route('pinoycoop.admin.menus.index')->with('status', 'Menu created.');
    }

    public function edit(Menu $menu): View
    {
        return view('pinoycoop_admin.menus.edit', [
            'menu' => $menu->load('allItems'),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:menus,slug,' . $menu->id],
            'location' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $menu->update($data);

        return redirect()->route('pinoycoop.admin.menus.index')->with('status', 'Menu updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('pinoycoop.admin.menus.index')->with('status', 'Menu deleted.');
    }
}
