<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function store(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'target' => ['nullable', 'string', 'max:32'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $menu->allItems()->create($data);

        return redirect()->route('pinoycoop.admin.menus.edit', $menu)->with('status', 'Menu item added.');
    }

    public function update(Request $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'target' => ['nullable', 'string', 'max:32'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $item->update($data);

        return redirect()->route('pinoycoop.admin.menus.edit', $menu)->with('status', 'Menu item updated.');
    }

    public function destroy(Menu $menu, MenuItem $item): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $item->delete();

        return redirect()->route('pinoycoop.admin.menus.edit', $menu)->with('status', 'Menu item deleted.');
    }
}

