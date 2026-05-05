@extends('pinoycoop_admin.layouts.app', ['title' => 'Edit Menu'])

@section('content')
    <div class="top">
        <h2>Edit Menu</h2>
        <div style="display:flex; gap:.6rem;">
            <a class="btn btn-g" href="{{ route('pinoycoop.admin.menus.index') }}">Back to Menus</a>
            <form method="POST" action="{{ route('pinoycoop.admin.menus.destroy', $menu) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-d" type="submit" onclick="return confirm('Delete this menu?')">Delete</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom:1rem;">
        <div class="head">Menu Details</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.menus.update', $menu) }}">
                @csrf
                @method('PUT')
                <div class="grid2">
                    <div>
                        <label>Name
                            <input type="text" name="name" value="{{ old('name', $menu->name) }}" required>
                        </label>
                    </div>
                    <div>
                        <label>Slug
                            <input type="text" name="slug" value="{{ old('slug', $menu->slug) }}">
                        </label>
                    </div>
                    <div>
                        <label>Location
                            <select name="location" required>
                                <option value="primary" {{ old('location', $menu->location) === 'primary' ? 'selected' : '' }}>Primary</option>
                                <option value="footer" {{ old('location', $menu->location) === 'footer' ? 'selected' : '' }}>Footer</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <label>Active</label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <button class="btn btn-p" type="submit">Save Menu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="head">Menu Items</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.menus.items.store', $menu) }}" style="margin-bottom:1rem;">
                @csrf
                <div class="grid2">
                    <div>
                        <label>Label
                            <input type="text" name="label" required>
                        </label>
                    </div>
                    <div>
                        <label>URL (e.g. /about)
                            <input type="text" name="url">
                        </label>
                    </div>
                    <div>
                        <label>Sort Order
                            <input type="number" name="sort_order" value="0" min="0">
                        </label>
                    </div>
                    <div>
                        <label>Active</label>
                        <input type="checkbox" name="is_active" value="1" checked>
                    </div>
                </div>
                <div style="margin-top:.8rem;">
                    <button class="btn btn-p" type="submit">Add Item</button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>URL</th>
                        <th>Order</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menu->allItems as $item)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('pinoycoop.admin.menus.items.update', [$menu, $item]) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="label" value="{{ $item->label }}" required>
                            </td>
                            <td><input type="text" name="url" value="{{ $item->url }}"></td>
                            <td><input type="number" name="sort_order" value="{{ $item->sort_order }}" min="0"></td>
                            <td>
                                <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
                            </td>
                            <td style="white-space:nowrap;">
                                <button class="btn btn-g btn-sm" type="submit">Save</button>
                                </form>
                                <form method="POST" action="{{ route('pinoycoop.admin.menus.items.destroy', [$menu, $item]) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-d btn-sm" type="submit" onclick="return confirm('Delete this menu item?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No menu items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

