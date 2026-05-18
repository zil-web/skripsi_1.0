<!-- DETAIL TRANSAKSI MODAL -->
<div id="detailTransaksiModal"
    style="display:none; pointer-events:none; position:fixed; inset:0; z-index:9999; 
           align-items:flex-start; justify-content:center; overflow-y:auto; 
           padding:24px 16px; background:rgba(0,0,0,0.5);">
    
    <!-- Backdrop -->
    <div 
        onclick="tutupDetailTransaksiModal()"
        style="position:absolute; inset:0; background:rgba(0,0,0,0.5);"></div>

    <!-- Box Modal -->
    <div style="position:relative; background:white; border-radius:12px; 
                width:100%; max-width:560px; margin:auto; 
                max-height:calc(100vh - 48px); display:flex; flex-direction:column;
                overflow:hidden; z-index:10000; box-shadow:0 20px 60px rgba(0,0,0,0.18);">

        <!-- Header Modal -->
        <div style="padding:16px 20px; border-bottom:1px solid #f3f4f6; 
                    display:flex; align-items:center; justify-content:space-between;
                    background:#10b981; color:#ffffff; flex-shrink:0;">
            <div>
            <h2 style="font-size:18px; font-weight:700; color:#ffffff; margin:0;">
                    Detail Transaksi
                </h2>
                <p style="font-size:12px; color:#d1fae5; margin:6px 0 0;">
                    Informasi lengkap transaksi beserta bukti
                </p>
            </div>
            <button type="button" onclick="tutupDetailTransaksiModal()"
                style="background:none; border:none; font-size:20px; 
                       color:#ffffff; cursor:pointer; width:32px; height:32px;
                       display:flex; align-items:center; justify-content:center;">
                ×
            </button>
        </div>

        <!-- Body Modal -->
        <div style="padding:20px; overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch;">
            <div id="detailTransaksiContent" style="display:flex; flex-direction:column; gap:14px;"></div>
        </div>

        <!-- Footer Modal -->
        <div style="padding:14px 20px; border-top:0.5px solid #e5e7eb; 
                    display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" onclick="tutupDetailTransaksiModal()"
                style="font-size:13px; background:#f3f4f6; color:#374151;
                       border:none; border-radius:8px; padding:10px 16px; 
                       cursor:pointer; font-weight:500; transition:all 0.2s;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function getFileIcon(filename) {
        if (!filename) return '📄';
        const ext = filename.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return '🖼️';
        if (ext === 'pdf') return '📕';
        return '📄';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderTransaksiField(label, value, isPeso = false) {
        return `
            <div style="padding:14px; background:#f9fafb; 
                        border:1px solid #f3f4f6; border-radius:10px;">
                <p style="font-size:10px; color:#9ca3af; text-transform:uppercase; 
                          letter-spacing:0.05em; margin:0 0 6px; font-weight:600;">
                    ${escapeHtml(label)}
                </p>
                <p style="font-size:14px; color:#1f2937; margin:0; font-weight:${isPeso ? '600' : '500'};">
                    ${escapeHtml(value || '-')}
                </p>
            </div>
        `;
    }

    function renderTransaksiBukti(buktiPath) {
        if (!buktiPath) return '';
        const rawPath = String(buktiPath);
        const fileUrl = rawPath.startsWith('http://') || rawPath.startsWith('https://') || rawPath.startsWith('/')
            ? rawPath
            : '/storage/' + rawPath;
        const filename = rawPath.split('/').pop();
        const icon = getFileIcon(filename);
        const ext = filename.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
        
        let preview = '';
        if (isImage) {
            preview = `<div style="margin-bottom:12px; border-radius:10px; overflow:hidden; background:#f3f4f6; border:1px solid #e5e7eb;">
                <img src="${fileUrl}" alt="Bukti Transaksi" style="width:100%; height:auto; max-height:280px; object-fit:contain; display:block;" />
            </div>`;
        }
        
        return `
            <div style="margin-top:20px; padding:16px; background:#f0f9ff; 
                        border:1px solid #e0f2fe; border-radius:10px;">
                <p style="font-size:10px; color:#0369a1; text-transform:uppercase; 
                          letter-spacing:0.05em; margin:0 0 12px; font-weight:600;">
                    📎 Bukti Transaksi
                </p>
                ${preview}
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                          <a href="${fileUrl}" target="_blank" 
                       style="display:inline-flex; align-items:center; gap:6px;
                              background:#3b82f6; color:white; padding:9px 14px;
                              border-radius:8px; font-size:12px; font-weight:500;
                              text-decoration:none; transition:all 0.2s;">
                        <span>${icon}</span>
                        <span>Lihat File</span>
                    </a>
                          <a href="${fileUrl}" download 
                       style="display:inline-flex; align-items:center; gap:6px;
                              background:#e5e7eb; color:#374151; padding:9px 14px;
                              border-radius:8px; font-size:12px; font-weight:500;
                              text-decoration:none; transition:all 0.2s;">
                        <span>⬇️</span>
                        <span>Download</span>
                    </a>
                </div>
                <p style="font-size:11px; color:#0369a1; margin:10px 0 0; 
                          word-break:break-all;">
                    📄 ${escapeHtml(filename)}
                </p>
            </div>
        `;
    }


    function openDetailTransaksi(fields, buktiPath = '') {
        let content = '';
        fields.forEach(field => {
            content += renderTransaksiField(field.label, field.value, field.isPeso || false);
        });
        content += renderTransaksiBukti(buktiPath);

        const modal = document.getElementById('detailTransaksiModal');
        document.getElementById('detailTransaksiContent').innerHTML = content;
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        // lock background scroll
        try { document.body.style.overflow = 'hidden'; } catch(e){}
    }

    function tutupDetailTransaksiModal() {
        const modal = document.getElementById('detailTransaksiModal');
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        try { document.body.style.overflow = ''; } catch(e){}
    }

    // Close modal when pressing Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupDetailTransaksiModal();
        }
    });

    const detailTransaksiBackdrop = document.getElementById('detailTransaksiModal');
    if (detailTransaksiBackdrop) {
        detailTransaksiBackdrop.addEventListener('click', function(event) {
            if (event.target === this) {
                tutupDetailTransaksiModal();
            }
        });
    }

    // Defensive: clear any stale body overflow on load (prevents frozen page if earlier code left it hidden)
    document.addEventListener('DOMContentLoaded', function(){
        try {
            if (getComputedStyle(document.body).overflow === 'hidden' && document.querySelectorAll('[style*="display:none"], [hidden]').length) {
                document.body.style.overflow = '';
            }
        } catch(e){}
    });
</script>
