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
        .dash-top h1 { margin: 0; font-size: 1.45rem; letter-spacing:.1px; }
        .dash-top p { margin: .25rem 0 0; color: #59708e; }
        .kpis { display:grid; grid-template-columns: repeat(5, minmax(140px, 1fr)); gap:.8rem; margin-bottom: 1rem; }
        .kpi { position:relative; overflow:hidden; background: var(--w); border:1px solid #dbe5ee; border-radius:8px; padding:.95rem; box-shadow:0 16px 40px rgba(32,48,79,.06); transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .kpi::after { content:""; position:absolute; inset:auto -20px -34px auto; width:88px; height:88px; border-radius:50%; background:rgba(0,167,225,.09); }
        .kpi:hover { border-color:rgba(0,167,225,.35); box-shadow:0 22px 60px rgba(32,48,79,.10); transform:translateY(-2px); }
        .kpi-top { display:flex; align-items:center; justify-content:space-between; gap:.7rem; margin-bottom:.35rem; }
        .kpi small { display:block; color:#67809b; text-transform:uppercase; letter-spacing:.6px; font-weight:700; font-size:.75rem; }
        .kpi strong { font-size:1.65rem; color: var(--d); }
        .kpi-icon { width:2.15rem; height:2.15rem; display:grid; place-items:center; border-radius:8px; background:#e9f5fb; color:#166286; }
        .kpi-icon svg, .quick-icon svg { width:1.05rem; height:1.05rem; stroke:currentColor; stroke-width:2; fill:none; stroke-linecap:round; stroke-linejoin:round; }
        .grid { display:grid; grid-template-columns: 2fr 1fr; gap:.9rem; }
        .badge { display:inline-block; border-radius:999px; padding:.2rem .55rem; font-size:.74rem; font-weight:700; }
        .b-pub { background: rgba(0, 167, 225, 0.14); color:#11658a; }
        .b-draft { background: rgba(127, 143, 178, 0.2); color:#4a5c84; }
        .quick { display:grid; gap:.55rem; }
        .quick a { display:grid; grid-template-columns:auto 1fr auto; gap:.7rem; align-items:center; text-decoration:none; color:#244265; border:1px solid #dfe8f1; border-radius:8px; padding:.72rem .75rem; background:#f9fcff; transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
        .quick a:hover { transform:translateY(-2px); border-color:#9fd4e8; box-shadow:0 18px 44px rgba(32,48,79,.10); }
        .quick span { color:#6a819b; font-size:.84rem; }
        .quick-icon { width:2rem; height:2rem; display:grid; place-items:center; border-radius:8px; background:#e9f5fb; color:#166286; }
        .quick-title { display:block; font-weight:700; color:#244265; }
        .quick-arrow { color:#85a0bb; font-weight:800; }
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
            <a class="btn btn-p" href="{{ route('pinoycoop.admin.page-builder.create') }}">+ Build Page</a>
        </div>
    </div>

    <div class="kpis">
        <div class="kpi">
            <div class="kpi-top"><small>Total Pages</small><span class="kpi-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg></span></div>
            <strong>{{ $pageCount }}</strong>
        </div>
        <div class="kpi">
            <div class="kpi-top"><small>Published</small><span class="kpi-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></span></div>
            <strong>{{ $publishedPageCount }}</strong>
        </div>
        <div class="kpi">
            <div class="kpi-top"><small>Drafts</small><span class="kpi-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></span></div>
            <strong>{{ $draftPageCount }}</strong>
        </div>
        <div class="kpi">
            <div class="kpi-top"><small>Builder Pages</small><span class="kpi-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h8"></path><path d="M3 11h6"></path><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></span></div>
            <strong>{{ $builderReadyCount }}</strong>
        </div>
        <div class="kpi">
            <div class="kpi-top"><small>Media Files</small><span class="kpi-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15 16 10 5 21"></path></svg></span></div>
            <strong>{{ $mediaCount }}</strong>
        </div>
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
                <a href="{{ route('pinoycoop.admin.page-builder.create') }}"><span class="quick-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg></span><span><strong class="quick-title">Build page</strong><span>Create content with blocks</span></span><span class="quick-arrow">></span></a>
                <a href="{{ route('pinoycoop.admin.page-builder.index') }}"><span class="quick-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h8"></path><path d="M3 11h6"></path><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path></svg></span><span><strong class="quick-title">Page builder</strong><span>Manage builder pages</span></span><span class="quick-arrow">></span></a>
                <a href="{{ route('pinoycoop.admin.media.index') }}"><span class="quick-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15 16 10 5 21"></path></svg></span><span><strong class="quick-title">Upload media</strong><span>Add image assets</span></span><span class="quick-arrow">></span></a>
                <a href="{{ route('landing') }}" target="_blank"><span class="quick-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg></span><span><strong class="quick-title">Visit website</strong><span>Preview public homepage</span></span><span class="quick-arrow">></span></a>
            </div>
        </div>
    </div>
@endsection
