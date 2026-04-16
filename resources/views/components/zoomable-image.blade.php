{{-- resources/views/components/zoomable-image.blade.php --}}
@props(['src', 'label' => 'Gambar'])

@php
    $id = 'zlb-' . md5($src . $label);
@endphp

<div style="position: relative;">

    {{-- ── Thumbnail ── --}}
    <div
        onclick="document.getElementById('{{ $id }}').style.display='flex'; zlbInit('{{ $id }}')"
        style="
            cursor: zoom-in;
            display: inline-block;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            max-width: 100%;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s;
        "
        onmouseover="this.style.borderColor='#6366f1'; this.style.boxShadow='0 4px 16px rgba(99,102,241,0.15)'"
        onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
        title="Klik untuk perbesar"
    >
        <img
            src="{{ $src }}"
            alt="{{ $label }}"
            style="max-width: 480px; width: 100%; height: auto; display: block;"
        />
        <div style="background: rgba(0,0,0,0.04); text-align: center; font-size: 12px; color: #6b7280; padding: 5px 8px; font-family: sans-serif;">
            🔍 Klik untuk perbesar
        </div>
    </div>

    {{-- ── Lightbox ── --}}
    {{-- NOTE: position:fixed replaced with a portal-style fixed via JS body append --}}
</div>

{{-- Lightbox mounted to body to escape any stacking context --}}
<div
    id="{{ $id }}"
    style="
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        width: 100vw; height: 100vh;
        z-index: 2147483647;
        background: rgba(0,0,0,0.92);
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: sans-serif;
    "
    onclick="if(event.target===this)zlbClose('{{ $id }}')"
>
    {{-- Top bar --}}
    <div style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:12px 20px;box-sizing:border-box;flex-shrink:0;">
        <span style="color:rgba(255,255,255,0.7);font-size:14px;">{{ $label }}</span>
        <button
            onclick="zlbClose('{{ $id }}')"
            style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);color:white;font-size:20px;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;line-height:1;"
        >&times;</button>
    </div>

    {{-- Viewport --}}
    <div
        id="{{ $id }}-vp"
        style="flex:1;width:100%;overflow:hidden;display:flex;align-items:center;justify-content:center;cursor:grab;"
    >
        <img
            id="{{ $id }}-img"
            src="{{ $src }}"
            alt="{{ $label }}"
            draggable="false"
            style="
                max-width: 88vw;
                max-height: calc(100vh - 140px);
                width: auto; height: auto;
                border-radius: 6px;
                box-shadow: 0 16px 64px rgba(0,0,0,0.5);
                display: block;
                background: white;
                transform-origin: center center;
                transform: scale(1) translate(0px,0px);
                transition: transform 0.18s cubic-bezier(0.25,0.46,0.45,0.94);
                user-select: none;
                -webkit-user-select: none;
                pointer-events: none;
            "
        />
    </div>

    {{-- Controls --}}
    <div style="flex-shrink:0;display:flex;gap:8px;align-items:center;padding:14px 20px;">
        <button onclick="zlbZoom('{{ $id }}',-0.3)"
            style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);color:white;font-size:22px;width:42px;height:42px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;"
        >−</button>

        <span id="{{ $id }}-pct" style="color:white;font-size:13px;min-width:48px;text-align:center;opacity:0.85;">100%</span>

        <button onclick="zlbZoom('{{ $id }}',0.3)"
            style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);color:white;font-size:22px;width:42px;height:42px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;"
        >+</button>

        <div style="width:1px;height:28px;background:rgba(255,255,255,0.2);margin:0 4px;"></div>

        <button onclick="zlbReset('{{ $id }}')"
            style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);color:white;font-size:12px;padding:0 14px;height:42px;border-radius:10px;cursor:pointer;"
        >Reset</button>

        <div style="width:1px;height:28px;background:rgba(255,255,255,0.2);margin:0 4px;"></div>

        <a href="{{ $src }}" download
            style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.25);color:white;font-size:18px;width:42px;height:42px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;text-decoration:none;"
            title="Unduh gambar"
        >↓</a>
    </div>

    <p style="color:rgba(255,255,255,0.3);font-size:11px;margin:0 0 10px;">
        Scroll untuk zoom · Drag untuk geser · Esc untuk tutup
    </p>
</div>

{{-- One-time global script --}}
@once
<script>
window._zlbState = window._zlbState || {};

window.zlbInit = function(id) {
    var s = window._zlbState;
    s[id] = { sc: 1, tx: 0, ty: 0, drag: false, sx: 0, sy: 0, stx: 0, sty: 0, tdist: null };

    var lb  = document.getElementById(id);
    var vp  = document.getElementById(id + '-vp');
    var img = document.getElementById(id + '-img');

    // Move lightbox to body to escape any stacking context / transform parent
    if (lb && lb.parentNode !== document.body) {
        document.body.appendChild(lb);
    }

    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    zlbApply(id, false);

    // Store handlers so we can remove them
    s[id]._wheel = function(e) { e.preventDefault(); zlbWheel(id, e); };
    s[id]._md    = function(e) { zlbMD(id, e); };
    s[id]._mm    = function(e) { zlbMM(id, e); };
    s[id]._mu    = function()  { zlbMU(id); };
    s[id]._kd    = function(e) { zlbKD(id, e); };

    vp.addEventListener('wheel',      s[id]._wheel, { passive: false });
    vp.addEventListener('mousedown',  s[id]._md);
    vp.addEventListener('touchstart', function(e){ zlbTS(id,e); }, { passive: false });
    vp.addEventListener('touchmove',  function(e){ zlbTM(id,e); }, { passive: false });
    vp.addEventListener('touchend',   function()  { zlbTE(id);  });
    window.addEventListener('mousemove', s[id]._mm);
    window.addEventListener('mouseup',   s[id]._mu);
    document.addEventListener('keydown', s[id]._kd);
};

window.zlbClose = function(id) {
    var s = window._zlbState[id];
    var lb = document.getElementById(id);
    var vp = document.getElementById(id + '-vp');
    if (lb) lb.style.display = 'none';
    document.body.style.overflow = '';
    if (s && vp) {
        vp.removeEventListener('wheel',     s._wheel);
        vp.removeEventListener('mousedown', s._md);
        window.removeEventListener('mousemove', s._mm);
        window.removeEventListener('mouseup',   s._mu);
        document.removeEventListener('keydown', s._kd);
    }
};

window.zlbApply = function(id, anim) {
    var s   = window._zlbState[id];
    var img = document.getElementById(id + '-img');
    var pct = document.getElementById(id + '-pct');
    if (!img || !s) return;
    if (!anim) img.style.transition = 'none';
    img.style.transform = 'scale(' + s.sc + ') translate(' + (s.tx/s.sc) + 'px,' + (s.ty/s.sc) + 'px)';
    if (!anim) { void img.offsetHeight; img.style.transition = ''; }
    if (pct) pct.textContent = Math.round(s.sc * 100) + '%';
};

window.zlbZoom = function(id, delta) {
    var s = window._zlbState[id];
    s.sc = Math.min(8, Math.max(0.3, s.sc + delta));
    zlbApply(id, true);
};

window.zlbReset = function(id) {
    var s = window._zlbState[id];
    s.sc = 1; s.tx = 0; s.ty = 0;
    zlbApply(id, true);
};

window.zlbWheel = function(id, e) {
    var s   = window._zlbState[id];
    var img = document.getElementById(id + '-img');
    var r   = img.getBoundingClientRect();
    var cx  = e.clientX - (r.left + r.width  / 2);
    var cy  = e.clientY - (r.top  + r.height / 2);
    var old = s.sc;
    s.sc = Math.min(8, Math.max(0.3, s.sc + (e.deltaY > 0 ? -0.15 : 0.15)));
    var ratio = s.sc / old;
    s.tx = cx * (1 - ratio) + s.tx * ratio;
    s.ty = cy * (1 - ratio) + s.ty * ratio;
    zlbApply(id, false);
};

window.zlbMD = function(id, e) {
    if (e.button !== 0) return;
    var s = window._zlbState[id];
    s.drag = true; s.sx = e.clientX; s.sy = e.clientY; s.stx = s.tx; s.sty = s.ty;
    document.getElementById(id + '-vp').style.cursor = 'grabbing';
    document.getElementById(id + '-img').style.transition = 'none';
    e.preventDefault();
};

window.zlbMM = function(id, e) {
    var s = window._zlbState[id];
    if (!s || !s.drag) return;
    s.tx = s.stx + (e.clientX - s.sx);
    s.ty = s.sty + (e.clientY - s.sy);
    zlbApply(id, false);
};

window.zlbMU = function(id) {
    var s = window._zlbState[id];
    if (!s) return;
    s.drag = false;
    var vp = document.getElementById(id + '-vp');
    var img = document.getElementById(id + '-img');
    if (vp) vp.style.cursor = 'grab';
    if (img) img.style.transition = '';
};

window.zlbTS = function(id, e) {
    var s = window._zlbState[id];
    if (e.touches.length === 1) {
        s.drag = true;
        s.sx = e.touches[0].clientX; s.sy = e.touches[0].clientY;
        s.stx = s.tx; s.sty = s.ty;
    } else if (e.touches.length === 2) {
        s.drag = false;
        s.tdist = Math.hypot(
            e.touches[0].clientX - e.touches[1].clientX,
            e.touches[0].clientY - e.touches[1].clientY
        );
    }
    e.preventDefault();
};

window.zlbTM = function(id, e) {
    var s = window._zlbState[id];
    if (e.touches.length === 1 && s.drag) {
        s.tx = s.stx + (e.touches[0].clientX - s.sx);
        s.ty = s.sty + (e.touches[0].clientY - s.sy);
        zlbApply(id, false);
    } else if (e.touches.length === 2 && s.tdist) {
        var d = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
        s.sc = Math.min(8, Math.max(0.3, s.sc * (d / s.tdist)));
        s.tdist = d;
        zlbApply(id, false);
    }
    e.preventDefault();
};

window.zlbTE = function(id) {
    var s = window._zlbState[id];
    if (s) { s.drag = false; s.tdist = null; }
};

window.zlbKD = function(id, e) {
    var lb = document.getElementById(id);
    if (!lb || lb.style.display !== 'flex') return;
    if (e.key === 'Escape')        zlbClose(id);
    if (e.key === '+' || e.key === '=') zlbZoom(id, 0.3);
    if (e.key === '-')             zlbZoom(id, -0.3);
    if (e.key === '0')             zlbReset(id);
};
</script>
@endonce
