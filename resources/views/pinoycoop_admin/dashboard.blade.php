@extends('pinoycoop_admin.layouts.app', ['title' => 'Admin Dashboard'])

@section('content')
    <style>
        .dash-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .8rem;
            margin-bottom: 1rem;
        }
        .dash-top h1 { margin: 0; font-size: 1.25rem; }
        .dash-top p { margin: .25rem 0 0; color: #59708e; }
        .kpis { display:grid; grid-template-columns: repeat(5, minmax(140px, 1fr)); gap:.8rem; margin-bottom: 1rem; }
        .kpi { background: var(--w); border:1px solid #dbe5ee; border-radius:14px; padding:.9rem; box-shadow:0 16px 40px rgba(32,48,79,.06); transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .kpi:hover { border-color:rgba(0,167,225,.35); box-shadow:0 22px 60px rgba(32,48,79,.10); transform:translateY(-2px); }
        .kpi small { display:block; color:#67809b; text-transform:uppercase; letter-spacing:.6px; font-weight:700; font-size:.75rem; margin-bottom:.35rem; }
        .kpi strong { font-size:1.65rem; color: var(--d); }
        .grid { display:grid; grid-template-columns: 2fr 1fr; gap:.9rem; }
        .badge { display:inline-block; border-radius:999px; padding:.2rem .55rem; font-size:.74rem; font-weight:700; }
        .b-pub { background: rgba(0, 167, 225, 0.14); color:#11658a; }
        .b-draft { background: rgba(127, 143, 178, 0.2); color:#4a5c84; }
        .quick { display:grid; gap:.55rem; }
        .quick a { display:flex; justify-content:space-between; gap:.8rem; align-items:center; text-decoration:none; color:#244265; border:1px solid #dfe8f1; border-radius:12px; padding:.7rem .75rem; background:#f9fcff; transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
        .quick a:hover { transform:translateY(-2px); border-color:#9fd4e8; box-shadow:0 18px 44px rgba(32,48,79,.10); }
        .quick span { color:#6a819b; font-size:.84rem; }
        @media (max-width: 1080px) { .kpis{ grid-template-columns: repeat(2, minmax(160px,1fr)); } .grid{ grid-template-columns: 1fr; } }
        @media (max-width: 760px) { .kpis{ grid-template-columns: 1fr; } .dash-top{ flex-direction:column; } }
    </style>

    <div class="dash-top">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Prototype CMS workspace designed with your main-site palette.</p>
        </div>
        <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
            <a class="btn btn-g" href="{{ route('landing') }}" target="_blank">Open Site</a>
            <a class="btn btn-p" href="{{ route('pinoycoop.admin.pages.create') }}">+ New Page</a>
        </div>
    </div>

    <div class="kpis">
        <div class="kpi"><small>Total Pages</small><strong>{{ $pageCount }}</strong></div>
        <div class="kpi"><small>Published</small><strong>{{ $publishedPageCount }}</strong></div>
        <div class="kpi"><small>Menus</small><strong>{{ $menuCount }}</strong></div>
        <div class="kpi"><small>Active Menus</small><strong>{{ $activeMenuCount }}</strong></div>
        <div class="kpi"><small>Menu Items</small><strong>{{ $menuItemCount }}</strong></div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="head">Recent Pages</div>
            <div class="body">
                @if ($recentPages->isEmpty())
                    <p style="margin:0; color:#6d839b;">No pages yet. Start by creating your first CMS page.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPages as $page)
                                <tr>
                                    <td>{{ $page->title }}</td>
                                    <td>/{{ $page->slug }}</td>
                                    <td>
                                        @if ($page->is_published)
                                            <span class="badge b-pub">Published</span>
                                        @else
                                            <span class="badge b-draft">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($page->updated_at)->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="head">Quick Actions</div>
            <div class="body quick">
                <a href="{{ route('pinoycoop.admin.pages.create') }}">Create page <span>Draft a new content page</span></a>
                <a href="{{ route('pinoycoop.admin.menus.index') }}">Manage menu <span>Sort and edit nav links</span></a>
                <a href="{{ route('pinoycoop.admin.media.index') }}">Upload media <span>Add image assets</span></a>
                <a href="{{ route('landing') }}" target="_blank">Visit website <span>Preview public homepage</span></a>
            </div>
        </div>
    </div>
@endsection
