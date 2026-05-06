@extends('pinoycoop_admin.layouts.app', ['title' => 'Page Builder'])

@section('content')
    <style>
        .builder-stats { display:grid; grid-template-columns:repeat(3,minmax(150px,1fr)); gap:.8rem; margin-bottom:1rem; }
        .builder-stat { background:#fff; border:1px solid var(--line); border-radius:8px; padding:.9rem; box-shadow:0 16px 40px rgba(32,48,79,.06); }
        .builder-stat small { display:block; color:#67809b; text-transform:uppercase; letter-spacing:.6px; font-weight:700; font-size:.72rem; margin-bottom:.25rem; }
        .builder-stat strong { color:var(--d); font-size:1.45rem; }
        .builder-badge { display:inline-flex; border-radius:999px; padding:.22rem .55rem; font-size:.75rem; font-weight:700; }
        .builder-badge.live { background:rgba(0,167,225,.14); color:#11658a; }
        .builder-badge.draft { background:rgba(127,143,178,.2); color:#4a5c84; }
        .builder-actions { display:flex; gap:.4rem; flex-wrap:wrap; }
        .builder-muted { display:block; color:#6d839b; font-size:.8rem; margin-top:.2rem; }
        @media (max-width:760px) { .builder-stats{grid-template-columns:1fr;} }
    </style>

    <div class="top">
        <div>
            <h2>Page Builder</h2>
            <span class="builder-muted">Build public CMS pages with reusable content blocks.</span>
        </div>
        <a class="btn btn-p" href="{{ route('pinoycoop.admin.page-builder.create') }}">+ Build Page</a>
    </div>

    <div class="builder-stats">
        <div class="builder-stat"><small>Total Pages</small><strong>{{ $pages->count() }}</strong></div>
        <div class="builder-stat"><small>Published</small><strong>{{ $publishedCount }}</strong></div>
        <div class="builder-stat"><small>Drafts</small><strong>{{ $draftCount }}</strong></div>
    </div>

    <div class="card">
        <div class="head">Built Pages</div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td>
                                <strong>{{ $page->title }}</strong>
                                <span class="builder-muted">/p/{{ $page->slug }}</span>
                            </td>
                            <td>{{ $page->template_label }}</td>
                            <td><span class="builder-badge {{ $page->is_published ? 'live' : 'draft' }}">{{ $page->is_published ? 'Published' : 'Draft' }}</span></td>
                            <td>{{ optional($page->updated_at)->format('M d, Y') }}</td>
                            <td>
                                <div class="builder-actions">
                                    <a class="btn btn-g btn-sm" href="{{ route('pinoycoop.admin.page-builder.edit', $page) }}">Open Builder</a>
                                    <a class="btn btn-g btn-sm" href="{{ route('pinoycoop.admin.pages.edit', $page) }}">Classic Edit</a>
                                    @if ($page->is_published)
                                        <a class="btn btn-d btn-sm" href="{{ route('cms.page', $page->slug) }}" target="_blank">View</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No pages yet. Start by building your first page.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
