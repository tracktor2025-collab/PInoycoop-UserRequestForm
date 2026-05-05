@extends('pinoycoop_admin.layouts.app', ['title' => 'Create Page'])

@section('content')
    <style>
        .page-create-toggle {
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

        .page-create-toggle input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: #00a7e1;
            flex: 0 0 auto;
            box-shadow: none;
        }

        .page-create-toggle-text {
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }

        .page-create-toggle-text strong {
            color: #20304f;
            font-size: .95rem;
        }

        .page-create-toggle-text span {
            color: #6a7d92;
            font-size: .84rem;
            line-height: 1.5;
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
            display: none;
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
        <h2>Create Page</h2>
        <a class="btn btn-g" href="{{ route('pinoycoop.admin.pages.index') }}">Back to Pages</a>
    </div>
    <div class="card">
        <div class="head">New Page Form</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.pages.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid2">
                    <div>
                        <label>Title
                            <input type="text" name="title" value="{{ old('title') }}" required>
                        </label>
                    </div>
                    <div>
                        <label>Slug (optional)
                            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="auto-generated from title">
                        </label>
                    </div>
                    <div>
                        <label>Template
                            <select name="template">
                                @php($tpl = old('template', 'page'))
                                <option value="page" {{ $tpl === 'page' ? 'selected' : '' }}>Default Page</option>
                                <option value="headline" {{ $tpl === 'headline' ? 'selected' : '' }}>Headline - Full-width banner, high-res image</option>
                                <option value="feature_story" {{ $tpl === 'feature_story' ? 'selected' : '' }}>Feature Story - Sidebar or second-tier grid position</option>
                                <option value="standard_news" {{ $tpl === 'standard_news' ? 'selected' : '' }}>Standard News - Regular list item in the main feed</option>
                                <option value="short_brief" {{ $tpl === 'short_brief' ? 'selected' : '' }}>Short/Brief - Text-only or ticker-style display</option>
                                <option value="event" {{ $tpl === 'event' ? 'selected' : '' }}>Event - Includes a Map button</option>
                            </select>
                        </label>
                        <small style="display:block; color:#6c757d; margin-top:.35rem;">Choose how this content should appear in the public news/events feed.</small>
                    </div>
                    <div>
                        <label>Publish now</label>
                        <label class="page-create-toggle">
                            <span class="page-create-toggle-text">
                                <strong>Publish this page</strong>
                                <span>Turn this on to make the page visible on the public site right away.</span>
                            </span>
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        </label>
                    </div>
                    <div>
                        <label>Featured image</label>
                        <div class="feature-image-card">
                            <div class="feature-image-preview" id="create-image-preview">
                                <img id="create-image-preview-img" alt="Selected feature image preview">
                                <span id="create-image-preview-empty">Upload an image or choose one from the Media Library.</span>
                            </div>
                            <div class="feature-image-actions">
                                <div>
                                    <label>Upload image
                                        <input type="file" name="image" id="create-image-input" accept="image/*">
                                    </label>
                                </div>
                                <div>
                                    <label>Select from Media Library</label>
                                    <input type="hidden" name="media_path" id="create-media-path" value="{{ old('media_path') }}">
                                    <div class="media-library-grid" id="create-media-library">
                                        @forelse ($mediaFiles as $file)
                                            <button
                                                type="button"
                                                class="media-library-item {{ old('media_path') === $file['path'] ? 'is-selected' : '' }}"
                                                data-media-picker
                                                data-target-input="create-media-path"
                                                data-target-image="create-image-preview-img"
                                                data-target-empty="create-image-preview-empty"
                                                data-file-input="create-image-input"
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
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:.8rem;">
                    <label>Content
                        <textarea name="content">{{ old('content') }}</textarea>
                    </label>
                </div>
                <div style="margin-top:1rem;">
                    <button class="btn btn-p" type="submit">Save Page</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function () {
            const fileInput = document.getElementById('create-image-input');
            const hiddenInput = document.getElementById('create-media-path');
            const previewImage = document.getElementById('create-image-preview-img');
            const previewEmpty = document.getElementById('create-image-preview-empty');
            const pickerButtons = document.querySelectorAll('[data-media-picker]');

            function showPreview(src) {
                if (!src) {
                    previewImage.style.display = 'none';
                    previewImage.removeAttribute('src');
                    previewEmpty.style.display = 'block';
                    return;
                }

                previewImage.src = src;
                previewImage.style.display = 'block';
                previewEmpty.style.display = 'none';
            }

            fileInput.addEventListener('change', function (event) {
                const [file] = event.target.files;

                pickerButtons.forEach((button) => button.classList.remove('is-selected'));
                hiddenInput.value = '';

                if (!file) {
                    showPreview('');
                    return;
                }

                showPreview(URL.createObjectURL(file));
            });

            pickerButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    pickerButtons.forEach((item) => item.classList.remove('is-selected'));
                    button.classList.add('is-selected');
                    hiddenInput.value = button.dataset.path;
                    fileInput.value = '';
                    showPreview(button.dataset.url);
                });
            });

            @if (old('media_path'))
                showPreview(@json(collect($mediaFiles)->firstWhere('path', old('media_path'))['url'] ?? ''));
            @endif
        }());
    </script>
@endsection
