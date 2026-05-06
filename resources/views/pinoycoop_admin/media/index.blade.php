@extends('pinoycoop_admin.layouts.app', ['title' => 'Admin Media'])

@section('content')
    <style>
        .preview-container { margin-top:1rem; padding:1rem; background:#f9fafb; border:2px dashed #d2dee9; border-radius:8px; display:none; text-align:center; }
        .preview-container.active { display:block; }
        .preview-image { max-width:100%; max-height:300px; margin-bottom:1rem; border-radius:8px; box-shadow:0 4px 12px rgba(32,48,79,.1); }
        .file-item { position:relative; cursor:pointer; transition:transform .15s ease; }
        .file-item:hover { transform:scale(1.02); }
        .media-thumb { width:72px; height:54px; border-radius:8px; object-fit:cover; background:#eef4f8; border:1px solid #dbe5ee; display:block; }
        .media-file-icon { width:72px; height:54px; border-radius:8px; background:#eef4f8; border:1px solid #dbe5ee; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; color:#607993; }
        .stored-files-table { max-height:calc(6 * 60px + 45px); overflow-y:auto; border-radius:8px; }
        .stored-files-table table { width:100%; }
        .preview-trigger { display:inline-block; padding:.4rem .6rem; background:rgba(0,167,225,.1); color:var(--p); border-radius:6px; font-size:.85rem; font-weight:600; transition:all .15s ease; }
        .file-item:hover .preview-trigger { background:var(--p); color:#fff; }
        .modal-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(32,48,79,.7); z-index:9998; align-items:center; justify-content:center; }
        .modal-overlay.active { display:flex; animation:fadeIn .2s ease; }
        .modal-content { background:#fff; border-radius:14px; max-width:90%; max-height:90vh; overflow:auto; box-shadow:0 30px 90px rgba(32,48,79,.3); animation:slideUp .3s ease; position:relative; }
        .modal-close { position:absolute; top:1rem; right:1rem; background:#f0f3f7; border:none; width:40px; height:40px; border-radius:50%; cursor:pointer; font-size:1.5rem; color:var(--t); transition:all .15s ease; z-index:9999; }
        .modal-close:hover { background:var(--p); color:#fff; }
        .modal-media { display:flex; align-items:center; justify-content:center; min-height:300px; padding:2rem; }
        .modal-media img { max-width:100%; max-height:70vh; border-radius:10px; }
        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
        @keyframes slideUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
        .file-info { padding:1rem; border-top:1px solid #edf2f7; }
        .file-info p { margin:.3rem 0; font-size:.85rem; color:#607993; }
    </style>

    <div class="top">
        <h2>Media Library</h2>
    </div>
    <div class="card" style="margin-bottom:1rem;">
        <div class="head">Upload File</div>
        <div class="body">
            <form id="uploadForm" method="POST" action="{{ route('pinoycoop.admin.media.store') }}" enctype="multipart/form-data">
                @csrf
                <label>Select file (max 5MB)
                    <input type="file" id="fileInput" name="file" required>
                </label>
                
                <label style="margin-top:.8rem; display:block;">File Name (for easy search)
                    <input type="text" id="fileName" name="file_name" placeholder="Enter a descriptive name for this file" required>
                </label>
                
                <div class="preview-container" id="previewContainer">
                    <div id="previewContent"></div>
                    <div style="margin-top:1rem; display:flex; gap:.5rem; justify-content:center;">
                        <button type="submit" class="btn btn-p">Confirm & Upload</button>
                        <button type="button" class="btn btn-d" onclick="cancelPreview()">Cancel</button>
                    </div>
                </div>

                <div style="margin-top:.8rem;" id="selectButtonContainer">
                    <button class="btn btn-p" type="submit" id="uploadBtn">Upload</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="head">Stored Files</div>
        <div class="body">
            <div class="stored-files-table">
                <table>
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Path</th>
                            <th>Size</th>
                            <th>Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($files as $file)
                            @php
                                $isImage = str_starts_with((string) $file['mime'], 'image/');
                                $size = (int) $file['size'];
                                $sizeLabel = $size >= 1048576
                                    ? number_format($size / 1048576, 2).' MB'
                                    : number_format(max(1, $size) / 1024, 1).' KB';
                            @endphp
                            <tr>
                                <td>{{ $file['name'] }}</td>
                                <td><code style="background:#f5f9fc; padding:.2rem .4rem; border-radius:4px; font-size:.85rem;">{{ $file['path'] }}</code></td>
                                <td>{{ $sizeLabel }}</td>
                                <td>
                                    <div class="file-item" onclick="showPreview({{ Illuminate\Support\Js::from($file['url']) }}, {{ Illuminate\Support\Js::from($file['name']) }}, {{ Illuminate\Support\Js::from($file['mime']) }})">
                                        @if($isImage)
                                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="media-thumb" loading="lazy">
                                        @else
                                            <span class="media-file-icon">FILE</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center; color:#999;">No files uploaded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal-overlay" id="previewModal" onclick="closePreviewModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <button class="modal-close" onclick="closePreviewModal()">×</button>
            <div class="modal-media" id="modalMedia"></div>
            <div class="file-info" id="fileInfo"></div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewContent = document.getElementById('previewContent');
        const selectButtonContainer = document.getElementById('selectButtonContainer');
        const uploadBtn = document.getElementById('uploadBtn');
        const uploadForm = document.getElementById('uploadForm');

        fileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (!file) return;

            // Auto-populate file name field with the selected file name (without extension)
            const fileNameWithoutExt = file.name.split('.').slice(0, -1).join('.');
            document.getElementById('fileName').value = fileNameWithoutExt || file.name;

            const reader = new FileReader();
            reader.onload = function(event) {
                previewContent.innerHTML = '';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.classList.add('preview-image');
                    previewContent.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = event.target.result;
                    video.controls = true;
                    video.style.maxWidth = '100%';
                    video.style.maxHeight = '300px';
                    video.style.borderRadius = '8px';
                    video.style.boxShadow = '0 4px 12px rgba(32,48,79,.1)';
                    previewContent.appendChild(video);
                } else {
                    previewContent.innerHTML = `
                        <div style="padding:2rem; background:#f9fafb; border-radius:8px;">
                            <div style="font-size:3rem; margin-bottom:1rem;">📄</div>
                            <p style="margin:0; font-weight:600;">File: ${file.name}</p>
                            <p style="margin:.5rem 0 0; color:#999;">Size: ${(file.size / 1024).toFixed(2)} KB</p>
                            <p style="margin:.3rem 0 0; color:#999;">Type: ${file.type || 'Unknown'}</p>
                        </div>
                    `;
                }

                previewContainer.classList.add('active');
                selectButtonContainer.style.display = 'none';
                uploadBtn.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        function cancelPreview() {
            fileInput.value = '';
            document.getElementById('fileName').value = '';
            previewContainer.classList.remove('active');
            selectButtonContainer.style.display = 'block';
            uploadBtn.style.display = 'inline-flex';
            previewContent.innerHTML = '';
        }

        function showPreview(url, fileName, mimeType) {
            const modal = document.getElementById('previewModal');
            const modalMedia = document.getElementById('modalMedia');
            const fileInfo = document.getElementById('fileInfo');
            
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            
            if ((mimeType || '').startsWith('image/') || imageExtensions.includes(fileExtension)) {
                modalMedia.innerHTML = `<img src="${url}" alt="${fileName}" />`;
            } else if ((mimeType || '').startsWith('video/') || ['mp4', 'webm', 'ogg', 'mov'].includes(fileExtension)) {
                modalMedia.innerHTML = `<video controls style="max-width:100%; max-height:70vh;"><source src="${url}" type="${mimeType || 'video/' + (fileExtension === 'mov' ? 'quicktime' : fileExtension)}"></video>`;
            } else {
                modalMedia.innerHTML = `
                    <div style="text-align:center; padding:2rem;">
                        <div style="font-size:4rem; margin-bottom:1rem;">📄</div>
                        <p style="margin:0; font-size:1rem;">File preview not available</p>
                        <p style="margin:.5rem 0 0; color:#999; font-size:.9rem;">${fileName}</p>
                        <a href="${url}" download style="display:inline-block; margin-top:1rem; color:var(--p); text-decoration:none; font-weight:600;">⬇️ Download File</a>
                    </div>
                `;
            }
            
            fileInfo.innerHTML = `<p><strong>File:</strong> ${fileName}</p><p><strong>Type:</strong> ${mimeType || 'Unknown'}</p>`;
            modal.classList.add('active');
        }

        function closePreviewModal(event) {
            if (event && event.target.id !== 'previewModal') return;
            const modal = document.getElementById('previewModal');
            modal.classList.remove('active');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePreviewModal();
            }
        });
    </script>
@endsection
