@extends('student.layouts.app')

@section('title', 'Lapor Pelanggaran')
@section('heading', 'Lapor Pelanggaran')
@section('subheading', 'Isi formulir berikut untuk melaporkan pelanggaran')

@push('styles')
<style>
    .sig-tab-btn,
    .cam-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 8px;
        border: 1.5px solid;
        cursor: pointer;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
        transition: background-color .15s, color .15s, border-color .15s, box-shadow .15s;
        white-space: nowrap;
    }
    .sig-tab-btn.active,
    .cam-tab-btn.active {
        background-color: #2563eb !important;
        color: #fff !important;
        border-color: #2563eb !important;
        box-shadow: 0 1px 4px rgba(37,99,235,.4);
    }
    .sig-tab-btn:not(.active),
    .cam-tab-btn:not(.active) {
        background-color: #f3f4f6 !important;
        color: #374151 !important;
        border-color: #d1d5db !important;
        box-shadow: none;
    }
    .sig-tab-btn:not(.active):hover,
    .cam-tab-btn:not(.active):hover {
        background-color: #e0e7ff !important;
        color: #3730a3 !important;
        border-color: #a5b4fc !important;
    }
    .sig-tab-btn:not(.active):active,
    .cam-tab-btn:not(.active):active {
        background-color: #c7d2fe !important;
        color: #3730a3 !important;
        border-color: #818cf8 !important;
        transform: scale(0.97);
    }
    .sig-tab-btn.active:active,
    .cam-tab-btn.active:active {
        transform: scale(0.97);
    }

    /* Kamera */
    .cam-wrapper {
        position: relative;
        background: #000;
        border-radius: 0.5rem;
        overflow: hidden;
        width: 100%;
        aspect-ratio: 4 / 3;
        max-height: 320px;
    }
    .cam-wrapper video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .cam-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }
    .cam-timestamp {
        position: absolute;
        bottom: 10px;
        left: 10px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .cam-timestamp span {
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        font-size: 11px;
        font-family: ui-monospace, monospace;
        padding: 2px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .cam-rec-dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 9px;
        height: 9px;
        background: #ef4444;
        border-radius: 50%;
        opacity: 0;
    }
    .cam-rec-dot.live {
        animation: blink-rec 1s infinite;
    }
    @keyframes blink-rec {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0; }
    }
    .cam-off-state {
        position: absolute;
        inset: 0;
        background: #111;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .cam-off-state svg { opacity: .45; }
    .cam-off-state span { font-size: 12px; color: #9ca3af; }

    /* Signature canvas */
    #signature-canvas {
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        touch-action: none;
        background: #fff;
        display: block;
        width: 100%;
        height: 170px;
        /* PENTING: cursor crosshair supaya user tahu area ini bisa digambar */
        cursor: crosshair;
    }
    #signature-canvas.drawing { border-color: #2563eb; }

    /* Drop zone */
    .drop-zone { transition: border-color .15s, background .15s; }
    .drop-zone.dragover { border-color: #2563eb !important; background: #eff6ff !important; }

    /* Snap preview */
    .snap-preview {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 10px;
        padding: 10px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }
    .snap-preview img {
        max-height: 90px;
        max-width: 130px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }
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
        <form id="report-form" method="POST" action="{{ route('student.report.store') }}"
              enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- ──────────────────────────────────────── --}}
            {{-- Jenis Pelanggaran --}}
            {{-- ──────────────────────────────────────── --}}
            <div>
                <label for="violation_type_id"
                       class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jenis Pelanggaran <span class="text-red-500">*</span>
                </label>
                <select id="violation_type_id" name="violation_type_id" required
                        class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg bg-white
                               text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/30
                               focus:border-blue-500">
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

            {{-- ──────────────────────────────────────── --}}
            {{-- Keterangan --}}
            {{-- ──────────────────────────────────────── --}}
            <div>
                <label for="description"
                       class="block text-sm font-medium text-gray-700 mb-1.5">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                          placeholder="Jelaskan detail pelanggaran yang terjadi..."
                          class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg bg-white
                                 text-gray-900 placeholder:text-gray-400 resize-none
                                 focus:outline-none focus:ring-2 focus:ring-blue-500/30
                                 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            {{-- ──────────────────────────────────────── --}}
            {{-- BUKTI FOTO  (Kamera / Upload) --}}
            {{-- ──────────────────────────────────────── --}}
            <div>
                <p class="block text-sm font-medium text-gray-700 mb-3">
                    Bukti Foto
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </p>

                {{-- Tab --}}
                <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
                    <button type="button" id="foto-tab-cam"
                            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                                   font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;
                                   border:1.5px solid #2563eb;background:#2563eb;color:#fff;
                                   transition:background .15s,border-color .15s,transform .1s;
                                   user-select:none;-webkit-tap-highlight-color:transparent;
                                   white-space:nowrap;line-height:1.4">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0
                                     0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07
                                     7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Kamera Langsung
                    </button>
                    <button type="button" id="foto-tab-upload"
                            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                                   font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;
                                   border:1.5px solid #d1d5db;background:#f3f4f6;color:#374151;
                                   transition:background .15s,border-color .15s,transform .1s;
                                   user-select:none;-webkit-tap-highlight-color:transparent;
                                   white-space:nowrap;line-height:1.4">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0
                                     0L8 8m4-4v12"/>
                        </svg>
                        Upload File
                    </button>
                </div>

                {{-- Panel: Kamera --}}
                <div id="foto-panel-cam">
                    <div class="cam-wrapper">
                        <video id="foto-video" autoplay playsinline muted
                               aria-label="Tampilan kamera bukti foto"></video>

                        {{-- Overlay jam & tanggal --}}
                        <div class="cam-overlay">
                            <div class="cam-timestamp">
                                <span id="foto-cam-date"></span>
                                <span id="foto-cam-time"></span>
                            </div>
                            <div class="cam-rec-dot" id="foto-rec-dot"></div>
                        </div>

                        {{-- State kamera mati --}}
                        <div class="cam-off-state" id="foto-cam-off">
                            <svg width="44" height="44" fill="none" viewBox="0 0 24 24"
                                 stroke="white" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2
                                         0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0
                                         0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0
                                         01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Kamera belum aktif</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <button type="button" id="foto-btn-start"
                                class="text-xs px-3 py-1.5 rounded-md bg-blue-600 text-white
                                       hover:bg-blue-700 transition-colors">
                            Aktifkan Kamera
                        </button>
                        <button type="button" id="foto-btn-snap" style="display:none"
                                class="text-xs px-3 py-1.5 rounded-md border border-gray-300
                                       text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            📸 Ambil Foto
                        </button>
                        <button type="button" id="foto-btn-stop" style="display:none"
                                class="text-xs px-3 py-1.5 rounded-md border border-red-200
                                       text-red-600 bg-white hover:bg-red-50 transition-colors">
                            Matikan Kamera
                        </button>
                        <span id="foto-cam-status" class="text-xs text-gray-400"></span>
                    </div>

                    {{-- Hasil foto --}}
                    <div id="foto-snap-result" class="snap-preview hidden">
                        <img id="foto-snap-img" src="" alt="Foto bukti">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Foto berhasil diambil</p>
                            <p class="text-xs text-gray-500 mt-0.5" id="foto-snap-ts"></p>
                            <button type="button" id="foto-btn-clear-snap"
                                    class="mt-2 text-xs px-3 py-1 rounded-md border border-red-200
                                           text-red-600 bg-white hover:bg-red-50 transition-colors">
                                ✕ Hapus Foto
                            </button>
                        </div>
                    </div>

                    {{-- Hidden inputs untuk foto kamera --}}
                    <input type="hidden" id="foto-cam-data" name="photo_cam_data">
                </div>

                {{-- Panel: Upload --}}
                <div id="foto-panel-upload" class="hidden">
                    <div id="foto-drop-zone"
                         class="drop-zone border-2 border-dashed border-gray-200 rounded-lg p-6
                                text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50">
                        <svg class="w-7 h-7 text-gray-300 mx-auto mb-2" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021
                                     18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-sm text-gray-500">Klik atau seret foto ke sini</p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — maks 2 MB</p>
                        <input type="file" id="photo_evidence" name="photo_evidence"
                               accept="image/*" class="hidden">
                    </div>

                    <div id="foto-upload-preview" class="snap-preview hidden">
                        <img id="foto-upload-img" src="" alt="Preview">
                        <div>
                            <p class="text-sm font-medium text-gray-800">File dipilih</p>
                            <p class="text-xs text-gray-500 mt-0.5" id="foto-upload-name"></p>
                            <button type="button" id="foto-btn-remove-upload"
                                    class="mt-2 text-xs px-3 py-1 rounded-md border border-red-200
                                           text-red-600 bg-white hover:bg-red-50 transition-colors">
                                ✕ Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ──────────────────────────────────────── --}}
            {{-- TANDA TANGAN (Gambar / Upload) --}}
            {{-- ──────────────────────────────────────── --}}
            <div>
                <p class="block text-sm font-medium text-gray-700 mb-3">
                    Tanda Tangan
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </p>

                {{-- Tab --}}
                <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
                    <button type="button" id="sig-tab-pad"
                            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                                   font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;
                                   border:1.5px solid #2563eb;background:#2563eb;color:#fff;
                                   transition:background .15s,border-color .15s,transform .1s;
                                   user-select:none;-webkit-tap-highlight-color:transparent;
                                   white-space:nowrap;line-height:1.4">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828
                                     2.828L11.828 15.828a2 2 0 01-1.415.586H9v-2.414a2 2 0
                                     01.586-1.414z"/>
                        </svg>
                        Gambar Langsung
                    </button>
                    <button type="button" id="sig-tab-upload"
                            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                                   font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;
                                   border:1.5px solid #d1d5db;background:#f3f4f6;color:#374151;
                                   transition:background .15s,border-color .15s,transform .1s;
                                   user-select:none;-webkit-tap-highlight-color:transparent;
                                   white-space:nowrap;line-height:1.4">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0
                                     0L8 8m4-4v12"/>
                        </svg>
                        Upload Foto
                    </button>
                </div>

                {{-- Panel: Gambar pad --}}
                <div id="sig-panel-pad">
                    {{--
                        PENTING: width/height di sini hanya placeholder awal.
                        JS akan set ukuran sebenarnya saat initCanvas() dipanggil.
                        CSS width:100%; height:170px mengatur tampilan visual.
                    --}}
                    <canvas id="signature-canvas"
                            aria-label="Area tanda tangan"></canvas>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" id="btn-undo"
                                class="text-xs px-3 py-1.5 rounded-md border border-gray-300
                                       text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                            ↩ Undo
                        </button>
                        <button type="button" id="btn-clear"
                                class="text-xs px-3 py-1.5 rounded-md border border-red-200
                                       text-red-600 bg-white hover:bg-red-50 transition-colors">
                            ✕ Hapus
                        </button>
                        <span class="text-xs text-gray-400">Gambar tanda tangan di area atas</span>
                    </div>
                    {{--
                        name="signature" — cocok dengan $request->filled('signature') di controller.
                        Nilai diisi JS saat saveSignature().
                    --}}
                    <input type="hidden" id="signature-data" name="signature">
                </div>

                {{-- Panel: Upload TTD --}}
                <div id="sig-panel-upload" class="hidden">
                    <div id="sig-drop-zone"
                         class="drop-zone border-2 border-dashed border-gray-200 rounded-lg p-6
                                text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50">
                        <svg class="w-7 h-7 text-gray-300 mx-auto mb-2" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021
                                     18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-sm text-gray-500">Klik atau seret foto tanda tangan</p>
                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG — maks 1 MB</p>
                        <input type="file" id="signature-upload-input" name="signature_upload"
                               accept="image/*" class="hidden">
                    </div>
                    <div id="sig-upload-preview" class="snap-preview hidden">
                        <img id="sig-upload-img" src="" alt="Preview TTD">
                        <div>
                            <p class="text-sm font-medium text-gray-800">File dipilih</p>
                            <p class="text-xs text-gray-500 mt-0.5" id="sig-upload-name"></p>
                            <button type="button" id="sig-btn-remove-upload"
                                    class="mt-2 text-xs px-3 py-1 rounded-md border border-red-200
                                           text-red-600 bg-white hover:bg-red-50 transition-colors">
                                ✕ Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ──────────────────────────────────────── --}}
            {{-- Catatan & Actions --}}
            {{-- ──────────────────────────────────────── --}}
            <div class="flex gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3
                        text-sm text-amber-700">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Laporan akan diverifikasi oleh guru BK sebelum diproses.
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
                               px-5 py-2.5 rounded-lg transition-colors">
                    Kirim Laporan
                </button>
                <a href="{{ route('student.dashboard') }}"
                   class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700
                          text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Canvas tersembunyi untuk mengambil foto bukti --}}
    <canvas id="foto-hidden-canvas" class="hidden"></canvas>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ─── Utilitas tanggal & waktu (Bahasa Indonesia) ─── */
    var DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var MONTHS = ['Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];

    function fmtDate(d) {
        return DAYS[d.getDay()] + ', ' + d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
    }
    function fmtTime(d) {
        return String(d.getHours()).padStart(2,'0') + ':' +
               String(d.getMinutes()).padStart(2,'0') + ':' +
               String(d.getSeconds()).padStart(2,'0');
    }

    /* Clock realtime untuk overlay kamera */
    function tickClock() {
        var n = new Date();
        var elDate = document.getElementById('foto-cam-date');
        var elTime = document.getElementById('foto-cam-time');
        if (elDate) elDate.textContent = fmtDate(n);
        if (elTime) elTime.textContent = fmtTime(n);
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* Bakar timestamp ke frame canvas foto */
    function stampCanvas(ctx, cw, ch) {
        var n    = new Date();
        var txt1 = fmtDate(n);
        var txt2 = fmtTime(n);
        ctx.save();
        ctx.font = '13px ui-monospace, monospace';
        var pad = 8, lh = 18, boxH = lh * 2 + pad;
        var boxW = Math.max(ctx.measureText(txt1).width, ctx.measureText(txt2).width) + pad * 2;
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(pad, ch - boxH - pad, boxW, boxH);
        ctx.fillStyle = '#ffffff';
        ctx.fillText(txt1, pad * 2, ch - pad - lh);
        ctx.fillText(txt2, pad * 2, ch - pad);
        ctx.restore();
        return { date: txt1, time: txt2 };
    }

    /* ─── Signature Pad ─────────────────────────────────────────────────────
       Pendekatan sederhana: TIDAK ada DPR scaling, TIDAK ada ResizeObserver,
       TIDAK ada initCanvas. Canvas pakai ukuran tetap via CSS (width:100%,
       height:170px). Koordinat diambil langsung dari getBoundingClientRect()
       relatif CSS px — konsisten antara draw dan redraw.
    ──────────────────────────────────────────────────────────────────────── */

    var canvas   = document.getElementById('signature-canvas');
    var sigCtx   = canvas.getContext('2d');
    var sigInput = document.getElementById('signature-data');

    /* Set ukuran canvas internal = ukuran CSS setelah layout selesai */
    function syncCanvasSize() {
        var w = canvas.offsetWidth  || 600;
        var h = canvas.offsetHeight || 170;
        if (canvas.width !== w || canvas.height !== h) {
            canvas.width  = w;
            canvas.height = h;
            redrawStrokes(); /* gambar ulang setelah resize */
        }
    }

    var drawing = false;
    var strokes = []; /* Array<Array<{x,y}>> dalam CSS px */
    var current = [];

    function redrawStrokes() {
        sigCtx.clearRect(0, 0, canvas.width, canvas.height);
        strokes.forEach(function (stroke) {
            if (stroke.length < 2) return;
            sigCtx.beginPath();
            sigCtx.moveTo(stroke[0].x, stroke[0].y);
            for (var i = 1; i < stroke.length; i++) {
                sigCtx.lineTo(stroke[i].x, stroke[i].y);
            }
            sigCtx.strokeStyle = '#1e293b';
            sigCtx.lineWidth   = 2.2;
            sigCtx.lineCap     = 'round';
            sigCtx.lineJoin    = 'round';
            sigCtx.stroke();
        });
    }

    function saveSignature() {
        sigInput.value = strokes.length ? canvas.toDataURL('image/png') : '';
    }

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var src  = e.touches ? e.touches[0] : e;
        return { x: src.clientX - rect.left, y: src.clientY - rect.top };
    }

    function onStartDraw(e) {
        e.preventDefault();
        syncCanvasSize(); /* pastikan ukuran benar sebelum mulai gambar */
        drawing = true;
        current = [];
        canvas.classList.add('drawing');
        var p = getPos(e);
        sigCtx.beginPath();
        sigCtx.moveTo(p.x, p.y);
        current.push(p);
    }

    function onDraw(e) {
        if (!drawing) return;
        e.preventDefault();
        var p = getPos(e);
        sigCtx.lineTo(p.x, p.y);
        sigCtx.strokeStyle = '#1e293b';
        sigCtx.lineWidth   = 2.2;
        sigCtx.lineCap     = 'round';
        sigCtx.lineJoin    = 'round';
        sigCtx.stroke();
        current.push(p);
    }

    function onEndDraw() {
        if (!drawing) return;
        drawing = false;
        canvas.classList.remove('drawing');
        if (current.length > 0) {
            strokes.push(current.slice());
            current = [];
            saveSignature();
        }
    }

    canvas.addEventListener('mousedown',  onStartDraw);
    canvas.addEventListener('mousemove',  onDraw);
    canvas.addEventListener('mouseup',    onEndDraw);
    canvas.addEventListener('mouseleave', onEndDraw);
    canvas.addEventListener('touchstart', onStartDraw, { passive: false });
    canvas.addEventListener('touchmove',  onDraw,      { passive: false });
    canvas.addEventListener('touchend',   onEndDraw);

    document.getElementById('btn-undo').addEventListener('click', function () {
        strokes.pop();
        redrawStrokes();
        saveSignature();
    });

    document.getElementById('btn-clear').addEventListener('click', function () {
        strokes = [];
        current = [];
        sigCtx.clearRect(0, 0, canvas.width, canvas.height);
        sigInput.value = '';
    });

    /* Sinkronisasi ukuran canvas satu kali saat halaman siap */
    syncCanvasSize();

    /* ─── Kamera Bukti Foto ─── */
    var fotoStream = null;

    var fotoVideo      = document.getElementById('foto-video');
    var fotoCamOff     = document.getElementById('foto-cam-off');
    var fotoBtnStart   = document.getElementById('foto-btn-start');
    var fotoBtnSnap    = document.getElementById('foto-btn-snap');
    var fotoBtnStop    = document.getElementById('foto-btn-stop');
    var fotoCamStatus  = document.getElementById('foto-cam-status');
    var fotoRecDot     = document.getElementById('foto-rec-dot');
    var fotoSnapResult = document.getElementById('foto-snap-result');
    var fotoSnapImg    = document.getElementById('foto-snap-img');
    var fotoSnapTs     = document.getElementById('foto-snap-ts');
    var fotoBtnClear   = document.getElementById('foto-btn-clear-snap');
    var fotoCamData    = document.getElementById('foto-cam-data');
    var fotoCanvas     = document.getElementById('foto-hidden-canvas');

    fotoBtnStart.addEventListener('click', function () {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
            .then(function (stream) {
                fotoStream = stream;
                fotoVideo.srcObject = stream;
                fotoCamOff.style.display   = 'none';
                fotoBtnStart.style.display = 'none';
                fotoBtnSnap.style.display  = '';
                fotoBtnStop.style.display  = '';
                fotoRecDot.classList.add('live');
                fotoCamStatus.textContent  = 'Kamera aktif';
            })
            .catch(function () {
                fotoCamStatus.textContent = 'Akses kamera ditolak atau tidak tersedia.';
            });
    });

    fotoBtnStop.addEventListener('click', function () {
        if (fotoStream) { fotoStream.getTracks().forEach(function (t) { t.stop(); }); fotoStream = null; }
        fotoVideo.srcObject        = null;
        fotoBtnStart.style.display = '';
        fotoBtnSnap.style.display  = 'none';
        fotoBtnStop.style.display  = 'none';
        fotoRecDot.classList.remove('live');
        fotoCamStatus.textContent  = '';
        fotoCamOff.style.display   = '';
    });

    fotoBtnSnap.addEventListener('click', function () {
        var w = fotoVideo.videoWidth  || 640;
        var h = fotoVideo.videoHeight || 480;
        fotoCanvas.width  = w;
        fotoCanvas.height = h;
        var ctx = fotoCanvas.getContext('2d');
        ctx.drawImage(fotoVideo, 0, 0, w, h);
        var stamp = stampCanvas(ctx, w, h);
        var dataUrl = fotoCanvas.toDataURL('image/jpeg', 0.92);
        fotoCamData.value     = dataUrl;
        fotoSnapImg.src       = dataUrl;
        fotoSnapTs.textContent = stamp.date + ' ' + stamp.time;
        fotoSnapResult.classList.remove('hidden');
    });

    fotoBtnClear.addEventListener('click', function () {
        fotoCamData.value = '';
        fotoSnapImg.src   = '';
        fotoSnapResult.classList.add('hidden');
    });

    /* ─── Tab Bukti Foto ─── */
    var fotoTabCam    = document.getElementById('foto-tab-cam');
    var fotoTabUpload = document.getElementById('foto-tab-upload');
    var fotoPanelCam  = document.getElementById('foto-panel-cam');
    var fotoPanelUp   = document.getElementById('foto-panel-upload');

    var STYLE_BASE     = 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;' +
                         'font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;' +
                         'transition:background .15s,border-color .15s,transform .1s;' +
                         'user-select:none;-webkit-tap-highlight-color:transparent;' +
                         'white-space:nowrap;line-height:1.4;';
    var STYLE_ACTIVE   = 'border:1.5px solid #2563eb;background:#2563eb;color:#fff;';
    var STYLE_INACTIVE = 'border:1.5px solid #d1d5db;background:#f3f4f6;color:#374151;';

    function setActive(btn)   { btn.setAttribute('style', STYLE_BASE + STYLE_ACTIVE);   }
    function setInactive(btn) { btn.setAttribute('style', STYLE_BASE + STYLE_INACTIVE); }

    fotoTabCam.addEventListener('click', function () {
        setActive(fotoTabCam);
        setInactive(fotoTabUpload);
        fotoPanelCam.classList.remove('hidden');
        fotoPanelUp.classList.add('hidden');
        document.getElementById('photo_evidence').value = '';
        document.getElementById('foto-upload-preview').classList.add('hidden');
    });

    fotoTabUpload.addEventListener('click', function () {
        setActive(fotoTabUpload);
        setInactive(fotoTabCam);
        fotoPanelUp.classList.remove('hidden');
        fotoPanelCam.classList.add('hidden');
        fotoCamData.value = '';
        if (fotoStream) { fotoStream.getTracks().forEach(function (t) { t.stop(); }); fotoStream = null; }
    });

    /* Drop-zone upload foto */
    var fotoDropZone  = document.getElementById('foto-drop-zone');
    var fotoFileInput = document.getElementById('photo_evidence');
    var fotoUpPrev    = document.getElementById('foto-upload-preview');
    var fotoUpImg     = document.getElementById('foto-upload-img');
    var fotoUpName    = document.getElementById('foto-upload-name');
    var fotoBtnRemUp  = document.getElementById('foto-btn-remove-upload');

    fotoDropZone.addEventListener('click',    function ()  { fotoFileInput.click(); });
    fotoDropZone.addEventListener('dragover', function (e) { e.preventDefault(); fotoDropZone.classList.add('dragover'); });
    fotoDropZone.addEventListener('dragleave',function ()  { fotoDropZone.classList.remove('dragover'); });
    fotoDropZone.addEventListener('drop',     function (e) {
        e.preventDefault(); fotoDropZone.classList.remove('dragover');
        handleUploadFile(e.dataTransfer.files[0], fotoFileInput, fotoUpImg, fotoUpName, fotoUpPrev, 2);
    });
    fotoFileInput.addEventListener('change', function () {
        if (fotoFileInput.files.length)
            handleUploadFile(fotoFileInput.files[0], fotoFileInput, fotoUpImg, fotoUpName, fotoUpPrev, 2);
    });
    fotoBtnRemUp.addEventListener('click', function () {
        fotoFileInput.value = '';
        fotoUpImg.src       = '';
        fotoUpPrev.classList.add('hidden');
    });

    /* ─── Tab Tanda Tangan ─── */
    var sigTabPad    = document.getElementById('sig-tab-pad');
    var sigTabUpload = document.getElementById('sig-tab-upload');
    var sigPanelPad  = document.getElementById('sig-panel-pad');
    var sigPanelUp   = document.getElementById('sig-panel-upload');
    var sigFileInput = document.getElementById('signature-upload-input');

    function switchSigTab(active) {
        if (active === 'pad') {
            setActive(sigTabPad);
            setInactive(sigTabUpload);
            sigPanelPad.classList.remove('hidden');
            sigPanelUp.classList.add('hidden');
            sigFileInput.value = '';
            document.getElementById('sig-upload-preview').classList.add('hidden');
            /* Sinkronisasi ukuran canvas saat tab kembali ditampilkan */
            syncCanvasSize();
        } else {
            setActive(sigTabUpload);
            setInactive(sigTabPad);
            sigPanelUp.classList.remove('hidden');
            sigPanelPad.classList.add('hidden');
            sigInput.value = '';
        }
    }

    sigTabPad.addEventListener('click',    function () { switchSigTab('pad');    });
    sigTabUpload.addEventListener('click', function () { switchSigTab('upload'); });

    /* Drop-zone upload TTD */
    var sigDropZone = document.getElementById('sig-drop-zone');
    var sigUpPrev   = document.getElementById('sig-upload-preview');
    var sigUpImg    = document.getElementById('sig-upload-img');
    var sigUpName   = document.getElementById('sig-upload-name');
    var sigBtnRemUp = document.getElementById('sig-btn-remove-upload');

    sigDropZone.addEventListener('click',    function ()  { sigFileInput.click(); });
    sigDropZone.addEventListener('dragover', function (e) { e.preventDefault(); sigDropZone.classList.add('dragover'); });
    sigDropZone.addEventListener('dragleave',function ()  { sigDropZone.classList.remove('dragover'); });
    sigDropZone.addEventListener('drop',     function (e) {
        e.preventDefault(); sigDropZone.classList.remove('dragover');
        handleUploadFile(e.dataTransfer.files[0], sigFileInput, sigUpImg, sigUpName, sigUpPrev, 1);
    });
    sigFileInput.addEventListener('change', function () {
        if (sigFileInput.files.length)
            handleUploadFile(sigFileInput.files[0], sigFileInput, sigUpImg, sigUpName, sigUpPrev, 1);
    });
    sigBtnRemUp.addEventListener('click', function () {
        sigFileInput.value = '';
        sigUpImg.src       = '';
        sigUpPrev.classList.add('hidden');
    });

    /* ─── Helper: preview file upload ─── */
    function handleUploadFile(file, input, imgEl, nameEl, wrapEl, maxMb) {
        if (!file || !file.type.startsWith('image/')) return;
        if (file.size > maxMb * 1024 * 1024) {
            alert('Ukuran file melebihi ' + maxMb + ' MB.');
            return;
        }
        try { var dt = new DataTransfer(); dt.items.add(file); input.files = dt.files; } catch (e) {}
        var reader = new FileReader();
        reader.onload = function (ev) {
            imgEl.src          = ev.target.result;
            nameEl.textContent = file.name;
            wrapEl.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    /* ─── Submit: nonaktifkan field yang tidak dipakai ─── */
    document.getElementById('report-form').addEventListener('submit', function () {
        var padActive = !sigPanelPad.classList.contains('hidden');
        var upActive  = !sigPanelUp.classList.contains('hidden');

        if (!padActive) sigInput.disabled      = true;
        if (!upActive)  sigFileInput.disabled  = true;

        /* Bukti foto */
        if (!fotoPanelUp.classList.contains('hidden')) {
            fotoCamData.disabled   = true;
        } else {
            fotoFileInput.disabled = true;
        }

        /* Matikan stream kamera */
        if (fotoStream) fotoStream.getTracks().forEach(function (t) { t.stop(); });
    });

})();
</script>
@endpush
