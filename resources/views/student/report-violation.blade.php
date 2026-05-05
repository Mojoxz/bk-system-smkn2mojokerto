@extends('student.layouts.app')

@section('title', 'Lapor Pelanggaran')
@section('heading', 'Lapor Pelanggaran')
@section('subheading', 'Isi formulir berikut untuk melaporkan pelanggaran')

@section('content')

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
            <ul style="margin:0;padding:0;list-style:none;">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="report-form" method="POST" action="{{ route('student.report.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ═══════════════════════════════════ --}}
        {{-- GRID DUA KOLOM --}}
        {{-- ═══════════════════════════════════ --}}
        <div id="report-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

            {{-- ══════════════ KOLOM KIRI ══════════════ --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Card: Data Pelanggaran --}}
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;">

                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#6b7280;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:.5;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Data Pelanggaran
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:12px;">
                        <label for="violation_type_id" style="font-size:13px;color:#374151;font-weight:500;">
                            Jenis Pelanggaran <span style="color:#ef4444;">*</span>
                        </label>
                        <select id="violation_type_id" name="violation_type_id" required
                                style="width:100%;font-size:13px;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#111827;outline:none;font-family:inherit;">
                            <option value="">-- Pilih Jenis Pelanggaran --</option>
                            @foreach($violationTypes as $categoryName => $types)
                                <optgroup label="{{ $categoryName }}">
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ $type->points }} poin)
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;">
                        <label for="description" style="font-size:13px;color:#374151;font-weight:500;">
                            Keterangan <span style="color:#ef4444;">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                                  placeholder="Jelaskan detail pelanggaran yang terjadi..."
                                  style="width:100%;font-size:13px;padding:8px 10px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#111827;outline:none;font-family:inherit;resize:vertical;min-height:100px;">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div style="display:flex;gap:8px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;font-size:12px;color:#92400e;align-items:flex-start;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Laporan akan diverifikasi oleh guru BK sebelum diproses.
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit"
                            style="background:#1d4ed8;color:#fff;font-size:13px;font-weight:500;padding:10px 22px;border-radius:8px;border:none;cursor:pointer;">
                        Kirim Laporan
                    </button>
                    <a href="{{ route('student.dashboard') }}"
                       style="background:#fff;color:#374151;font-size:13px;font-weight:500;padding:10px 18px;border-radius:8px;border:1px solid #d1d5db;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;">
                        Batal
                    </a>
                </div>

            </div>{{-- /kolom kiri --}}

            {{-- ══════════════ KOLOM KANAN ══════════════ --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- ─── Card: Bukti Foto ─── --}}
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;">

                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#6b7280;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:.5;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Bukti Foto
                        <span style="font-size:10px;font-weight:400;color:#9ca3af;text-transform:none;letter-spacing:0;">(opsional)</span>
                    </div>

                    <div style="display:flex;gap:6px;margin-bottom:14px;">
                        <button type="button" id="foto-tab-cam" onclick="switchFotoTab('cam')"
                                style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;border:1px solid #1d4ed8;background:#1d4ed8;color:#fff;cursor:pointer;white-space:nowrap;line-height:1.4;">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Kamera Langsung
                        </button>
                        <button type="button" id="foto-tab-upload" onclick="switchFotoTab('upload')"
                                style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;border:1px solid #d1d5db;background:#f3f4f6;color:#374151;cursor:pointer;white-space:nowrap;line-height:1.4;">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload File
                        </button>
                    </div>

                    {{-- Panel Kamera --}}
                    <div id="foto-panel-cam" style="display:block;">
                        <div style="position:relative;background:#0a0a0a;border-radius:8px;overflow:hidden;aspect-ratio:4/3;width:100%;">
                            <video id="foto-video" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;display:block;"></video>
                            <div id="foto-cam-off" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#111;">
                                <svg width="40" height="40" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.3;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span style="font-size:11px;color:#6b7280;">Kamera belum aktif</span>
                            </div>
                            <div style="position:absolute;bottom:8px;left:8px;display:flex;flex-direction:column;gap:2px;">
                                <span id="foto-cam-date" style="font-size:10px;font-family:ui-monospace,monospace;background:rgba(0,0,0,.55);color:#fff;padding:2px 6px;border-radius:4px;white-space:nowrap;"></span>
                                <span id="foto-cam-time" style="font-size:10px;font-family:ui-monospace,monospace;background:rgba(0,0,0,.55);color:#fff;padding:2px 6px;border-radius:4px;white-space:nowrap;"></span>
                            </div>
                            <div id="foto-rec-dot" style="position:absolute;top:8px;right:8px;width:7px;height:7px;background:#ef4444;border-radius:50%;display:none;"></div>
                        </div>

                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-top:8px;">
                            <button type="button" id="foto-btn-start" onclick="startCamera()"
                                    style="font-size:11px;padding:5px 10px;border-radius:6px;border:1px solid #1d4ed8;background:#1d4ed8;color:#fff;cursor:pointer;line-height:1.4;">
                                Aktifkan Kamera
                            </button>
                            <button type="button" id="foto-btn-snap" onclick="snapPhoto()"
                                    style="display:none;font-size:11px;padding:5px 10px;border-radius:6px;border:1px solid #d1d5db;background:#f9fafb;color:#374151;cursor:pointer;line-height:1.4;">
                                📸 Ambil Foto
                            </button>
                            <button type="button" id="foto-btn-stop" onclick="stopCamera()"
                                    style="display:none;font-size:11px;padding:5px 10px;border-radius:6px;border:1px solid #fca5a5;background:#fff;color:#dc2626;cursor:pointer;line-height:1.4;">
                                Matikan Kamera
                            </button>
                            <span id="foto-cam-status" style="font-size:11px;color:#9ca3af;"></span>
                        </div>

                        <div id="foto-snap-result" style="display:none;margin-top:10px;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;gap:10px;align-items:flex-start;">
                            <img id="foto-snap-img" src="" alt="Foto bukti" style="max-height:80px;max-width:110px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;flex-shrink:0;">
                            <div>
                                <p style="font-size:12px;font-weight:500;color:#111827;margin:0;">Foto berhasil diambil</p>
                                <span id="foto-snap-ts" style="font-size:11px;color:#6b7280;"></span>
                                <div style="margin-top:6px;">
                                    <button type="button" onclick="clearSnap()"
                                            style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #fca5a5;background:#fff;color:#dc2626;cursor:pointer;">
                                        ✕ Hapus Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="foto-cam-data" name="photo_cam_data">
                    </div>

                    {{-- Panel Upload Foto --}}
                    <div id="foto-panel-upload" style="display:none;">
                        <div id="foto-drop-zone"
                             onclick="document.getElementById('photo_evidence').click()"
                             ondragover="dzOver(event,'foto-drop-zone')"
                             ondragleave="dzLeave('foto-drop-zone')"
                             ondrop="dzDrop(event,'foto')"
                             style="border:1.5px dashed #d1d5db;border-radius:8px;padding:22px 12px;text-align:center;cursor:pointer;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="opacity:.3;margin:0 auto 6px;display:block;width:24px;height:24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p style="font-size:12px;color:#6b7280;margin:0;">Klik atau seret foto ke sini</p>
                            <span style="font-size:11px;color:#9ca3af;">JPG, PNG — maks 2 MB</span>
                        </div>
                        <input type="file" id="photo_evidence" name="photo_evidence" accept="image/*" style="display:none;"
                               onchange="fileSelected(this,'foto',2)">
                        <div id="foto-upload-preview" style="display:none;margin-top:10px;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;gap:10px;align-items:flex-start;">
                            <img id="foto-upload-img" src="" alt="Preview" style="max-height:80px;max-width:110px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;flex-shrink:0;">
                            <div>
                                <p style="font-size:12px;font-weight:500;color:#111827;margin:0;">File dipilih</p>
                                <span id="foto-upload-name" style="font-size:11px;color:#6b7280;"></span>
                                <div style="margin-top:6px;">
                                    <button type="button" onclick="clearUpload('foto')"
                                            style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #fca5a5;background:#fff;color:#dc2626;cursor:pointer;">
                                        ✕ Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /card foto --}}

                {{-- ─── Card: Tanda Tangan ─── --}}
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;">

                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#6b7280;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:.5;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.415.586H9v-2.414a2 2 0 01.586-1.414z"/>
                        </svg>
                        Tanda Tangan
                        <span style="font-size:10px;font-weight:400;color:#9ca3af;text-transform:none;letter-spacing:0;">(opsional)</span>
                    </div>

                    <div style="display:flex;gap:6px;margin-bottom:14px;">
                        <button type="button" id="sig-tab-pad" onclick="switchSigTab('pad')"
                                style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;border:1px solid #1d4ed8;background:#1d4ed8;color:#fff;cursor:pointer;white-space:nowrap;line-height:1.4;">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.415.586H9v-2.414a2 2 0 01.586-1.414z"/>
                            </svg>
                            Gambar Langsung
                        </button>
                        <button type="button" id="sig-tab-upload" onclick="switchSigTab('upload')"
                                style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;border:1px solid #d1d5db;background:#f3f4f6;color:#374151;cursor:pointer;white-space:nowrap;line-height:1.4;">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload Foto
                        </button>
                    </div>

                    {{-- Panel Signature Pad --}}
                    <div id="sig-panel-pad" style="display:block;">
                        <canvas id="sig-canvas" aria-label="Area tanda tangan"
                                style="display:block;width:100%;height:150px;border:1px solid #d1d5db;border-radius:8px;background:#fff;cursor:crosshair;touch-action:none;"></canvas>
                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-top:6px;">
                            <button type="button" onclick="sigUndo()"
                                    style="font-size:11px;padding:5px 10px;border-radius:6px;border:1px solid #d1d5db;background:#f9fafb;color:#374151;cursor:pointer;line-height:1.4;">
                                ↩ Undo
                            </button>
                            <button type="button" onclick="sigClear()"
                                    style="font-size:11px;padding:5px 10px;border-radius:6px;border:1px solid #fca5a5;background:#fff;color:#dc2626;cursor:pointer;line-height:1.4;">
                                ✕ Hapus
                            </button>
                            <span style="font-size:11px;color:#9ca3af;">Gambar tanda tangan di area atas</span>
                        </div>
                        <input type="hidden" id="signature-data" name="signature">
                    </div>

                    {{-- Panel Upload TTD --}}
                    <div id="sig-panel-upload" style="display:none;">
                        <div id="sig-drop-zone"
                             onclick="document.getElementById('signature_upload').click()"
                             ondragover="dzOver(event,'sig-drop-zone')"
                             ondragleave="dzLeave('sig-drop-zone')"
                             ondrop="dzDrop(event,'sig')"
                             style="border:1.5px dashed #d1d5db;border-radius:8px;padding:22px 12px;text-align:center;cursor:pointer;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="opacity:.3;margin:0 auto 6px;display:block;width:24px;height:24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p style="font-size:12px;color:#6b7280;margin:0;">Klik atau seret foto tanda tangan</p>
                            <span style="font-size:11px;color:#9ca3af;">JPG, PNG — maks 1 MB</span>
                        </div>
                        <input type="file" id="signature_upload" name="signature_upload" accept="image/*" style="display:none;"
                               onchange="fileSelected(this,'sig',1)">
                        <div id="sig-upload-preview" style="display:none;margin-top:10px;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;gap:10px;align-items:flex-start;">
                            <img id="sig-upload-img" src="" alt="Preview TTD" style="max-height:80px;max-width:110px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;flex-shrink:0;">
                            <div>
                                <p style="font-size:12px;font-weight:500;color:#111827;margin:0;">File dipilih</p>
                                <span id="sig-upload-name" style="font-size:11px;color:#6b7280;"></span>
                                <div style="margin-top:6px;">
                                    <button type="button" onclick="clearUpload('sig')"
                                            style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #fca5a5;background:#fff;color:#dc2626;cursor:pointer;">
                                        ✕ Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /card tanda tangan --}}

            </div>{{-- /kolom kanan --}}

        </div>{{-- /report-grid --}}
    </form>

    <canvas id="foto-hidden-canvas" style="display:none;"></canvas>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Responsif: satu kolom di layar sempit ── */
    function applyGrid() {
        var grid = document.getElementById('report-grid');
        if (!grid) return;
        grid.style.gridTemplateColumns = window.innerWidth < 768 ? '1fr' : '1fr 1fr';
    }
    applyGrid();
    window.addEventListener('resize', applyGrid);

    /* ── Inject keyframe animasi rec dot ── */
    var ks = document.createElement('style');
    ks.textContent = '@keyframes blink-rec{0%,100%{opacity:1}50%{opacity:0}}';
    document.head.appendChild(ks);

    /* ── Tanggal & Waktu (Bahasa Indonesia) ── */
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
    function tickClock() {
        var n = new Date();
        var ed = document.getElementById('foto-cam-date');
        var et = document.getElementById('foto-cam-time');
        if (ed) ed.textContent = fmtDate(n);
        if (et) et.textContent = fmtTime(n);
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* ── Stamp timestamp ke canvas ── */
    function stampCanvas(ctx, w, h) {
        var n = new Date(), txt1 = fmtDate(n), txt2 = fmtTime(n);
        ctx.save();
        ctx.font = '13px ui-monospace,monospace';
        var pad = 8, lh = 18, bh = lh * 2 + pad;
        var bw = Math.max(ctx.measureText(txt1).width, ctx.measureText(txt2).width) + pad * 2;
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(pad, h - bh - pad, bw, bh);
        ctx.fillStyle = '#fff';
        ctx.fillText(txt1, pad * 2, h - pad - lh);
        ctx.fillText(txt2, pad * 2, h - pad);
        ctx.restore();
        return { date: txt1, time: txt2 };
    }

    /* ══════════════════════════════════════
       SIGNATURE PAD
    ══════════════════════════════════════ */
    var sigCanvas = document.getElementById('sig-canvas');
    var sigCtx    = sigCanvas.getContext('2d');
    var sigInput  = document.getElementById('signature-data');
    var drawing   = false, strokes = [], curStroke = [];

    function syncCanvas() {
        var w = sigCanvas.offsetWidth || 400, h = sigCanvas.offsetHeight || 150;
        if (sigCanvas.width !== w || sigCanvas.height !== h) {
            sigCanvas.width = w; sigCanvas.height = h; redraw();
        }
    }
    function redraw() {
        sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
        strokes.forEach(function (s) {
            if (s.length < 2) return;
            sigCtx.beginPath(); sigCtx.moveTo(s[0].x, s[0].y);
            for (var i = 1; i < s.length; i++) sigCtx.lineTo(s[i].x, s[i].y);
            sigCtx.strokeStyle = '#1e293b'; sigCtx.lineWidth = 2.2;
            sigCtx.lineCap = 'round'; sigCtx.lineJoin = 'round'; sigCtx.stroke();
        });
    }
    function saveSig() { sigInput.value = strokes.length ? sigCanvas.toDataURL('image/png') : ''; }
    function getPos(e) {
        var r = sigCanvas.getBoundingClientRect(), src = e.touches ? e.touches[0] : e;
        return { x: src.clientX - r.left, y: src.clientY - r.top };
    }
    function startDraw(e) {
        e.preventDefault(); syncCanvas(); drawing = true; curStroke = [];
        sigCanvas.style.borderColor = '#3b82f6';
        var p = getPos(e); sigCtx.beginPath(); sigCtx.moveTo(p.x, p.y); curStroke.push(p);
    }
    function moveDraw(e) {
        if (!drawing) return; e.preventDefault();
        var p = getPos(e); sigCtx.lineTo(p.x, p.y);
        sigCtx.strokeStyle = '#1e293b'; sigCtx.lineWidth = 2.2;
        sigCtx.lineCap = 'round'; sigCtx.lineJoin = 'round'; sigCtx.stroke(); curStroke.push(p);
    }
    function endDraw() {
        if (!drawing) return; drawing = false;
        sigCanvas.style.borderColor = '#d1d5db';
        if (curStroke.length) { strokes.push(curStroke.slice()); curStroke = []; saveSig(); }
    }
    sigCanvas.addEventListener('mousedown',  startDraw);
    sigCanvas.addEventListener('mousemove',  moveDraw);
    sigCanvas.addEventListener('mouseup',    endDraw);
    sigCanvas.addEventListener('mouseleave', endDraw);
    sigCanvas.addEventListener('touchstart', startDraw, { passive: false });
    sigCanvas.addEventListener('touchmove',  moveDraw,  { passive: false });
    sigCanvas.addEventListener('touchend',   endDraw);

    window.sigUndo  = function () { strokes.pop(); redraw(); saveSig(); };
    window.sigClear = function () {
        strokes = []; curStroke = [];
        sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
        sigInput.value = '';
    };
    syncCanvas();

    /* ══════════════════════════════════════
       KAMERA BUKTI FOTO
    ══════════════════════════════════════ */
    var fotoStream = null;

    window.startCamera = function () {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
            .then(function (stream) {
                fotoStream = stream;
                document.getElementById('foto-video').srcObject = stream;
                document.getElementById('foto-cam-off').style.display    = 'none';
                document.getElementById('foto-btn-start').style.display  = 'none';
                document.getElementById('foto-btn-snap').style.display   = 'inline-flex';
                document.getElementById('foto-btn-stop').style.display   = 'inline-flex';
                var dot = document.getElementById('foto-rec-dot');
                dot.style.display    = 'block';
                dot.style.animation  = 'blink-rec 1s infinite';
                document.getElementById('foto-cam-status').textContent   = 'Kamera aktif';
            })
            .catch(function () {
                document.getElementById('foto-cam-status').textContent = 'Akses kamera ditolak atau tidak tersedia.';
            });
    };

    window.stopCamera = function () {
        if (fotoStream) { fotoStream.getTracks().forEach(function (t) { t.stop(); }); fotoStream = null; }
        document.getElementById('foto-video').srcObject          = null;
        document.getElementById('foto-cam-off').style.display    = 'flex';
        document.getElementById('foto-btn-start').style.display  = 'inline-flex';
        document.getElementById('foto-btn-snap').style.display   = 'none';
        document.getElementById('foto-btn-stop').style.display   = 'none';
        document.getElementById('foto-rec-dot').style.display    = 'none';
        document.getElementById('foto-cam-status').textContent   = '';
    };

    window.snapPhoto = function () {
        var v = document.getElementById('foto-video');
        var c = document.getElementById('foto-hidden-canvas');
        c.width = v.videoWidth || 640; c.height = v.videoHeight || 480;
        var ctx = c.getContext('2d');
        ctx.drawImage(v, 0, 0, c.width, c.height);
        var stamp  = stampCanvas(ctx, c.width, c.height);
        var url    = c.toDataURL('image/jpeg', 0.92);
        document.getElementById('foto-cam-data').value       = url;
        document.getElementById('foto-snap-img').src         = url;
        document.getElementById('foto-snap-ts').textContent  = stamp.date + ' ' + stamp.time;
        document.getElementById('foto-snap-result').style.display = 'flex';
    };

    window.clearSnap = function () {
        document.getElementById('foto-cam-data').value              = '';
        document.getElementById('foto-snap-img').src                = '';
        document.getElementById('foto-snap-result').style.display   = 'none';
    };

    /* ══════════════════════════════════════
       TAB SWITCHING
    ══════════════════════════════════════ */
    var ACT = 'display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;border:1px solid #1d4ed8;background:#1d4ed8;color:#fff;cursor:pointer;white-space:nowrap;line-height:1.4;';
    var INA = 'display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;border:1px solid #d1d5db;background:#f3f4f6;color:#374151;cursor:pointer;white-space:nowrap;line-height:1.4;';

    window.switchFotoTab = function (tab) {
        var isCam = tab === 'cam';
        document.getElementById('foto-tab-cam').setAttribute('style',    isCam ? ACT : INA);
        document.getElementById('foto-tab-upload').setAttribute('style', isCam ? INA : ACT);
        document.getElementById('foto-panel-cam').style.display    = isCam ? 'block' : 'none';
        document.getElementById('foto-panel-upload').style.display = isCam ? 'none'  : 'block';
        if (!isCam) {
            document.getElementById('foto-cam-data').value = '';
            if (fotoStream) { fotoStream.getTracks().forEach(function (t) { t.stop(); }); fotoStream = null; }
        } else {
            document.getElementById('photo_evidence').value = '';
            document.getElementById('foto-upload-preview').style.display = 'none';
        }
    };

    window.switchSigTab = function (tab) {
        var isPad = tab === 'pad';
        document.getElementById('sig-tab-pad').setAttribute('style',    isPad ? ACT : INA);
        document.getElementById('sig-tab-upload').setAttribute('style', isPad ? INA : ACT);
        document.getElementById('sig-panel-pad').style.display    = isPad ? 'block' : 'none';
        document.getElementById('sig-panel-upload').style.display = isPad ? 'none'  : 'block';
        if (isPad) {
            document.getElementById('signature_upload').value = '';
            document.getElementById('sig-upload-preview').style.display = 'none';
            syncCanvas();
        } else {
            sigInput.value = '';
        }
    };

    /* ══════════════════════════════════════
       DROP ZONE & FILE UPLOAD
    ══════════════════════════════════════ */
    window.dzOver  = function (e, id) {
        e.preventDefault();
        document.getElementById(id).style.borderColor = '#3b82f6';
        document.getElementById(id).style.background  = '#eff6ff';
    };
    window.dzLeave = function (id) {
        document.getElementById(id).style.borderColor = '#d1d5db';
        document.getElementById(id).style.background  = 'transparent';
    };
    window.dzDrop  = function (e, prefix) {
        e.preventDefault();
        dzLeave(prefix === 'foto' ? 'foto-drop-zone' : 'sig-drop-zone');
        applyFile(e.dataTransfer.files[0], prefix, prefix === 'sig' ? 1 : 2);
    };
    window.fileSelected = function (input, prefix, maxMb) {
        if (input.files.length) applyFile(input.files[0], prefix, maxMb);
    };

    function applyFile(file, prefix, maxMb) {
        if (!file || !file.type.startsWith('image/')) { alert('File harus berupa gambar (JPG/PNG).'); return; }
        if (file.size > maxMb * 1024 * 1024) { alert('Ukuran file melebihi ' + maxMb + ' MB.'); return; }
        var r = new FileReader();
        r.onload = function (ev) {
            document.getElementById(prefix + '-upload-img').src          = ev.target.result;
            document.getElementById(prefix + '-upload-name').textContent = file.name;
            document.getElementById(prefix + '-upload-preview').style.display = 'flex';
        };
        r.readAsDataURL(file);
    }

    window.clearUpload = function (prefix) {
        document.getElementById(prefix === 'foto' ? 'photo_evidence' : 'signature_upload').value = '';
        document.getElementById(prefix + '-upload-img').src = '';
        document.getElementById(prefix + '-upload-preview').style.display = 'none';
    };

    /* ══════════════════════════════════════
       SUBMIT
    ══════════════════════════════════════ */
    document.getElementById('report-form').addEventListener('submit', function () {
        var padVis   = document.getElementById('sig-panel-pad').style.display    !== 'none';
        var sigUpVis = document.getElementById('sig-panel-upload').style.display !== 'none';
        var camVis   = document.getElementById('foto-panel-cam').style.display   !== 'none';

        if (!padVis)   document.getElementById('signature-data').disabled   = true;
        if (!sigUpVis) document.getElementById('signature_upload').disabled  = true;
        if (!camVis)   document.getElementById('foto-cam-data').disabled     = true;
        else           document.getElementById('photo_evidence').disabled    = true;

        if (fotoStream) fotoStream.getTracks().forEach(function (t) { t.stop(); });
    });

})();
</script>
@endpush
