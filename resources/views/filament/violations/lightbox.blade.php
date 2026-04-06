<div
    id="fi-lightbox-overlay"
    style="
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(0,0,0,0.92);
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        cursor: zoom-out;
    "
    onclick="window.__closeLightbox()"
>
    <div
        style="position: relative; max-width: 92vw; max-height: 92vh;"
        onclick="event.stopPropagation()"
    >
        <img
            id="fi-lightbox-img"
            src=""
            alt="Preview"
            style="
                max-width: 90vw;
                max-height: 88vh;
                object-fit: contain;
                border-radius: 12px;
                box-shadow: 0 30px 90px rgba(0,0,0,0.7);
                display: block;
                transition: opacity 0.2s;
            "
        />
        <button
            onclick="window.__closeLightbox()"
            style="
                position: absolute;
                top: -16px;
                right: -16px;
                width: 38px;
                height: 38px;
                border-radius: 50%;
                background: white;
                border: none;
                font-size: 22px;
                font-weight: bold;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 16px rgba(0,0,0,0.35);
                color: #111;
                line-height: 1;
                padding: 0;
            "
            title="Tutup (Esc)"
        >&times;</button>
    </div>
</div>

<script>
    window.__openLightbox = function(src) {
        var overlay = document.getElementById('fi-lightbox-overlay');
        var img     = document.getElementById('fi-lightbox-img');
        if (!overlay || !img) return;
        img.src = src;
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.__closeLightbox = function() {
        var overlay = document.getElementById('fi-lightbox-overlay');
        if (!overlay) return;
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.__closeLightbox();
    });
</script>
