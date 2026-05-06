@extends('pinoycoop_admin.layouts.app', ['title' => $page ? 'Edit Page' : 'New Page'])

@section('content')
    <style>
        .builder-page { color:var(--t); }
        .builder-page button, .builder-page input, .builder-page textarea, .builder-page select { font:inherit; }
        .builder-frame { max-width:1180px; margin:0 auto; }
        .builder-topbar { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
        .builder-crumbs { display:flex; align-items:center; gap:.55rem; font-size:1.35rem; font-weight:800; color:var(--t); }
        .builder-crumbs a { color:inherit; text-decoration:none; }
        .builder-crumbs span { color:#607993; }
        .builder-actions { display:flex; align-items:center; gap:1rem; }
        .builder-action { min-width:160px; height:48px; border:1px solid var(--p); border-radius:5px; background:#fff; color:var(--d); display:inline-flex; align-items:center; justify-content:center; gap:.65rem; font-size:1rem; text-decoration:none; cursor:pointer; }
        .builder-action svg { width:22px; height:22px; fill:currentColor; }
        .builder-save { min-width:184px; height:48px; border:0; border-radius:5px; background:var(--p); color:#fff; display:inline-flex; align-items:center; justify-content:center; gap:.8rem; font-size:1rem; cursor:pointer; box-shadow:0 10px 24px rgba(0,167,225,.22); }
        .builder-save svg { width:20px; height:20px; fill:currentColor; }
        .builder-save .chevron { width:16px; height:16px; margin-left:.45rem; }
        .builder-fields { display:grid; grid-template-columns:minmax(0,1fr) minmax(280px,1fr); gap:2.5rem; margin-bottom:1.9rem; }
        .builder-field label { display:block; margin:0 0 .55rem .15rem; font-size:1rem; font-weight:800; color:var(--t); }
        .builder-field input, .builder-field select { height:48px; padding:.65rem 2rem .65rem 2rem; margin:0; border:1px solid var(--line); border-radius:3px; background:#fff; color:var(--t); font-size:1.05rem; box-shadow:none; }
        .builder-field textarea { min-height:88px; margin:0; border:1px solid var(--line); background:#fff; box-shadow:none; }
        .builder-field select { appearance:none; background-image:linear-gradient(45deg,transparent 50%,var(--p) 50%),linear-gradient(135deg,var(--p) 50%,transparent 50%); background-position:calc(100% - 28px) 21px,calc(100% - 22px) 21px; background-size:6px 6px,6px 6px; background-repeat:no-repeat; }
        .builder-card { overflow:hidden; border:1px solid var(--line); border-radius:20px 20px 0 0; background:#fff; box-shadow:0 18px 40px rgba(32,48,79,.10); }
        .builder-tabs { display:grid; grid-template-columns:150px 155px 155px 230px 130px 130px minmax(0,1fr); border-bottom:1px solid var(--line); background:#f8fbfd; }
        .builder-tab { height:64px; border:0; border-right:0; background:transparent; color:#607993; font-size:1rem; cursor:pointer; }
        .builder-tab.is-active { background:#fff; color:var(--d); font-weight:800; box-shadow:inset 0 -3px 0 var(--p); }
        .builder-tab-panel { display:none; }
        .builder-tab-panel.is-active { display:block; }
        .builder-workspace { display:grid; grid-template-columns:minmax(0,1fr) 304px; gap:2rem; min-height:596px; padding:24px 39px 32px; }
        .builder-content-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin:6px 0 12px; }
        .builder-content-head h3 { margin:0; font-size:1.12rem; color:var(--t); }
        .builder-content-tools { display:flex; gap:.5rem; flex-wrap:wrap; }
        .builder-mini-button { border:1px solid var(--line); border-radius:5px; background:#fff; color:var(--d); padding:.45rem .7rem; font-weight:700; cursor:pointer; }
        .builder-canvas { min-height:520px; padding:20px 36px; border:1px solid #e4ebf2; border-radius:20px 20px 0 0; background:#fff; transition:border-color .15s ease, background .15s ease; }
        .builder-canvas.is-drag-over { border-color:var(--p); background:#eef9fe; }
        .builder-blocks { display:grid; gap:26px; }
        .builder-empty { display:grid; place-items:center; min-height:260px; border:1px dashed #b5e8fb; border-radius:8px; color:#607993; text-align:center; padding:1rem; }
        .builder-block { background:#fff; transition:opacity .15s ease, transform .15s ease; }
        .builder-block.is-selected .builder-editable, .builder-block.is-selected .builder-image-thumb { border-color:#b5e8fb; box-shadow:0 0 0 3px rgba(0,167,225,.10); }
        .builder-block.is-dragging { opacity:.55; transform:scale(.99); }
        .builder-block.drag-before { border-top:4px solid var(--p); padding-top:8px; }
        .builder-block.drag-after { border-bottom:4px solid var(--p); padding-bottom:8px; }
        .builder-block-head { display:grid; grid-template-columns:24px auto 1fr auto; align-items:center; gap:10px; margin-bottom:14px; }
        .builder-block-check { width:18px; height:18px; margin:0; border-color:var(--line); }
        .builder-block-name { color:var(--t); font-size:1.05rem; font-weight:800; }
        .builder-heading-select { width:auto; min-width:64px; height:34px; margin:0; padding:.2rem .9rem; border-color:var(--p); border-radius:5px; background:#e9f8ff; color:var(--d); font-weight:800; }
        .builder-block-actions { display:flex; align-items:center; gap:16px; color:var(--d); }
        .builder-icon-button { width:20px; height:20px; padding:0; border:0; background:transparent; color:var(--d); display:grid; place-items:center; cursor:pointer; }
        .builder-icon-button svg { width:17px; height:17px; fill:currentColor; stroke:currentColor; stroke-width:1.5; }
        .builder-drag-handle { cursor:grab; }
        .builder-editable { width:100%; min-height:88px; margin:0; padding:14px 26px; border:1px solid var(--line); border-radius:3px; background:#fff; color:var(--d); font-size:1.12rem; font-weight:800; line-height:1.35; resize:vertical; }
        .builder-editable.paragraph { color:#4f5d61; font-size:1rem; font-weight:500; }
        .builder-editable.quote { color:var(--d); font-size:1.05rem; font-style:italic; }
        .builder-editable.cta { min-height:54px; color:#fff; background:var(--p); border-color:var(--p); }
        .builder-editable[disabled] { min-height:48px; color:#607993; background:#f8fbfd; }
        .builder-character-note { margin:.75rem 0 0; color:#19a617; display:flex; align-items:center; gap:.55rem; }
        .builder-image-select { display:grid; gap:.8rem; }
        .builder-image-thumb { position:relative; width:100%; aspect-ratio:16/9; border:1px solid var(--line); border-radius:6px; background:#f8fbfd; display:grid; place-items:center; overflow:hidden; color:#607993; }
        .builder-image-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
        .builder-image-badge { position:absolute; top:14px; right:14px; width:37px; height:28px; border:2px solid #fff; border-radius:4px; display:grid; place-items:center; color:#fff; background:rgba(0,167,225,.22); }
        .builder-image-badge svg { width:24px; height:24px; fill:currentColor; }
        .builder-gallery-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(118px,1fr)); gap:.7rem; min-height:96px; padding:.7rem; border:1px dashed #b5e8fb; border-radius:8px; background:#f8fbfd; color:#607993; }
        .builder-gallery-grid img { width:100%; aspect-ratio:4/3; object-fit:cover; border-radius:6px; display:block; }
        .builder-inline-media { display:grid; grid-template-columns:repeat(auto-fill,minmax(92px,1fr)); gap:.6rem; max-height:180px; overflow:auto; }
        .builder-inline-media button { border:1px solid var(--line); border-radius:6px; overflow:hidden; background:#fff; cursor:pointer; }
        .builder-inline-media button.is-selected { border-color:var(--p); box-shadow:0 0 0 3px rgba(0,167,225,.12); }
        .builder-inline-media img { width:100%; height:64px; object-fit:cover; display:block; }
        .builder-sidebar { align-self:start; position:sticky; top:1rem; border:1px solid var(--line); border-radius:18px 18px 0 0; background:#fff; box-shadow:0 20px 42px rgba(32,48,79,.10); overflow:hidden; }
        .builder-side-tabs { display:grid; grid-template-columns:1fr 1fr; background:#f8fbfd; border-bottom:1px solid var(--line); }
        .builder-side-tab { height:56px; border:0; background:transparent; color:#607993; font-weight:500; cursor:pointer; }
        .builder-side-tab.is-active { background:#fff; color:var(--d); font-weight:800; box-shadow:inset 0 -3px 0 var(--p); }
        .builder-palette { padding:16px 32px 28px; }
        .builder-hero-editor { display:grid; gap:.75rem; max-width:760px; }
        .builder-hero-editor h4 { margin:0; color:var(--t); font-size:1rem; }
        .builder-hero-editor small { color:#607993; line-height:1.35; }
        .builder-drop-copy { display:flex; align-items:center; gap:12px; margin-bottom:16px; color:#607993; line-height:1.15; }
        .builder-drop-copy svg { width:30px; height:30px; color:var(--p); fill:none; stroke:currentColor; stroke-width:1.8; }
        .builder-palette-grid { display:grid; grid-template-columns:1fr 1fr; gap:26px 28px; }
        .builder-palette-item { min-height:104px; border:1px solid #e4ebf2; border-radius:18px; background:#fff; color:var(--t); display:grid; place-items:center; gap:8px; padding:14px 8px; cursor:grab; box-shadow:0 12px 28px rgba(32,48,79,.08); }
        .builder-palette-item:active { cursor:grabbing; }
        .builder-palette-icon { height:42px; display:grid; place-items:center; color:var(--p); }
        .builder-palette-icon svg { width:36px; height:36px; fill:currentColor; }
        .builder-palette-label { display:flex; align-items:center; justify-content:center; gap:.35rem; font-size:.88rem; }
        .builder-panel-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
        .builder-panel-grid .full { grid-column:1 / -1; }
        .builder-panel-grid label { color:var(--t); font-weight:700; }
        .builder-panel-grid input, .builder-panel-grid textarea, .builder-panel-grid select { margin-top:.4rem; background:#fff; }
        .seo-meter { height:8px; border-radius:999px; background:#e8eef6; overflow:hidden; margin-top:.45rem; }
        .seo-meter span { display:block; height:100%; width:0; background:var(--p); transition:width .15s ease, background .15s ease; }
        .seo-note { display:block; color:#607993; font-size:.78rem; margin-top:.25rem; }
        .builder-side-panel { display:none; }
        .builder-side-panel.is-active { display:block; }
        .builder-selected-card { display:grid; gap:.8rem; margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid #e4ebf2; }
        .builder-media-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(88px,1fr)); gap:.65rem; max-height:210px; overflow:auto; margin-top:.65rem; }
        .builder-media { border:1px solid var(--line); border-radius:7px; background:#fff; overflow:hidden; cursor:pointer; }
        .builder-media.is-selected { border-color:var(--p); box-shadow:0 0 0 3px rgba(0,167,225,.12); }
        .builder-media img { width:100%; height:62px; object-fit:cover; display:block; }
        .builder-media span { display:block; padding:.35rem; color:#4f5d61; font-size:.72rem; word-break:break-word; }
        .builder-image-preview { display:grid; place-items:center; min-height:132px; border:1px dashed #b5e8fb; border-radius:8px; background:#f8fbfd; color:#607993; overflow:hidden; }
        .builder-image-preview img { width:100%; height:100%; max-height:190px; object-fit:cover; display:none; }
        .builder-check { display:flex; align-items:center; justify-content:space-between; gap:.7rem; padding:.65rem .75rem; border:1px solid var(--line); border-radius:8px; background:#f9fcff; }
        .builder-check input { width:auto; margin:0; }
        .preview-frame { display:grid; gap:.7rem; min-height:260px; padding:1rem; background:#fff; border:1px solid var(--line); border-radius:8px; }
        .preview-title { margin:0; color:var(--t); font-size:1.35rem; }
        .preview-subcontext { color:#607993; margin:0; }
        .preview-content h3 { color:var(--d); margin:.9rem 0 .35rem; }
        .preview-content p { color:#4f5d61; line-height:1.7; margin:.45rem 0; }
        .preview-content blockquote { margin:.65rem 0; padding:.7rem .85rem; border-left:4px solid var(--p); background:#f3f9fc; color:var(--t); }
        .preview-content figure { margin:.7rem 0; overflow:hidden; border:1px solid var(--line); border-radius:8px; background:#fff; }
        .preview-content figure img { width:100%; max-height:260px; object-fit:cover; display:block; }
        .preview-content .gallery { display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:.6rem; margin:.7rem 0; }
        .preview-content .gallery img { width:100%; aspect-ratio:4/3; object-fit:cover; border-radius:6px; display:block; }
        .preview-content figcaption { padding:.55rem .7rem; color:#607993; font-size:.85rem; }
        .preview-content .cta { display:inline-flex; margin:.6rem 0; padding:.6rem .85rem; border-radius:6px; background:var(--p); color:#fff; font-weight:800; }
        .preview-content hr { border:0; border-top:1px solid var(--line); margin:1rem 0; }
        @media (max-width:1160px) {
            .builder-workspace { grid-template-columns:1fr; }
            .builder-sidebar { position:static; }
            .builder-palette-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
        }
        @media (max-width:860px) {
            .builder-topbar, .builder-actions { align-items:stretch; flex-direction:column; }
            .builder-actions, .builder-action, .builder-save { width:100%; }
            .builder-fields, .builder-panel-grid { grid-template-columns:1fr; gap:1rem; }
            .builder-tabs { grid-template-columns:repeat(2,1fr); }
            .builder-tab { height:54px; }
            .builder-workspace { padding:18px; }
            .builder-canvas { padding:18px; }
            .builder-palette-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
        }
    </style>

    @php($settings = old('builder_settings', $page->builder_settings ?? []))
    @php($tpl = old('template', $page->template ?? 'page'))

    <div class="builder-page">
        <div class="builder-frame">
            <form method="POST" action="{{ $page ? route('pinoycoop.admin.page-builder.update', $page) : route('pinoycoop.admin.page-builder.store') }}" enctype="multipart/form-data" id="builderForm">
                @csrf
                @if ($page)
                    @method('PUT')
                @endif

                <div class="builder-topbar">
                    <div class="builder-crumbs">
                        <a href="{{ route('pinoycoop.admin.page-builder.index') }}">Pages</a>
                        <span>&rsaquo;</span>
                        <strong>{{ $page ? 'Edit Page' : 'New Page' }}</strong>
                    </div>
                    <div class="builder-actions">
                        @if ($page && $page->is_published)
                            <a class="builder-action" href="{{ route('cms.page', $page->slug) }}" target="_blank">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c5.2 0 9.4 5.2 10 7-.6 1.8-4.8 7-10 7S2.6 13.8 2 12c.6-1.8 4.8-7 10-7Zm0 3.4a3.6 3.6 0 1 0 0 7.2 3.6 3.6 0 0 0 0-7.2Z"/></svg>
                                Preview
                            </a>
                        @else
                            <button class="builder-action" type="button" id="previewFocus">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c5.2 0 9.4 5.2 10 7-.6 1.8-4.8 7-10 7S2.6 13.8 2 12c.6-1.8 4.8-7 10-7Zm0 3.4a3.6 3.6 0 1 0 0 7.2 3.6 3.6 0 0 0 0-7.2Z"/></svg>
                                Preview
                            </button>
                        @endif
                        <button class="builder-save" type="submit">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h12l2 2v16H5V3Zm3 2v5h8V5H8Zm1 11v3h6v-3H9Z"/></svg>
                            Save
                            <svg class="chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.4 8.6 12 13.2l4.6-4.6L18 10l-6 6-6-6 1.4-1.4Z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="builder-fields">
                    <div class="builder-field">
                        <label for="builderTitle">Page Title</label>
                        <input type="text" name="title" id="builderTitle" value="{{ old('title', $page->title ?? '') }}" required>
                    </div>
                    <div class="builder-field">
                        <label for="builderTemplate">Category</label>
                        <select name="template" id="builderTemplate">
                            @foreach (\App\Models\Page::TEMPLATE_OPTIONS as $value => $label)
                                <option value="{{ $value }}" {{ $tpl === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="hidden" name="slug" id="builderSlug" value="{{ old('slug', $page->slug ?? '') }}">
                <textarea name="subcontext" id="builderSubcontext" maxlength="500" hidden>{{ old('subcontext', $page->subcontext ?? '') }}</textarea>

                <div class="builder-card">
                    <div class="builder-tabs">
                        <button class="builder-tab is-active" type="button" data-builder-tab="content">Content</button>
                        <button class="builder-tab" type="button" data-builder-tab="hero">Hero Image</button>
                        <button class="builder-tab" type="button" data-builder-tab="seo">SEO Tools</button>
                        <button class="builder-tab" type="button" data-builder-tab="workflow">Approval Workflow</button>
                        <button class="builder-tab" type="button" data-builder-tab="settings">Settings</button>
                        <button class="builder-tab" type="button" data-builder-tab="versions">Versions</button>
                        <span></span>
                    </div>

                    <div class="builder-workspace">
                        <main>
                            <div class="builder-tab-panel is-active" data-builder-panel="content">
                                <div class="builder-canvas" id="builderCanvas">
                                    <div class="builder-content-head">
                                        <h3>Page Content</h3>
                                        <div class="builder-content-tools">
                                            <button class="builder-mini-button" type="button" id="selectFirstBlock">Select First</button>
                                            <button class="builder-mini-button" type="button" id="clearBlocks">Clear</button>
                                        </div>
                                    </div>
                                    <div class="builder-blocks" id="builderBlocks"></div>
                                </div>
                            </div>

                            <div class="builder-tab-panel" data-builder-panel="seo">
                                <div class="builder-canvas">
                                    <div class="builder-content-head"><h3>SEO Tools</h3></div>
                                    <div class="builder-panel-grid">
                                        <label class="full">SEO Title
                                            <input type="text" name="seo_title" id="seoTitle" maxlength="255" value="{{ old('seo_title', $page->seo_title ?? '') }}" placeholder="Search result title">
                                            <span class="seo-note" id="seoTitleNote">Recommended: 50-60 characters.</span>
                                            <div class="seo-meter"><span id="seoTitleMeter"></span></div>
                                        </label>
                                        <label class="full">SEO Description
                                            <textarea name="seo_description" id="seoDescription" maxlength="500" placeholder="Short search result description">{{ old('seo_description', $page->seo_description ?? '') }}</textarea>
                                            <span class="seo-note" id="seoDescriptionNote">Recommended: 120-160 characters.</span>
                                            <div class="seo-meter"><span id="seoDescriptionMeter"></span></div>
                                        </label>
                                        <label class="full">Keywords
                                            <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $page->seo_keywords ?? '') }}" placeholder="cooperative, finance, training">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="builder-tab-panel" data-builder-panel="hero">
                                <div class="builder-canvas">
                                    <div class="builder-content-head"><h3>Article Hero Image</h3></div>
                                    <div class="builder-hero-editor">
                                        <small>This controls the public article banner image.</small>
                                        <div class="builder-image-preview">
                                            <img id="builderImagePreview" src="{{ old('media_path') ? '' : ($page->image_data_uri ?? '') }}" alt="Article hero image preview" @if(! old('media_path') && ! empty($page?->image_data_uri)) style="display:block;" @endif>
                                            <span id="builderImageEmpty" @if(! old('media_path') && ! empty($page?->image_data_uri)) style="display:none;" @endif>Upload or choose an image.</span>
                                        </div>
                                        <label>Upload hero image
                                            <input type="file" name="image" id="builderImageInput" accept="image/*">
                                        </label>
                                        <input type="hidden" name="media_path" id="builderMediaPath" value="{{ old('media_path') }}">
                                        <div class="builder-media-grid">
                                            @forelse ($mediaFiles as $file)
                                                <button type="button" class="builder-media {{ old('media_path') === $file['path'] ? 'is-selected' : '' }}" data-media-path="{{ $file['path'] }}" data-media-url="{{ $file['url'] }}">
                                                    <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}">
                                                    <span>{{ $file['name'] }}</span>
                                                </button>
                                            @empty
                                                <p style="color:#607993;">No media images yet.</p>
                                            @endforelse
                                        </div>
                                        @if ($page && $page->image_data_uri)
                                            <label class="builder-check">
                                                <span>Remove current hero image</span>
                                                <input type="checkbox" name="remove_image" value="1">
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="builder-tab-panel" data-builder-panel="workflow">
                                <div class="builder-canvas">
                                    <div class="builder-content-head"><h3>Approval Workflow</h3></div>
                                    <label class="builder-check">
                                        <span>Publish page after save</span>
                                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
                                    </label>
                                </div>
                            </div>

                            <div class="builder-tab-panel" data-builder-panel="settings">
                                <div class="builder-canvas">
                                    <div class="builder-content-head"><h3>Settings</h3></div>
                                    <div class="builder-panel-grid">
                                        <label>Layout Width
                                            <select name="layout_width">
                                                <option value="default" {{ old('layout_width', $settings['layout_width'] ?? 'default') === 'default' ? 'selected' : '' }}>Default</option>
                                                <option value="wide" {{ old('layout_width', $settings['layout_width'] ?? 'default') === 'wide' ? 'selected' : '' }}>Wide</option>
                                                <option value="narrow" {{ old('layout_width', $settings['layout_width'] ?? 'default') === 'narrow' ? 'selected' : '' }}>Narrow</option>
                                            </select>
                                        </label>
                                        <label class="builder-check">
                                            <span>Show recent posts</span>
                                            <input type="checkbox" name="show_recent_posts" value="1" {{ old('show_recent_posts', $settings['show_recent_posts'] ?? true) ? 'checked' : '' }}>
                                        </label>
                                        <label class="builder-check full">
                                            <span>Show article action buttons</span>
                                            <input type="checkbox" name="enable_article_actions" value="1" {{ old('enable_article_actions', $settings['enable_article_actions'] ?? true) ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="builder-tab-panel" data-builder-panel="versions">
                                <div class="builder-canvas">
                                    <div class="builder-content-head"><h3>Versions</h3></div>
                                    <div class="preview-frame">
                                        <div>
                                            <h3 class="preview-title" id="previewTitle">Untitled page</h3>
                                            <p class="preview-subcontext" id="previewSubcontext"></p>
                                        </div>
                                        <div class="preview-content" id="previewContent"></div>
                                    </div>
                                </div>
                            </div>
                        </main>

                        <aside class="builder-sidebar">
                            <div class="builder-side-tabs">
                                <button class="builder-side-tab is-active" type="button" data-side-tab="palette">Page Builder</button>
                                <button class="builder-side-tab" type="button" data-side-tab="personalization">Personalization</button>
                            </div>
                            <div class="builder-palette builder-side-panel is-active" data-side-panel="palette" aria-label="Builder blocks">
                                <div class="builder-drop-copy">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4z"/><path d="M9 9h11v11H9z"/><path d="M2 2v4M2 2h4M22 22h-4M22 22v-4"/></svg>
                                    <span>Drag &amp; drop block to page content</span>
                                </div>
                                <div class="builder-palette-grid">
                                    <button class="builder-palette-item" type="button" draggable="true" data-add-block="heading">
                                        <span class="builder-palette-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v4h-7v10h-4V9H3V5Zm13 7h5v3h-5v-3Zm0 5h5v2h-5v-2Z"/></svg></span>
                                        <span class="builder-palette-label">Text <span>&#8964;</span></span>
                                    </button>
                                    <button class="builder-palette-item" type="button" draggable="true" data-add-block="image">
                                        <span class="builder-palette-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v14H3V5Zm3 3v7.2l3.6-3.7 3.1 3 2.2-2.5L18 15.4V8H6Z"/></svg></span>
                                        <span class="builder-palette-label">Image <span>&#8964;</span></span>
                                    </button>
                                    <button class="builder-palette-item" type="button" draggable="true" data-add-block="gallery">
                                        <span class="builder-palette-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h7v14H4V5Zm9 0h7v6h-7V5Zm0 8h7v6h-7v-6Z"/></svg></span>
                                        <span class="builder-palette-label">Gallery</span>
                                    </button>
                                    <button class="builder-palette-item" type="button" draggable="true" data-add-block="divider">
                                        <span class="builder-palette-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 11h16v2H4v-2Z"/></svg></span>
                                        <span class="builder-palette-label">Divider</span>
                                    </button>
                                    <button class="builder-palette-item" type="button" draggable="true" data-add-block="quote">
                                        <span class="builder-palette-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h7v7c0 4.4-2.2 7-7 7v-3c2.2 0 3.3-1 3.5-3H5V5Zm11 0h7v7c0 4.4-2.2 7-7 7v-3c2.2 0 3.3-1 3.5-3H16V5Z"/></svg></span>
                                        <span class="builder-palette-label">Quote</span>
                                    </button>
                                </div>
                                <div class="builder-selected-card">
                                    <label>Block Label
                                        <input type="text" id="selectedBlockLabel" placeholder="Select a block to customize">
                                    </label>
                                    <button class="builder-mini-button" type="button" id="duplicateBlock">Duplicate Block</button>
                                </div>
                            </div>

                            <div class="builder-palette builder-side-panel" data-side-panel="personalization">
                                <label class="builder-check">
                                    <span>Publish page</span>
                                    <input type="checkbox" value="1" {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }} data-publish-mirror>
                                </label>
                            </div>
                        </aside>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            let blocks = @json(old('builder_blocks', $blocks));
            let selectedIndex = 0;
            let draggedBlockIndex = null;
            let draggedPaletteType = null;
            const blockTypes = ['heading', 'paragraph', 'image', 'gallery', 'quote', 'cta', 'divider'];
            const blockLabels = { heading: 'Page Header', paragraph: 'Text Block', image: 'Content Image', gallery: 'Gallery', quote: 'Quote', cta: 'Call To Action', divider: 'Divider' };
            const mediaFiles = @json($mediaFiles);
            const blockRoot = document.getElementById('builderBlocks');
            const canvas = document.getElementById('builderCanvas');
            const selectedBlockLabel = document.getElementById('selectedBlockLabel');
            const duplicateBlock = document.getElementById('duplicateBlock');
            const clearBlocks = document.getElementById('clearBlocks');
            const selectFirstBlock = document.getElementById('selectFirstBlock');
            const tabButtons = document.querySelectorAll('[data-builder-tab]');
            const tabPanels = document.querySelectorAll('[data-builder-panel]');
            const sideButtons = document.querySelectorAll('[data-side-tab]');
            const sidePanels = document.querySelectorAll('[data-side-panel]');
            const seoTitle = document.getElementById('seoTitle');
            const seoDescription = document.getElementById('seoDescription');
            const seoTitleMeter = document.getElementById('seoTitleMeter');
            const seoDescriptionMeter = document.getElementById('seoDescriptionMeter');
            const seoTitleNote = document.getElementById('seoTitleNote');
            const seoDescriptionNote = document.getElementById('seoDescriptionNote');
            const titleInput = document.getElementById('builderTitle');
            const slugInput = document.getElementById('builderSlug');
            const subcontextInput = document.getElementById('builderSubcontext');
            const previewTitle = document.getElementById('previewTitle');
            const previewSubcontext = document.getElementById('previewSubcontext');
            const previewContent = document.getElementById('previewContent');
            const imageInput = document.getElementById('builderImageInput');
            const mediaInput = document.getElementById('builderMediaPath');
            const imagePreview = document.getElementById('builderImagePreview');
            const imageEmpty = document.getElementById('builderImageEmpty');
            const previewFocus = document.getElementById('previewFocus');
            const autoSlug = !slugInput.value;

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                }[char]));
            }

            function slugify(value) {
                return String(value || '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            }

            function icon(name) {
                const icons = {
                    gear: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.4 13.5c.1-.5.1-1 .1-1.5s0-1-.1-1.5l2-1.5-2-3.5-2.4 1a7.5 7.5 0 0 0-2.6-1.5L14 2h-4l-.4 3a7.5 7.5 0 0 0-2.6 1.5l-2.4-1-2 3.5 2 1.5c-.1.5-.1 1-.1 1.5s0 1 .1 1.5l-2 1.5 2 3.5 2.4-1a7.5 7.5 0 0 0 2.6 1.5l.4 3h4l.4-3a7.5 7.5 0 0 0 2.6-1.5l2.4 1 2-3.5-2-1.5ZM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"/></svg>',
                    copy: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 7h10v14H8V7Zm-3-4h10v2H7v12H5V3Z"/></svg>',
                    eye: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c5.2 0 9.4 5.2 10 7-.6 1.8-4.8 7-10 7S2.6 13.8 2 12c.6-1.8 4.8-7 10-7Zm0 4a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/></svg>',
                    trash: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 8h10l-.7 13H7.7L7 8Zm2-4h6l1 2h4v2H4V6h4l1-2Z"/></svg>',
                    image: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v14H3V5Zm3 3v7.2l3.6-3.7 3.1 3 2.2-2.5L18 15.4V8H6Z"/></svg>'
                };
                return icons[name] || '';
            }

            function renderBlocks() {
                blockRoot.innerHTML = '';
                if (!blocks.length) {
                    blockRoot.innerHTML = '<div class="builder-empty">Drag blocks here from the palette, or click a block type to add it.</div>';
                    renderPreview();
                    return;
                }

                blocks.forEach((block, index) => {
                    const el = document.createElement('div');
                    const isDivider = block.type === 'divider';
                    const isImage = block.type === 'image';
                    const isGallery = block.type === 'gallery';
                    const selectedMedia = mediaFiles.find((file) => file.path === (block.image || ''));
                    el.className = `builder-block ${selectedIndex === index ? 'is-selected' : ''}`;
                    el.draggable = true;
                    el.dataset.index = index;

                    const bodyHtml = isImage ? `
                        <div class="builder-image-select">
                            <input type="hidden" name="builder_blocks[${index}][image]" value="${escapeHtml(block.image || '')}" data-index="${index}" data-field="image">
                            <div class="builder-image-thumb">
                                ${selectedMedia ? `<img src="${selectedMedia.url}" alt="${escapeHtml(block.text || 'Builder image')}">` : '<span>Select an image from the Media Library.</span>'}
                                <span class="builder-image-badge">${icon('image')}</span>
                            </div>
                            <textarea class="builder-editable paragraph" name="builder_blocks[${index}][text]" data-index="${index}" data-field="text" placeholder="Optional image caption">${escapeHtml(block.text || '')}</textarea>
                            <div class="builder-inline-media">
                                ${mediaFiles.length ? mediaFiles.map((file) => `
                                    <button type="button" class="${file.path === (block.image || '') ? 'is-selected' : ''}" data-pick-block-image="${index}" data-image-path="${escapeHtml(file.path)}">
                                        <img src="${file.url}" alt="${escapeHtml(file.name)}">
                                    </button>
                                `).join('') : '<p style="color:#607993;">No Media Library images yet.</p>'}
                            </div>
                        </div>
                    ` : isGallery ? `
                        <div class="builder-image-select">
                            ${(block.images || []).map((path) => `<input type="hidden" name="builder_blocks[${index}][images][]" value="${escapeHtml(path)}">`).join('')}
                            <div class="builder-gallery-grid">
                                ${(block.images || []).length ? (block.images || []).map((path) => {
                                    const file = mediaFiles.find((item) => item.path === path);
                                    return file ? `<img src="${file.url}" alt="${escapeHtml(file.name)}">` : '';
                                }).join('') : '<span>Select multiple images from the Media Library.</span>'}
                            </div>
                            <textarea class="builder-editable paragraph" name="builder_blocks[${index}][text]" data-index="${index}" data-field="text" placeholder="Optional gallery caption">${escapeHtml(block.text || '')}</textarea>
                            <div class="builder-inline-media">
                                ${mediaFiles.length ? mediaFiles.map((file) => `
                                    <button type="button" class="${(block.images || []).includes(file.path) ? 'is-selected' : ''}" data-toggle-gallery-image="${index}" data-image-path="${escapeHtml(file.path)}">
                                        <img src="${file.url}" alt="${escapeHtml(file.name)}">
                                    </button>
                                `).join('') : '<p style="color:#607993;">No Media Library images yet.</p>'}
                            </div>
                        </div>
                    ` : `
                        <textarea class="builder-editable ${block.type}" name="builder_blocks[${index}][text]" data-index="${index}" data-field="text" ${isDivider ? 'disabled' : ''} placeholder="${isDivider ? 'Divider block has no text.' : 'Write block content...'}">${escapeHtml(block.text || '')}</textarea>
                        ${block.type === 'heading' ? '<p class="builder-character-note"><span>✓</span> 80-90 characters</p>' : ''}
                    `;

                    el.innerHTML = `
                        <div class="builder-block-head">
                            <input class="builder-block-check" type="checkbox" aria-label="Select ${escapeHtml(blockLabels[block.type] || 'block')}">
                            <span class="builder-block-name">${escapeHtml(blockLabels[block.type] || 'Block')}</span>
                            <select class="builder-heading-select" name="builder_blocks[${index}][type]" data-index="${index}" data-field="type">
                                ${blockTypes.map((type) => `<option value="${type}" ${block.type === type ? 'selected' : ''}>${type === 'heading' ? 'H1' : type.charAt(0).toUpperCase() + type.slice(1)}</option>`).join('')}
                            </select>
                            <div class="builder-block-actions">
                                <button class="builder-icon-button builder-drag-handle" type="button" title="Drag to reorder">⋮⋮</button>
                                <button class="builder-icon-button" type="button" data-move-up="${index}" title="Move up">${icon('gear')}</button>
                                <button class="builder-icon-button" type="button" data-move-down="${index}" title="Move down">${icon('copy')}</button>
                                <button class="builder-icon-button" type="button" data-preview-block="${index}" title="Preview block">${icon('eye')}</button>
                                <button class="builder-icon-button" type="button" data-remove="${index}" title="Remove block">${icon('trash')}</button>
                            </div>
                        </div>
                        ${bodyHtml}
                    `;
                    blockRoot.appendChild(el);
                });
                syncSelectedBlockPanel();
                renderPreview();
            }

            function syncSelectedBlockPanel() {
                const block = blocks[selectedIndex];
                selectedBlockLabel.value = block ? (block.text || '') : '';
                selectedBlockLabel.disabled = !block || block.type === 'divider';
                duplicateBlock.disabled = !block;
            }

            function defaultText(type) {
                if (type === 'heading') return titleInput.value || 'Design & Construction of Public Buildings - Entrust Your Project to Professionals!';
                if (type === 'paragraph') return 'Write a paragraph here.';
                if (type === 'quote') return 'Add a quote or highlighted statement.';
                if (type === 'cta') return 'Contact us today';
                return '';
            }

            function addBlock(type, index = blocks.length) {
                const block = type === 'gallery' ? { type, text: '', images: [] } : { type, text: defaultText(type) };
                blocks.splice(index, 0, block);
                selectedIndex = index;
                renderBlocks();
            }

            function clearDropMarkers() {
                blockRoot.querySelectorAll('.builder-block').forEach((block) => {
                    block.classList.remove('drag-before', 'drag-after');
                });
            }

            function dropIndexFromEvent(event) {
                const block = event.target.closest('.builder-block');
                if (!block) return blocks.length;
                const index = Number(block.dataset.index);
                const rect = block.getBoundingClientRect();
                return index + (event.clientY > rect.top + rect.height / 2 ? 1 : 0);
            }

            function markDropPosition(event) {
                clearDropMarkers();
                const block = event.target.closest('.builder-block');
                if (!block) return;
                const rect = block.getBoundingClientRect();
                block.classList.add(event.clientY > rect.top + rect.height / 2 ? 'drag-after' : 'drag-before');
            }

            function renderPreview() {
                previewTitle.textContent = titleInput.value || 'Untitled page';
                previewSubcontext.textContent = subcontextInput.value || '';
                previewContent.innerHTML = blocks.map((block) => {
                    const text = escapeHtml(block.text || '').replace(/\n/g, '<br>');
                    if (block.type === 'heading') return `<h3>${text || 'Section heading'}</h3>`;
                    if (block.type === 'image') {
                        const file = mediaFiles.find((item) => item.path === (block.image || ''));
                        if (!file) return '<figure><figcaption>Select an image block source.</figcaption></figure>';
                        return `<figure><img src="${file.url}" alt="${escapeHtml(block.text || 'Builder image')}">${text ? `<figcaption>${text}</figcaption>` : ''}</figure>`;
                    }
                    if (block.type === 'gallery') {
                        const images = (block.images || [])
                            .map((path) => mediaFiles.find((item) => item.path === path))
                            .filter(Boolean);
                        if (!images.length) return '<figure><figcaption>Select gallery images.</figcaption></figure>';
                        return `<div class="gallery">${images.map((file) => `<img src="${file.url}" alt="${escapeHtml(file.name)}">`).join('')}</div>${text ? `<p>${text}</p>` : ''}`;
                    }
                    if (block.type === 'quote') return `<blockquote>${text || 'Quote text'}</blockquote>`;
                    if (block.type === 'cta') return `<span class="cta">${text || 'Call to action'}</span>`;
                    if (block.type === 'divider') return '<hr>';
                    return `<p>${text || 'Paragraph text'}</p>`;
                }).join('');
            }

            function switchBuilderTab(tab) {
                tabButtons.forEach((button) => button.classList.toggle('is-active', button.dataset.builderTab === tab));
                tabPanels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.builderPanel === tab));
            }

            function switchSideTab(tab) {
                sideButtons.forEach((button) => button.classList.toggle('is-active', button.dataset.sideTab === tab));
                sidePanels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.sidePanel === tab));
            }

            function syncSeoMeter(input, meter, note, min, max) {
                const length = input.value.length;
                const percent = Math.min(100, Math.round((length / max) * 100));
                meter.style.width = `${percent}%`;
                meter.style.background = length >= min && length <= max ? '#20c997' : '#00a7e1';
                note.textContent = `${length} characters. Recommended: ${min}-${max} characters.`;
            }

            tabButtons.forEach((button) => button.addEventListener('click', () => switchBuilderTab(button.dataset.builderTab)));
            sideButtons.forEach((button) => button.addEventListener('click', () => switchSideTab(button.dataset.sideTab)));
            seoTitle.addEventListener('input', () => syncSeoMeter(seoTitle, seoTitleMeter, seoTitleNote, 50, 60));
            seoDescription.addEventListener('input', () => syncSeoMeter(seoDescription, seoDescriptionMeter, seoDescriptionNote, 120, 160));

            document.querySelectorAll('[data-add-block]').forEach((button) => {
                button.addEventListener('click', () => addBlock(button.dataset.addBlock));
                button.addEventListener('dragstart', (event) => {
                    draggedPaletteType = button.dataset.addBlock;
                    draggedBlockIndex = null;
                    event.dataTransfer.effectAllowed = 'copy';
                    event.dataTransfer.setData('text/plain', draggedPaletteType);
                });
            });

            blockRoot.addEventListener('input', (event) => {
                const index = Number(event.target.dataset.index);
                const field = event.target.dataset.field;
                if (!Number.isNaN(index) && field) {
                    blocks[index][field] = event.target.value;
                    renderPreview();
                }
            });

            selectedBlockLabel.addEventListener('input', () => {
                if (!blocks[selectedIndex] || blocks[selectedIndex].type === 'divider') return;
                blocks[selectedIndex].text = selectedBlockLabel.value;
                const selectedTextarea = blockRoot.querySelector(`[data-index="${selectedIndex}"][data-field="text"]`);
                if (selectedTextarea) selectedTextarea.value = selectedBlockLabel.value;
                renderPreview();
            });

            duplicateBlock.addEventListener('click', () => {
                if (!blocks[selectedIndex]) return;
                blocks.splice(selectedIndex + 1, 0, { ...blocks[selectedIndex] });
                selectedIndex++;
                renderBlocks();
            });

            clearBlocks.addEventListener('click', () => {
                if (!confirm('Clear all builder blocks?')) return;
                blocks = [];
                selectedIndex = 0;
                renderBlocks();
            });

            selectFirstBlock.addEventListener('click', () => {
                selectedIndex = 0;
                renderBlocks();
            });

            blockRoot.addEventListener('change', (event) => {
                const index = Number(event.target.dataset.index);
                const field = event.target.dataset.field;
                if (!Number.isNaN(index) && field === 'type') {
                    blocks[index][field] = event.target.value;
                    if (event.target.value === 'divider') blocks[index].text = '';
                    if (event.target.value !== 'image') blocks[index].image = '';
                    if (event.target.value !== 'gallery') blocks[index].images = [];
                    if (event.target.value === 'gallery' && !Array.isArray(blocks[index].images)) blocks[index].images = [];
                    renderBlocks();
                }
            });

            blockRoot.addEventListener('click', (event) => {
                const imagePicker = event.target.closest('[data-pick-block-image]');
                if (imagePicker) {
                    const index = Number(imagePicker.dataset.pickBlockImage);
                    blocks[index].image = imagePicker.dataset.imagePath;
                    selectedIndex = index;
                    renderBlocks();
                    return;
                }

                const galleryPicker = event.target.closest('[data-toggle-gallery-image]');
                if (galleryPicker) {
                    const index = Number(galleryPicker.dataset.toggleGalleryImage);
                    const imagePath = galleryPicker.dataset.imagePath;
                    blocks[index].images = Array.isArray(blocks[index].images) ? blocks[index].images : [];
                    blocks[index].images = blocks[index].images.includes(imagePath)
                        ? blocks[index].images.filter((path) => path !== imagePath)
                        : [...blocks[index].images, imagePath];
                    selectedIndex = index;
                    renderBlocks();
                    return;
                }

                const remove = event.target.closest('[data-remove]')?.dataset.remove;
                const up = event.target.closest('[data-move-up]')?.dataset.moveUp;
                const down = event.target.closest('[data-move-down]')?.dataset.moveDown;
                if (remove !== undefined) {
                    event.stopPropagation();
                    blocks.splice(Number(remove), 1);
                    selectedIndex = Math.max(0, Math.min(selectedIndex, blocks.length - 1));
                }
                if (up !== undefined && Number(up) > 0) {
                    event.stopPropagation();
                    [blocks[Number(up) - 1], blocks[Number(up)]] = [blocks[Number(up)], blocks[Number(up) - 1]];
                    selectedIndex = Number(up) - 1;
                }
                if (down !== undefined && Number(down) < blocks.length - 1) {
                    event.stopPropagation();
                    [blocks[Number(down) + 1], blocks[Number(down)]] = [blocks[Number(down)], blocks[Number(down) + 1]];
                    selectedIndex = Number(down) + 1;
                }
                if (remove !== undefined || up !== undefined || down !== undefined) renderBlocks();

                const selectedBlock = event.target.closest('.builder-block');
                if (selectedBlock && remove === undefined && up === undefined && down === undefined) {
                    selectedIndex = Number(selectedBlock.dataset.index);
                    if (!event.target.closest('input, textarea, select')) renderBlocks();
                }
            });

            blockRoot.addEventListener('dragstart', (event) => {
                const block = event.target.closest('.builder-block');
                if (!block) return;
                draggedBlockIndex = Number(block.dataset.index);
                draggedPaletteType = null;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', String(draggedBlockIndex));
                block.classList.add('is-dragging');
            });

            blockRoot.addEventListener('dragend', () => {
                draggedBlockIndex = null;
                draggedPaletteType = null;
                canvas.classList.remove('is-drag-over');
                clearDropMarkers();
                renderBlocks();
            });

            canvas.addEventListener('dragover', (event) => {
                if (draggedBlockIndex === null && draggedPaletteType === null) return;
                event.preventDefault();
                canvas.classList.add('is-drag-over');
                markDropPosition(event);
            });

            canvas.addEventListener('dragleave', (event) => {
                if (!canvas.contains(event.relatedTarget)) {
                    canvas.classList.remove('is-drag-over');
                    clearDropMarkers();
                }
            });

            canvas.addEventListener('drop', (event) => {
                if (draggedBlockIndex === null && draggedPaletteType === null) return;
                event.preventDefault();
                canvas.classList.remove('is-drag-over');
                clearDropMarkers();
                let targetIndex = dropIndexFromEvent(event);
                if (draggedPaletteType) {
                    addBlock(draggedPaletteType, targetIndex);
                    draggedPaletteType = null;
                    return;
                }
                if (draggedBlockIndex !== null) {
                    const [moved] = blocks.splice(draggedBlockIndex, 1);
                    if (targetIndex > draggedBlockIndex) targetIndex--;
                    blocks.splice(targetIndex, 0, moved);
                    selectedIndex = targetIndex;
                    draggedBlockIndex = null;
                    renderBlocks();
                }
            });

            titleInput.addEventListener('input', () => {
                if (autoSlug) slugInput.value = slugify(titleInput.value);
                if (blocks[0] && blocks[0].type === 'heading' && blocks[0].text === 'Section heading') {
                    blocks[0].text = titleInput.value;
                    renderBlocks();
                }
                renderPreview();
            });

            function showImage(src) {
                if (!src) {
                    imagePreview.style.display = 'none';
                    imageEmpty.style.display = 'block';
                    return;
                }
                imagePreview.src = src;
                imagePreview.style.display = 'block';
                imageEmpty.style.display = 'none';
            }

            imageInput.addEventListener('change', () => {
                document.querySelectorAll('.builder-media').forEach((button) => button.classList.remove('is-selected'));
                mediaInput.value = '';
                const file = imageInput.files[0];
                showImage(file ? URL.createObjectURL(file) : '');
            });

            document.querySelectorAll('.builder-media').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('.builder-media').forEach((item) => item.classList.remove('is-selected'));
                    button.classList.add('is-selected');
                    mediaInput.value = button.dataset.mediaPath;
                    imageInput.value = '';
                    showImage(button.dataset.mediaUrl);
                });
            });

            document.querySelectorAll('[data-publish-mirror]').forEach((mirror) => {
                mirror.addEventListener('change', () => {
                    const real = document.querySelector('input[name="is_published"]');
                    if (real) real.checked = mirror.checked;
                });
            });

            if (previewFocus) {
                previewFocus.addEventListener('click', () => {
                    switchBuilderTab('versions');
                    document.querySelector('[data-builder-panel="versions"]').scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            @if (old('media_path'))
                showImage(@json(collect($mediaFiles)->firstWhere('path', old('media_path'))['url'] ?? ''));
            @endif

            if (autoSlug && titleInput.value) slugInput.value = slugify(titleInput.value);
            syncSeoMeter(seoTitle, seoTitleMeter, seoTitleNote, 50, 60);
            syncSeoMeter(seoDescription, seoDescriptionMeter, seoDescriptionNote, 120, 160);
            renderBlocks();
        }());
    </script>
@endsection
