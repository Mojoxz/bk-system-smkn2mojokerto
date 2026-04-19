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
    /* Aktif — biru solid */
    .sig-tab-btn.active,
    .cam-tab-btn.active {
        background-color: #2563eb !important;
        color: #fff !important;
        border-color: #2563eb !important;
        box-shadow: 0 1px 4px rgba(37,99,235,.4);
    }
    .sig-tab-btn.active:hover,
    .cam-tab-btn.active:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }
    .sig-tab-btn.active:active,
    .cam-tab-btn.active:active {
        background-color: #1e40af !important;
        border-color: #1e40af !important;
        box-shadow: none;
    }
    /* Tidak aktif — abu-abu muda, selalu punya fill */
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
    /* Efek tekan — terlihat jelas di mobile */
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
        cursor: crosshair;
        touch-action: none;
        background: #fff;
        display: block;
        width: 100%;
        height: 170px;
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
            {{-- TANDA TANGAN (Gambar / Kamera / Upload) --}}
            {{-- ──────────────────────────────────────── --}}
            <div>
                <p class="block text-sm font-medium text-gray-700 mb-3">
                    Tanda Tangan
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </p>

                {{-- Tab 2 opsi --}}
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
                    <canvas id="signature-canvas" width="600" height="170"
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
    const DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];

    function fmtDate(d) {
        return DAYS[d.getDay()] + ', ' + d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
    }
    function fmtTime(d) {
        return String(d.getHours()).padStart(2,'0') + ':' +
               String(d.getMinutes()).padStart(2,'0') + ':' +
               String(d.getSeconds()).padStart(2,'0');
    }
    function nowLabel() {
        const n = new Date();
        return fmtDate(n) + ' ' + fmtTime(n);
    }

    /* Jalankan clock realtime untuk overlay kamera bukti foto */
    function tickClock() {
        const n = new Date();
        const elDate = document.getElementById('foto-cam-date');
        const elTime = document.getElementById('foto-cam-time');
        if (elDate) elDate.textContent = fmtDate(n);
        if (elTime) elTime.textContent = fmtTime(n);
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* ─── Bakar timestamp ke frame canvas ─── */
    function stampCanvas(ctx, cw, ch) {
        const n   = new Date();
        const txt1 = fmtDate(n);
        const txt2 = fmtTime(n);
        ctx.save();
        ctx.font = '13px ui-monospace, monospace';
        const pad = 8, lh = 18, boxH = lh * 2 + pad;
        const boxW = Math.max(ctx.measureText(txt1).width, ctx.measureText(txt2).width) + pad * 2;
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(pad, ch - boxH - pad, boxW, boxH);
        ctx.fillStyle = '#ffffff';
        ctx.fillText(txt1, pad * 2, ch - pad - lh);
        ctx.fillText(txt2, pad * 2, ch - pad);
        ctx.restore();
        return { date: txt1, time: txt2 };
    }

    /* ─── Kamera Bukti Foto ─── */
    let fotoStream = null;

    const fotoVideo      = document.getElementById('foto-video');
    const fotoCamOff     = document.getElementById('foto-cam-off');
    const fotoBtnStart   = document.getElementById('foto-btn-start');
    const fotoBtnSnap    = document.getElementById('foto-btn-snap');
    const fotoBtnStop    = document.getElementById('foto-btn-stop');
    const fotoCamStatus  = document.getElementById('foto-cam-status');
    const fotoRecDot     = document.getElementById('foto-rec-dot');
    const fotoSnapResult = document.getElementById('foto-snap-result');
    const fotoSnapImg    = document.getElementById('foto-snap-img');
    const fotoSnapTs     = document.getElementById('foto-snap-ts');
    const fotoBtnClear   = document.getElementById('foto-btn-clear-snap');
    const fotoCamData    = document.getElementById('foto-cam-data');
    const fotoCanvas     = document.getElementById('foto-hidden-canvas');

    fotoBtnStart.addEventListener('click', async function () {
        try {
            fotoStream = await navigator.mediaDevices.getUserMedia(
                { video: { facingMode: 'environment' }, audio: false }
            );
            fotoVideo.srcObject = fotoStream;
            fotoCamOff.style.display    = 'none';
            fotoBtnStart.style.display  = 'none';
            fotoBtnSnap.style.display   = '';
            fotoBtnStop.style.display   = '';
            fotoRecDot.classList.add('live');
            fotoCamStatus.textContent   = 'Kamera aktif';
        } catch (err) {
            fotoCamStatus.textContent = 'Akses kamera ditolak atau tidak tersedia.';
        }
    });

    fotoBtnStop.addEventListener('click', function () {
        if (fotoStream) { fotoStream.getTracks().forEach(function (t) { t.stop(); }); fotoStream = null; }
        fotoVideo.srcObject   = null;
        fotoBtnStart.style.display = '';
        fotoBtnSnap.style.display  = 'none';
        fotoBtnStop.style.display  = 'none';
        fotoRecDot.classList.remove('live');
        fotoCamStatus.textContent  = '';
    });

    fotoBtnSnap.addEventListener('click', function () {
        const w = fotoVideo.videoWidth  || 640;
        const h = fotoVideo.videoHeight || 480;
        fotoCanvas.width  = w;
        fotoCanvas.height = h;
        const ctx = fotoCanvas.getContext('2d');
        ctx.drawImage(fotoVideo, 0, 0, w, h);
        const stamp = stampCanvas(ctx, w, h);
        const dataUrl = fotoCanvas.toDataURL('image/jpeg', 0.92);
        fotoCamData.value = dataUrl;
        fotoSnapImg.src   = dataUrl;
        fotoSnapTs.textContent = stamp.date + ' ' + stamp.time;
        fotoSnapResult.classList.remove('hidden');
    });

    fotoBtnClear.addEventListener('click', function () {
        fotoCamData.value = '';
        fotoSnapImg.src   = '';
        fotoSnapResult.classList.add('hidden');
    });

    /* ─── Tab Bukti Foto ─── */
    const fotoTabCam    = document.getElementById('foto-tab-cam');
    const fotoTabUpload = document.getElementById('foto-tab-upload');
    const fotoPanelCam  = document.getElementById('foto-panel-cam');
    const fotoPanelUp   = document.getElementById('foto-panel-upload');

    var STYLE_BTN_ACTIVE   = 'border:1.5px solid #2563eb;background:#2563eb;color:#fff;';
    var STYLE_BTN_INACTIVE = 'border:1.5px solid #d1d5db;background:#f3f4f6;color:#374151;';
    var STYLE_BTN_BASE     = 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;' +
                             'font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;' +
                             'transition:background .15s,border-color .15s,transform .1s;' +
                             'user-select:none;-webkit-tap-highlight-color:transparent;' +
                             'white-space:nowrap;line-height:1.4;';

    function setTabActive(btn) {
        btn.setAttribute('style', STYLE_BTN_BASE + STYLE_BTN_ACTIVE);
    }
    function setTabInactive(btn) {
        btn.setAttribute('style', STYLE_BTN_BASE + STYLE_BTN_INACTIVE);
    }

    fotoTabCam.addEventListener('click', function () {
        setTabActive(fotoTabCam);
        setTabInactive(fotoTabUpload);
        fotoPanelCam.classList.remove('hidden');
        fotoPanelUp.classList.add('hidden');
        document.getElementById('photo_evidence').value = '';
        document.getElementById('foto-upload-preview').classList.add('hidden');
    });
    fotoTabUpload.addEventListener('click', function () {
        setTabActive(fotoTabUpload);
        setTabInactive(fotoTabCam);
        fotoPanelUp.classList.remove('hidden');
        fotoPanelCam.classList.add('hidden');
        fotoCamData.value = '';
        if (fotoStream) { fotoStream.getTracks().forEach(function (t) { t.stop(); }); fotoStream = null; }
    });

    /* Drop-zone upload foto */
    const fotoDropZone   = document.getElementById('foto-drop-zone');
    const fotoFileInput  = document.getElementById('photo_evidence');
    const fotoUpPrev     = document.getElementById('foto-upload-preview');
    const fotoUpImg      = document.getElementById('foto-upload-img');
    const fotoUpName     = document.getElementById('foto-upload-name');
    const fotoBtnRemUp   = document.getElementById('foto-btn-remove-upload');

    fotoDropZone.addEventListener('click', function () { fotoFileInput.click(); });
    fotoDropZone.addEventListener('dragover',  function (e) { e.preventDefault(); fotoDropZone.classList.add('dragover'); });
    fotoDropZone.addEventListener('dragleave', function ()  { fotoDropZone.classList.remove('dragover'); });
    fotoDropZone.addEventListener('drop', function (e) {
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
    const sigTabPad    = document.getElementById('sig-tab-pad');
    const sigTabUpload = document.getElementById('sig-tab-upload');
    const sigPanelPad  = document.getElementById('sig-panel-pad');
    const sigPanelUp   = document.getElementById('sig-panel-upload');

    function switchSigTab(active) {
        if (active === 'pad') {
            setTabActive(sigTabPad);
            setTabInactive(sigTabUpload);
            sigPanelPad.classList.remove('hidden');
            sigPanelUp.classList.add('hidden');
            document.getElementById('signature-upload-input').value = '';
            document.getElementById('sig-upload-preview').classList.add('hidden');
        } else {
            setTabActive(sigTabUpload);
            setTabInactive(sigTabPad);
            sigPanelUp.classList.remove('hidden');
            sigPanelPad.classList.add('hidden');
            document.getElementById('signature-data').value = '';
        }
    }

    sigTabPad.addEventListener('click',    function () { switchSigTab('pad');    });
    sigTabUpload.addEventListener('click', function () { switchSigTab('upload'); });

    /* Drop-zone upload TTD */
    const sigDropZone  = document.getElementById('sig-drop-zone');
    const sigFileInput = document.getElementById('signature-upload-input');
    const sigUpPrev    = document.getElementById('sig-upload-preview');
    const sigUpImg     = document.getElementById('sig-upload-img');
    const sigUpName    = document.getElementById('sig-upload-name');
    const sigBtnRemUp  = document.getElementById('sig-btn-remove-upload');

    sigDropZone.addEventListener('click', function () { sigFileInput.click(); });
    sigDropZone.addEventListener('dragover',  function (e) { e.preventDefault(); sigDropZone.classList.add('dragover'); });
    sigDropZone.addEventListener('dragleave', function ()  { sigDropZone.classList.remove('dragover'); });
    sigDropZone.addEventListener('drop', function (e) {
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

    /* ─── Helper: tampilkan preview file upload ─── */
    function handleUploadFile(file, input, imgEl, nameEl, wrapEl, maxMb) {
        if (!file || !file.type.startsWith('image/')) return;
        if (file.size > maxMb * 1024 * 1024) {
            alert('Ukuran file melebihi ' + maxMb + ' MB.');
            return;
        }
        try { const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files; } catch (e) {}
        const reader = new FileReader();
        reader.onload = function (ev) {
            imgEl.src  = ev.target.result;
            nameEl.textContent = file.name;
            wrapEl.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    /* ─── Signature Pad ─── */
    const canvas  = document.getElementById('signature-canvas');
    const ctx     = canvas.getContext('2d');
    const sigInput = document.getElementById('signature-data');
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
        const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); current.push(p);
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
        strokes.forEach(function (stroke) {
            if (!stroke.length) return;
            ctx.beginPath(); ctx.moveTo(stroke[0].x, stroke[0].y);
            stroke.forEach(function (p) { ctx.lineTo(p.x, p.y); });
            ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 2.2;
            ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.stroke();
        });
    }
    function saveSignature() {
        sigInput.value = strokes.length ? canvas.toDataURL('image/png') : '';
    }

    canvas.addEventListener('mousedown',  startDraw);
    canvas.addEventListener('mousemove',  draw);
    canvas.addEventListener('mouseup',    endDraw);
    canvas.addEventListener('mouseleave', endDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove',  draw,      { passive: false });
    canvas.addEventListener('touchend',   endDraw);

    document.getElementById('btn-undo').addEventListener('click', function () {
        strokes.pop(); redraw(); saveSignature();
    });
    document.getElementById('btn-clear').addEventListener('click', function () {
        strokes = []; current = [];
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        sigInput.value = '';
    });

    new ResizeObserver(resizeCanvas).observe(canvas);

    /* ─── Submit: nonaktifkan field yang tidak dipakai ─── */
    document.getElementById('report-form').addEventListener('submit', function () {
        const padActive = !sigPanelPad.classList.contains('hidden');
        const upActive  = !sigPanelUp.classList.contains('hidden');

        if (!padActive) sigInput.disabled = true;
        if (!upActive)  sigFileInput.disabled = true;

        /* Bukti foto: jika panel kamera aktif, nonaktifkan file upload */
        if (!fotoPanelUp.classList.contains('hidden')) {
            fotoCamData.disabled = true;
        } else {
            fotoFileInput.disabled = true;
        }

        /* Matikan stream kamera bukti foto */
        if (fotoStream) fotoStream.getTracks().forEach(function (t) { t.stop(); });
    });

})();
</script>
@endpush
