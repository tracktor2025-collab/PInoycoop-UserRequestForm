@extends('pinoycoop_admin.layouts.app', ['title' => 'Admin Menus'])

@section('content')
    <div class="top">
        <h2>Menus</h2>
        <a class="btn btn-p" href="{{ route('pinoycoop.admin.menus.create') }}">+ New Menu</a>
    </div>
    <div class="card">
        <div class="head">Menu Registry</div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Location</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menus as $menu)
                        <tr>
                            <td>{{ $menu->name }}</td>
                            <td>{{ $menu->slug }}</td>
                            <td>{{ $menu->location }}</td>
                            <td>{{ $menu->all_items_count }}</td>
                            <td>{{ $menu->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a class="btn btn-g btn-sm" href="{{ route('pinoycoop.admin.menus.edit', $menu) }}">Edit</a>
                                <form method="POST" action="{{ route('pinoycoop.admin.menus.destroy', $menu) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-d btn-sm" type="submit" onclick="return confirm('Delete this menu?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No menus found yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
