@extends('pinoycoop_admin.layouts.app', ['title' => 'Admin Pages'])

@section('content')
    <div class="top">
        <h2>Pages</h2>
        <a class="btn btn-p" href="{{ route('pinoycoop.admin.pages.create') }}">+ New Page</a>
    </div>
    <div class="card">
        <div class="head">All Pages</div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td>{{ $page->title }}</td>
                            <td>/{{ $page->slug }}</td>
                            <td>{{ $page->template_label }}</td>
                            <td>{{ $page->is_published ? 'Published' : 'Draft' }}</td>
                            <td>{{ optional($page->updated_at)->format('M d, Y') }}</td>
                            <td>
                                <a class="btn btn-g btn-sm" href="{{ route('pinoycoop.admin.pages.edit', $page) }}">Edit</a>
                                <form method="POST" action="{{ route('pinoycoop.admin.pages.destroy', $page) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-d btn-sm" type="submit" onclick="return confirm('Delete this page?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No pages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
