@extends('pinoycoop_admin.layouts.app', ['title' => 'Edit Page'])

@section('content')
    <style>
        .page-edit-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: .3rem;
            padding: .85rem 1rem;
            border: 1px solid #dbe5ee;
            border-radius: 12px;
            background: linear-gradient(180deg, #fbfdff, #f4f8fb);
        }

        .page-edit-toggle input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: #00a7e1;
            flex: 0 0 auto;
            box-shadow: none;
        }

        .page-edit-toggle-text {
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }

        .page-edit-toggle-text strong {
            color: #20304f;
            font-size: .95rem;
        }

        .page-edit-toggle-text span {
            color: #6a7d92;
            font-size: .84rem;
            line-height: 1.5;
        }

        .page-edit-image-preview {
            margin-top: .75rem;
            max-width: 220px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #dbe5ee;
            background: #f4f8fb;
        }

        .page-edit-image-preview img {
            display: block;
            width: 100%;
            height: auto;
        }

        .feature-image-card {
            margin-top: .35rem;
            padding: 1rem;
            border: 1px solid #dbe5ee;
            border-radius: 14px;
            background: linear-gradient(180deg, #fbfdff, #f4f8fb);
        }

        .feature-image-preview {
            width: 100%;
            max-width: 240px;
            aspect-ratio: 4 / 3;
            border-radius: 14px;
            overflow: hidden;
            border: 1px dashed #c9d9e8;
            background: #eef5fb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6a7d92;
            font-size: .88rem;
            text-align: center;
        }

        .feature-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .feature-image-actions {
            display: grid;
            gap: .85rem;
            margin-top: .9rem;
        }

        .media-library-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: .75rem;
            margin-top: .85rem;
            max-height: 280px;
            overflow: auto;
            padding-right: .25rem;
        }

        .media-library-item {
            border: 1px solid #dbe5ee;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .media-library-item:hover,
        .media-library-item.is-selected {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(32, 48, 79, .10);
            border-color: rgba(0, 167, 225, .55);
        }

        .media-library-item img {
            width: 100%;
            height: 92px;
            object-fit: cover;
            display: block;
        }

        .media-library-item span {
            display: block;
            padding: .55rem .6rem;
            font-size: .8rem;
            color: #486078;
            word-break: break-word;
        }
    </style>
    <div class="top">
        <h2>Edit Page</h2>
        <a class="btn btn-g" href="{{ route('pinoycoop.admin.pages.index') }}">Back to Pages</a>
    </div>
    <div class="card">
        <div class="head">Update Page</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.pages.update', $page) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid2">
                    <div>
                        <label>Title
                            <input type="text" name="title" value="{{ old('title', $page->title) }}" required>
                        </label>
                    </div>
                    <div>
                        <label>Slug (optional)
                            <input type="text" name="slug" value="{{ old('slug', $page->slug) }}">
                        </label>
                    </div>
                    <div>
                        <label>Template
                            @php($tpl = old('template', $page->template ?: 'page'))
                            <select name="template">
                                <option value="page" {{ $tpl === 'page' ? 'selected' : '' }}>Default Page</option>
                                <option value="headline" {{ $tpl === 'headline' ? 'selected' : '' }}>Headline - Full-width banner, high-res image</option>
                                <option value="feature_story" {{ $tpl === 'feature_story' ? 'selected' : '' }}>Feature Story - Sidebar or second-tier grid position</option>
                                <option value="standard_news" {{ $tpl === 'standard_news' ? 'selected' : '' }}>Standard News - Regular list item in the main feed</option>
                                <option value="short_brief" {{ $tpl === 'short_brief' ? 'selected' : '' }}>Short/Brief - Text-only or ticker-style display</option>
                                <option value="event" {{ $tpl === 'event' ? 'selected' : '' }}>Event - Includes a Map button</option>
                            </select>
                        </label>
                        <small style="display:block; color:#6c757d; margin-top:.35rem;">This controls how the item is displayed on the public news/events page.</small>
                    </div>
                    <div>
                        <label>Published</label>
                        <label class="page-edit-toggle">
                            <span class="page-edit-toggle-text">
                                <strong>Publish this page</strong>
                                <span>Turn this on to show the page on the public site.</span>
                            </span>
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                        </label>
                    </div>
                    <div>
                        <label>Replace featured image</label>
                        <div class="feature-image-card">
                            <div class="feature-image-preview">
                                @if ($page->image_data_uri)
                                    <img src="{{ $page->image_data_uri }}" alt="{{ $page->title }}" id="edit-image-preview-img">
                                @else
                                    <img alt="Selected feature image preview" id="edit-image-preview-img" style="display:none;">
                                    <span id="edit-image-preview-empty">Upload an image or choose one from the Media Library.</span>
                                @endif
                            </div>
                            <div class="feature-image-actions">
                                <div>
                                    <label>Upload image
                                        <input type="file" name="image" id="edit-image-input" accept="image/*">
                                    </label>
                                </div>
                                <div>
                                    <label>Select from Media Library</label>
                                    <input type="hidden" name="media_path" id="edit-media-path" value="{{ old('media_path') }}">
                                    <div class="media-library-grid" id="edit-media-library">
                                        @forelse ($mediaFiles as $file)
                                            <button
                                                type="button"
                                                class="media-library-item {{ old('media_path') === $file['path'] ? 'is-selected' : '' }}"
                                                data-media-picker
                                                data-target-input="edit-media-path"
                                                data-target-image="edit-image-preview-img"
                                                data-target-empty="edit-image-preview-empty"
                                                data-file-input="edit-image-input"
                                                data-url="{{ $file['url'] }}"
                                                data-path="{{ $file['path'] }}"
                                            >
                                                <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}">
                                                <span>{{ $file['name'] }}</span>
                                            </button>
                                        @empty
                                            <p style="margin:0; color:#6a7d92;">No images in the Media Library yet.</p>
                                        @endforelse
                                    </div>
                                </div>
                                @if ($page->image_data_uri)
                                    <label class="page-edit-toggle">
                                        <span class="page-edit-toggle-text">
                                            <strong>Remove current image</strong>
                                            <span>Check this if you want to clear the current featured image.</span>
                                        </span>
                                        <input type="checkbox" name="remove_image" value="1">
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:.8rem;">
                    <label>Content
                        <textarea name="content">{{ old('content', $page->content) }}</textarea>
                    </label>
                </div>
                <div style="margin-top:1rem; display:flex; gap:.6rem;">
                    <button class="btn btn-p" type="submit">Save Changes</button>
                    <button class="btn btn-d" type="submit" form="delete-page-form" onclick="return confirm('Delete this page?')">Delete</button>
                </div>
            </form>
            <form id="delete-page-form" method="POST" action="{{ route('pinoycoop.admin.pages.destroy', $page) }}" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    <script>
        (function () {
            const fileInput = document.getElementById('edit-image-input');
            const hiddenInput = document.getElementById('edit-media-path');
            const previewImage = document.getElementById('edit-image-preview-img');
            const previewEmpty = document.getElementById('edit-image-preview-empty');
            const pickerButtons = document.querySelectorAll('[data-media-picker]');

            function showPreview(src) {
                if (!src) {
                    previewImage.style.display = 'none';
                    previewImage.removeAttribute('src');
                    if (previewEmpty) {
                        previewEmpty.style.display = 'block';
                    }
                    return;
                }

                previewImage.src = src;
                previewImage.style.display = 'block';
                if (previewEmpty) {
                    previewEmpty.style.display = 'none';
                }
            }

            fileInput.addEventListener('change', function (event) {
                const [file] = event.target.files;

                pickerButtons.forEach((button) => {
                    if (button.dataset.targetInput === 'edit-media-path') {
                        button.classList.remove('is-selected');
                    }
                });
                hiddenInput.value = '';

                if (!file) {
                    showPreview(@json($page->image_data_uri));
                    return;
                }

                showPreview(URL.createObjectURL(file));
            });

            pickerButtons.forEach((button) => {
                if (button.dataset.targetInput !== 'edit-media-path') {
                    return;
                }

                button.addEventListener('click', function () {
                    pickerButtons.forEach((item) => {
                        if (item.dataset.targetInput === 'edit-media-path') {
                            item.classList.remove('is-selected');
                        }
                    });
                    button.classList.add('is-selected');
                    hiddenInput.value = button.dataset.path;
                    fileInput.value = '';
                    showPreview(button.dataset.url);
                });
            });

            @if (old('media_path'))
                showPreview(@json(collect($mediaFiles)->firstWhere('path', old('media_path'))['url'] ?? $page->image_data_uri));
            @endif
        }());
    </script>
@endsection

