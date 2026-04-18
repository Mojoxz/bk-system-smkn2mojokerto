@extends('student.layouts.app')

@section('title', 'Lapor Pelanggaran')
@section('heading', 'Lapor Pelanggaran')
@section('subheading', 'Isi formulir berikut untuk melaporkan pelanggaran')

@push('scripts')
<style>
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
    #signature-canvas {
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        cursor: crosshair;
        touch-action: none;
        background: #fff;
        display: block;
        width: 100%;
        height: 170px;
    }
    #signature-canvas.drawing { border-color: #2563eb; }
    #upload-drop-zone.dragover { border-color: #2563eb; background: #eff6ff; }
</style>
@endpush

@section('content')

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm">
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-5 sm:p-6">
        <form id="report-form" method="POST" action="{{ route('student.report.store') }}" enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            {{-- Jenis Pelanggaran --}}
            <div>
                <label for="violation_type_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jenis Pelanggaran <span class="text-red-500">*</span>
                </label>
                <select id="violation_type_id" name="violation_type_id" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg bg-white text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
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

            {{-- Keterangan --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                          placeholder="Jelaskan detail pelanggaran yang terjadi..."
                          class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg bg-white text-gray-900
                                 placeholder:text-gray-400 resize-none
                                 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            {{-- Bukti Foto --}}
            <div>
                <label for="photo_evidence" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Bukti Foto
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <input type="file" id="photo_evidence" name="photo_evidence" accept="image/*"
                       class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg bg-white
                              text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0
                              file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 cursor-pointer">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG — maks 2 MB</p>
            </div>

            {{-- Tanda Tangan --}}
            <div>
                <p class="block text-sm font-medium text-gray-700 mb-3">
                    Tanda Tangan
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </p>

                {{-- Tab toggle --}}
                <div class="flex gap-2 mb-4">
                    <button type="button" id="tab-pad"
                            class="sig-tab-btn active px-3.5 py-2 rounded-lg border text-xs font-medium flex items-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.415.586H9v-2.414a2 2 0 01.586-1.414z"/>
                        </svg>
                        Gambar Langsung
                    </button>
                    <button type="button" id="tab-upload"
                            class="sig-tab-btn px-3.5 py-2 rounded-lg border text-xs font-medium flex items-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload Foto
                    </button>
                </div>

                {{-- Panel: Gambar Langsung --}}
                <div id="panel-pad">
                    <canvas id="signature-canvas" width="600" height="170" aria-label="Area tanda tangan"></canvas>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" id="btn-undo"
                                class="text-xs px-3 py-1.5 rounded-md border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                            ↩ Undo
                        </button>
                        <button type="button" id="btn-clear"
                                class="text-xs px-3 py-1.5 rounded-md border border-red-200 text-red-600 bg-white hover:bg-red-50 transition-colors">
                            ✕ Hapus
                        </button>
                        <span class="text-xs text-gray-400">Gambar tanda tangan di area atas</span>
                    </div>
                    <input type="hidden" id="signature-data" name="signature">
                </div>

                {{-- Panel: Upload Foto --}}
                <div id="panel-upload" class="hidden">
                    <div id="upload-drop-zone"
                         class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center cursor-pointer
                                hover:border-blue-400 hover:bg-blue-50/50 transition-colors">
                        <svg class="w-7 h-7 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-sm text-gray-500">Klik atau seret foto tanda tangan</p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — maks 1 MB</p>
                        <input type="file" id="signature-upload-input" name="signature_upload" accept="image/*" class="hidden">
                    </div>

                    <div id="upload-preview-wrapper" class="mt-3 hidden">
                        <div class="flex items-start gap-3">
                            <div class="border border-gray-200 rounded-lg p-1.5 bg-gray-50">
                                <img id="upload-preview-img" src="" alt="Preview" class="max-h-24 max-w-48 object-contain">
                            </div>
                            <button type="button" id="btn-remove-upload"
                                    class="text-xs px-3 py-1.5 rounded-md border border-red-200 text-red-600 bg-white hover:bg-red-50 transition-colors mt-1">
                                ✕ Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="flex gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-700">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Laporan akan diverifikasi oleh guru BK sebelum diproses.
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    Kirim Laporan
                </button>
                <a href="{{ route('student.dashboard') }}"
                   class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    const tabPad         = document.getElementById('tab-pad');
    const tabUpload      = document.getElementById('tab-upload');
    const panelPad       = document.getElementById('panel-pad');
    const panelUpload    = document.getElementById('panel-upload');
    const canvas         = document.getElementById('signature-canvas');
    const ctx            = canvas.getContext('2d');
    const sigInput       = document.getElementById('signature-data');
    const btnUndo        = document.getElementById('btn-undo');
    const btnClear       = document.getElementById('btn-clear');
    const dropZone       = document.getElementById('upload-drop-zone');
    const fileInput      = document.getElementById('signature-upload-input');
    const previewWrap    = document.getElementById('upload-preview-wrapper');
    const previewImg     = document.getElementById('upload-preview-img');
    const btnRemoveUpload= document.getElementById('btn-remove-upload');
    const form           = document.getElementById('report-form');

    function switchTab(tab) {
        if (tab === 'pad') {
            tabPad.classList.add('active');
            tabUpload.classList.remove('active');
            panelPad.classList.remove('hidden');
            panelUpload.classList.add('hidden');
            fileInput.value = '';
            previewWrap.classList.add('hidden');
        } else {
            tabUpload.classList.add('active');
            tabPad.classList.remove('active');
            panelUpload.classList.remove('hidden');
            panelPad.classList.add('hidden');
            sigInput.value = '';
        }
    }

    tabPad.addEventListener('click', () => switchTab('pad'));
    tabUpload.addEventListener('click', () => switchTab('upload'));

    let drawing = false, strokes = [], current = [];

    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        const dpr  = window.devicePixelRatio || 1;
        canvas.width  = rect.width  * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);
        if (strokes.length) redraw();
    }

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
    }

    function startDraw(e) {
        e.preventDefault(); drawing = true; current = [];
        canvas.classList.add('drawing');
        const p = getPos(e);
        ctx.beginPath(); ctx.moveTo(p.x, p.y); current.push(p);
    }

    function draw(e) {
        if (!drawing) return; e.preventDefault();
        const p = getPos(e);
        ctx.lineTo(p.x, p.y);
        ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 2.2;
        ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.stroke();
        current.push(p);
    }

    function endDraw() {
        if (!drawing) return; drawing = false;
        canvas.classList.remove('drawing');
        if (current.length) { strokes.push([...current]); current = []; saveSignature(); }
    }

    function redraw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        strokes.forEach(stroke => {
            if (!stroke.length) return;
            ctx.beginPath(); ctx.moveTo(stroke[0].x, stroke[0].y);
            stroke.forEach(p => ctx.lineTo(p.x, p.y));
            ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 2.2;
            ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.stroke();
        });
    }

    function saveSignature() {
        sigInput.value = strokes.length ? canvas.toDataURL('image/png') : '';
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', endDraw);
    canvas.addEventListener('mouseleave', endDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', endDraw);

    btnUndo.addEventListener('click', () => { strokes.pop(); redraw(); saveSignature(); });
    btnClear.addEventListener('click', () => {
        strokes = []; current = [];
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        sigInput.value = '';
    });

    new ResizeObserver(resizeCanvas).observe(canvas);

    function handleFile(file) {
        if (!file || !file.type.startsWith('image/')) return;
        if (file.size > 1024 * 1024) { alert('Ukuran file melebihi 1 MB.'); return; }
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; previewWrap.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    }

    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => { if (fileInput.files.length) handleFile(fileInput.files[0]); });
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault(); dropZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file) {
            try { const dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files; } catch {}
            handleFile(file);
        }
    });

    btnRemoveUpload.addEventListener('click', () => {
        fileInput.value = ''; previewImg.src = ''; previewWrap.classList.add('hidden');
    });

    form.addEventListener('submit', () => {
        const isPad = !panelPad.classList.contains('hidden');
        if (isPad) { fileInput.disabled = true; }
        else { sigInput.disabled = true; }
    });
})();
</script>
@endpush
