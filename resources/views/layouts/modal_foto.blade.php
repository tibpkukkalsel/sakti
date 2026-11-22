<!-- Modal Preview Foto (Standar Modal) -->
<style>
    .modal-foto-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 999999 !important;
        background: rgba(0, 0, 0, 0.6) !important;
        display: none;
        align-items: center !important;
        justify-content: center !important;
        padding: 16px !important;
        box-sizing: border-box !important;
    }
    .modal-foto-overlay.active {
        display: flex !important;
    }
    .modal-foto-card {
        position: relative !important;
        width: 100% !important;
        max-width: 450px !important;
        background: #ffffff !important;
        border-radius: 8px !important;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2) !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }
    .modal-foto-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 12px 16px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #e9ecef !important;
    }
    .modal-foto-title {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #212529 !important;
        margin: 0 !important;
    }
    .modal-foto-close {
        border: none !important;
        background: transparent !important;
        color: #6c757d !important;
        font-size: 24px !important;
        line-height: 1 !important;
        padding: 0 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 28px !important;
        height: 28px !important;
    }
    .modal-foto-close:hover {
        color: #000000 !important;
    }
    .modal-foto-body {
        background: #f8f9fa !important;
        padding: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 200px !important;
        max-height: 75vh !important;
        overflow: auto !important;
        position: relative !important;
    }
    .modal-foto-body img {
        max-height: 70vh !important;
        max-width: 100% !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        border-radius: 4px !important;
        display: block !important;
    }
    .modal-foto-loading {
        display: none;
        position: absolute;
        color: #6c757d;
        font-size: 13px;
    }
</style>

<div id="modalFotoPreviewGlobal" class="modal-foto-overlay" onclick="handleLightboxBackdropClick(event)">
    <div class="modal-foto-card" onclick="event.stopPropagation()">
        <div class="modal-foto-header">
            <h6 class="modal-foto-title" id="modalFotoPreviewTitle">Lihat Foto</h6>
            <button type="button" class="modal-foto-close" onclick="closeFotoNoticeModal()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-foto-body">
            <div id="lightboxLoadingSpinner" class="modal-foto-loading">
                Memuat foto...
            </div>
            <img src="" id="modalFotoPreviewImg" alt="Foto" onload="document.getElementById('lightboxLoadingSpinner').style.display='none'" onerror="this.style.display='none'; document.getElementById('lightboxLoadingSpinner').style.display='block'; document.getElementById('lightboxLoadingSpinner').innerText='Foto tidak dapat dimuat';">
        </div>
    </div>
</div>

<script>
    function previewFotoNotice(url, title) {
        if (!url) return;
        var overlay = document.getElementById('modalFotoPreviewGlobal');
        var img = document.getElementById('modalFotoPreviewImg');
        var titleEl = document.getElementById('modalFotoPreviewTitle');
        var spinner = document.getElementById('lightboxLoadingSpinner');

        if (spinner) {
            spinner.style.display = 'block';
            spinner.innerText = 'Memuat foto...';
        }
        if (img) {
            img.style.display = 'block';
            var finalUrl = url;
            if (finalUrl.indexOf('?') === -1) {
                finalUrl += '?t=' + new Date().getTime();
            } else if (finalUrl.indexOf('&t=') === -1 && finalUrl.indexOf('?t=') === -1) {
                finalUrl += '&t=' + new Date().getTime();
            }
            img.src = finalUrl;
        }
        if (titleEl && title) {
            titleEl.innerText = title;
        }

        if (overlay) {
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeFotoNoticeModal() {
        var overlay = document.getElementById('modalFotoPreviewGlobal');
        if (overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function handleLightboxBackdropClick(e) {
        if (e.target && e.target.id === 'modalFotoPreviewGlobal') {
            closeFotoNoticeModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeFotoNoticeModal();
        }
    });
</script>
