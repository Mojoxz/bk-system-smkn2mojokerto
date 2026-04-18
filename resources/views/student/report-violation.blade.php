<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Pelanggaran - BK SMKN 2 Mojokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ──────────────────────────────────────────
           Signature section styles
        ────────────────────────────────────────── */
        .sig-tab-btn {
            transition: all .2s;
        }
        .sig-tab-btn.active {
            background-color: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
        .sig-tab-btn:not(.active) {
            background-color: #fff;
            color: #374151;
            border-color: #d1d5db;
        }

        #signature-canvas-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
        }
        #signature-canvas {
            border: 2px solid #d1d5db;
            border-radius: 8px;
            cursor: crosshair;
            touch-action: none;
            background: #fff;
            display: block;
            width: 100%;
            height: 180px;
        }
        #signature-canvas.drawing {
            border-color: #2563eb;
        }

        .sig-action-btn {
            font-size: 0.75rem;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            border: 1px solid;
            cursor: pointer;
            transition: opacity .15s;
        }
        .sig-action-btn:hover { opacity: .8; }

        .sig-preview-box {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 8px;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80px;
            max-width: 300px;
        }
        .sig-preview-box img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }

        /* drag-over state for upload zone */
        #upload-drop-zone.dragover {
            border-color: #2563eb;
            background: #eff6ff;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('student.partials.navbar')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Lapor Pelanggaran</h2>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <form id="report-form" method="POST" action="{{ route('student.report.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- ── Jenis Pelanggaran ── --}}
                <div class="mb-6">
                    <label for="violation_type_id" class="block text-gray-700 font-semibold mb-2">
                        Jenis Pelanggaran <span class="text-red-500">*</span>
                    </label>
                    <select id="violation_type_id" name="violation_type_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            required>
                        <option value="">-- Pilih Jenis Pelanggaran --</option>
                        @foreach($violationTypes as $categoryName => $types)
                            <optgroup label="{{ $categoryName }}">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->points }} poin)
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- ── Keterangan ── --}}
                <div class="mb-6">
                    <label for="description" class="block text-gray-700 font-semibold mb-2">
                        Keterangan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                              placeholder="Jelaskan detail pelanggaran yang terjadi..."
                              required>{{ old('description') }}</textarea>
                </div>

                {{-- ── Bukti Foto ── --}}
                <div class="mb-6">
                    <label for="photo_evidence" class="block text-gray-700 font-semibold mb-2">
                        Bukti Foto <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="file" id="photo_evidence" name="photo_evidence" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG — maks 2 MB</p>
                </div>

                {{-- ═══════════════════════════════════════════
                     TANDA TANGAN
                ════════════════════════════════════════════ --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-3">
                        Tanda Tangan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>

                    {{-- Tab toggle --}}
                    <div class="flex gap-2 mb-4">
                        <button type="button" id="tab-pad"
                                class="sig-tab-btn active px-4 py-2 rounded-lg border text-sm font-medium flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.415.586H9v-2.414a2 2 0 01.586-1.414z"/>
                            </svg>
                            Gambar Langsung
                        </button>
                        <button type="button" id="tab-upload"
                                class="sig-tab-btn px-4 py-2 rounded-lg border text-sm font-medium flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload Foto
                        </button>
                    </div>

                    {{-- ── Panel: Gambar Langsung ── --}}
                    <div id="panel-pad">
                        <div id="signature-canvas-wrapper">
                            <canvas id="signature-canvas" width="600" height="180"
                                    aria-label="Area tanda tangan"></canvas>
                        </div>
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            <button type="button" id="btn-undo"
                                    class="sig-action-btn border-gray-300 text-gray-600 bg-white"
                                    title="Undo">
                                ↩ Undo
                            </button>
                            <button type="button" id="btn-clear"
                                    class="sig-action-btn border-red-300 text-red-600 bg-white"
                                    title="Hapus">
                                ✕ Hapus
                            </button>
                            <span class="text-xs text-gray-400 ml-1">Gambar tanda tangan Anda di area abu-abu di atas</span>
                        </div>
                        {{-- Hidden input yang akan diisi base64 --}}
                        <input type="hidden" id="signature-data" name="signature">
                    </div>

                    {{-- ── Panel: Upload Foto ── --}}
                    <div id="panel-upload" class="hidden">
                        {{-- Drop-zone --}}
                        <div id="upload-drop-zone"
                             class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mx-auto mb-2"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p class="text-sm text-gray-500">
                                Klik atau seret foto tanda tangan ke sini
                            </p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG — maks 1 MB</p>
                            <input type="file" id="signature-upload-input" name="signature_upload"
                                   accept="image/*" class="hidden">
                        </div>

                        {{-- Preview --}}
                        <div id="upload-preview-wrapper" class="mt-3 hidden">
                            <div class="flex items-start gap-4">
                                <div class="sig-preview-box">
                                    <img id="upload-preview-img" src="" alt="Preview tanda tangan">
                                </div>
                                <button type="button" id="btn-remove-upload"
                                        class="sig-action-btn border-red-300 text-red-600 bg-white mt-2">
                                    ✕ Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ── / TANDA TANGAN ── --}}

                {{-- ── Catatan ── --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <p class="text-yellow-700">
                        <strong>Catatan:</strong> Laporan pelanggaran akan diverifikasi oleh guru BK.
                        Status akan diperbarui setelah proses verifikasi selesai.
                    </p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        Kirim Laporan
                    </button>
                    <a href="{{ route('student.dashboard') }}"
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 font-semibold">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         JAVASCRIPT — Signature Pad & Upload
    ════════════════════════════════════════════ --}}
    <script>
    (function () {
        /* ── Element refs ── */
        const tabPad        = document.getElementById('tab-pad');
        const tabUpload     = document.getElementById('tab-upload');
        const panelPad      = document.getElementById('panel-pad');
        const panelUpload   = document.getElementById('panel-upload');
        const canvas        = document.getElementById('signature-canvas');
        const ctx           = canvas.getContext('2d');
        const sigInput      = document.getElementById('signature-data');
        const btnUndo       = document.getElementById('btn-undo');
        const btnClear      = document.getElementById('btn-clear');
        const dropZone      = document.getElementById('upload-drop-zone');
        const fileInput     = document.getElementById('signature-upload-input');
        const previewWrap   = document.getElementById('upload-preview-wrapper');
        const previewImg    = document.getElementById('upload-preview-img');
        const btnRemoveUpload = document.getElementById('btn-remove-upload');
        const form          = document.getElementById('report-form');

        /* ─────────────────────────────────────────
           TAB SWITCHING
        ───────────────────────────────────────── */
        function switchTab(tab) {
            if (tab === 'pad') {
                tabPad.classList.add('active');
                tabUpload.classList.remove('active');
                panelPad.classList.remove('hidden');
                panelUpload.classList.add('hidden');
                // clear upload input so it won't submit
                fileInput.value = '';
                previewWrap.classList.add('hidden');
            } else {
                tabUpload.classList.add('active');
                tabPad.classList.remove('active');
                panelUpload.classList.remove('hidden');
                panelPad.classList.add('hidden');
                // clear canvas data
                sigInput.value = '';
            }
        }

        tabPad.addEventListener('click', () => switchTab('pad'));
        tabUpload.addEventListener('click', () => switchTab('upload'));

        /* ─────────────────────────────────────────
           SIGNATURE PAD
        ───────────────────────────────────────── */
        let drawing  = false;
        let strokes  = [];          // array of arrays of points for undo
        let current  = [];          // current stroke

        /* Scale canvas drawing buffer to CSS size */
        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            const dpr  = window.devicePixelRatio || 1;

            // Save existing drawing
            const snapshot = canvas.toDataURL();

            canvas.width  = rect.width  * dpr;
            canvas.height = rect.height * dpr;
            ctx.scale(dpr, dpr);

            // Restore drawing
            if (strokes.length) {
                redraw();
            }
        }

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const src  = e.touches ? e.touches[0] : e;
            return {
                x: src.clientX - rect.left,
                y: src.clientY - rect.top,
            };
        }

        function startDraw(e) {
            e.preventDefault();
            drawing = true;
            current = [];
            canvas.classList.add('drawing');
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            current.push(pos);
        }

        function draw(e) {
            if (!drawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth   = 2.2;
            ctx.lineCap     = 'round';
            ctx.lineJoin    = 'round';
            ctx.stroke();
            current.push(pos);
        }

        function endDraw(e) {
            if (!drawing) return;
            drawing = false;
            canvas.classList.remove('drawing');
            if (current.length) {
                strokes.push([...current]);
                current = [];
                saveSignature();
            }
        }

        function redraw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            strokes.forEach(function(stroke) {
                if (!stroke.length) return;
                ctx.beginPath();
                ctx.moveTo(stroke[0].x, stroke[0].y);
                stroke.forEach(function(p) { ctx.lineTo(p.x, p.y); });
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth   = 2.2;
                ctx.lineCap     = 'round';
                ctx.lineJoin    = 'round';
                ctx.stroke();
            });
        }

        function saveSignature() {
            if (strokes.length === 0) {
                sigInput.value = '';
                return;
            }
            sigInput.value = canvas.toDataURL('image/png');
        }

        // Mouse events
        canvas.addEventListener('mousedown',  startDraw);
        canvas.addEventListener('mousemove',  draw);
        canvas.addEventListener('mouseup',    endDraw);
        canvas.addEventListener('mouseleave', endDraw);

        // Touch events
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove',  draw,      { passive: false });
        canvas.addEventListener('touchend',   endDraw);

        // Undo
        btnUndo.addEventListener('click', function () {
            strokes.pop();
            redraw();
            saveSignature();
        });

        // Clear
        btnClear.addEventListener('click', function () {
            strokes  = [];
            current  = [];
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            sigInput.value = '';
        });

        // Resize observer
        const ro = new ResizeObserver(resizeCanvas);
        ro.observe(canvas);

        /* ─────────────────────────────────────────
           SIGNATURE UPLOAD
        ───────────────────────────────────────── */
        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            if (file.size > 1024 * 1024) {
                alert('Ukuran file melebihi 1 MB. Harap pilih file yang lebih kecil.');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src        = e.target.result;
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        // Click to browse
        dropZone.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (fileInput.files.length) handleFile(fileInput.files[0]);
        });

        // Drag & drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', function() {
            dropZone.classList.remove('dragover');
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) {
                // Put into file input via DataTransfer (modern browsers)
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;
                } catch(err) {}
                handleFile(file);
            }
        });

        // Remove upload
        btnRemoveUpload.addEventListener('click', function() {
            fileInput.value   = '';
            previewImg.src    = '';
            previewWrap.classList.add('hidden');
        });

        /* ─────────────────────────────────────────
           FORM SUBMIT — ensure correct field names
           - Pad mode   → name="signature"  (base64)
           - Upload mode→ name="signature_upload" (file)
        ───────────────────────────────────────── */
        form.addEventListener('submit', function() {
            const isPad = !panelPad.classList.contains('hidden');
            if (isPad) {
                // Make sure the hidden input is set (already done on each stroke)
                // Disable the upload input so it won't be sent
                fileInput.disabled = true;
            } else {
                // Disable the hidden text input so it won't interfere
                sigInput.disabled = true;
            }
        });

    })();
    </script>
</body>
</html>
