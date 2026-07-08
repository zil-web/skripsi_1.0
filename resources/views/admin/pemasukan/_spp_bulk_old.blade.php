<div id="siswa-list-section" style="display:block; margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:8px;">
                    Daftar Siswa (SPP - bulk)
                </label>
                <div style="display:flex; gap:8px; align-items:center; margin-bottom:8px; flex-wrap:wrap;">
                    <button type="button" id="btnTambahSiswa" style="background:#10B981; color:white; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; font-weight:600;">+ Cari & Tambah Siswa</button>
                    <button type="button" id="btnKosongkanSiswa" style="background:#ef4444; color:white; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; font-weight:600;">Kosongkan</button>
                    <span id="totalSelectedBadge" style="margin-left:auto; font-size:13px; color:#6b7280;">Total: 0 siswa dipilih</span>
                </div>

                <div style="border:1px solid #f3f4f6; border-radius:12px; overflow-x:auto; overflow-y:hidden; -webkit-overflow-scrolling:touch;">
                    <table style="width:100%; min-width:760px; font-size:13px;" id="siswa-table">
                        <thead>
                            <tr style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
                                <th style="padding:10px 12px; text-align:center; width:48px;">No</th>
                                <th style="padding:10px 12px; text-align:left;">Nama</th>
                                <th style="padding:10px 12px; text-align:left;">NIS</th>
                                <th style="padding:10px 12px; text-align:left;">Kelas</th>
                                <th style="padding:10px 12px; text-align:right; width:160px;">Jumlah (Rp)</th>
                                <th style="padding:10px 12px; text-align:center; width:96px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="siswa-table-body">
                            <tr><td colspan="6" style="padding:16px; text-align:center; color:#9ca3af;">Belum ada siswa ditambahkan.</td></tr>
                        </tbody>
                    </table>
                </div>

                <p id="bulkSppMessage" class="hidden" style="font-size:12px; font-weight:600; color:#ef4444; margin:8px 0 0;"></p>

    </div>

            <!-- Upload Bukti -->
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Bukti Transaksi
                    <span style="color:#ef4444;">*</span>
                </label>
                @error('bukti_transaksi')
                    <p style="font-size:11px;color:#ef4444;margin:0 0 4px;">
                        {{ $message }}
                    </p>
                @enderror
                <input type="file" name="bukti_transaksi"
                    accept=".jpg,.jpeg,.png,.pdf"
                          style="width:100%; font-size:12px; 
                              border:1px solid {{ $errors->has('bukti_transaksi') ? '#ef4444' : '#e5e7eb' }}; 
                           border-radius:8px; padding:6px 12px;
                           box-sizing:border-box;">
                <p style="font-size:10px;color:#9ca3af;margin:4px 0 0;">
                    JPG, PNG, PDF maksimal 2MB
                </p>
            </div>

            <!-- Footer Tombol -->
            <div style="display:flex; gap:8px; 
                        padding-top:16px; 
                        border-top:1px solid #f3f4f6;">
                <button type="button" onclick="tutupModal()"
                    style="flex:1; font-size:13px; 
                           border:1px solid #e5e7eb;
                           background:white; color:#6b7280;
                           border-radius:8px; padding:9px;
                           cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="flex:1; font-size:13px; 
                           background:#1D9E75; color:white;
                           border:none; border-radius:8px; 
                           padding:9px; cursor:pointer;
                           font-weight:500;">
                    Simpan Transaksi
                </button>
            </div>


<div id="modalSiswa"
    style="display:none; position:fixed; inset:0; 
           z-index:10010; align-items:flex-start; 
           justify-content:center; overflow-y:auto; 
           padding:24px 16px; background:rgba(0,0,0,0.5);">
    <div onclick="tutupModalSiswa()"
        style="position:absolute; inset:0; background:rgba(0,0,0,0.5);"></div>

    <div style="position:relative; background:white; 
                border-radius:12px; width:100%; 
                max-width:640px; margin:auto; 
                max-height:calc(100vh - 48px); overflow:hidden; 
                display:flex; flex-direction:column; 
                z-index:10020; box-shadow:0 20px 60px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:16px 20px; background:#10b981; color:white; flex-shrink:0;">
            <div>
                <p style="font-size:14px; font-weight:600; margin:0;">Pilih Siswa</p>
                <p style="font-size:11px; opacity:0.9; margin:4px 0 0;">Cari siswa berdasarkan NIS, nama, atau kelas</p>
            </div>
            <button type="button" onclick="tutupModalSiswa()"
                style="background:none; border:none; color:white; cursor:pointer; font-size:20px; line-height:1;">
                &times;
            </button>
        </div>

        <div style="padding:20px; overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch;">
            <input type="text" id="searchSiswa"
                placeholder="Cari NIS, nama, atau kelas..."
                style="width:100%; font-size:13px; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px; box-sizing:border-box; outline:none; margin-bottom:16px;">

            <div style="border:1px solid #f3f4f6; border-radius:12px; overflow:hidden;">
                <table style="width:100%; font-size:13px;">
                    <thead>
                        <tr style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
                            <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">NIS</th>
                            <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Nama</th>
                            <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Kelas</th>
                            <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Jenis Kelamin</th>
                            <th style="text-align:center; padding:12px 16px; font-weight:500; color:#6b7280;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="hasilSiswa">
                        <tr>
                            <td colspan="5" style="padding:20px 16px; text-align:center; color:#9ca3af;">Ketik minimal 1 karakter untuk mencari siswa.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <div style="padding:14px 20px; border-top:0.5px solid #e5e7eb; text-align:right; flex-shrink:0; background:#ffffff;">
            <button type="button" onclick="tutupModalSiswa()" style="background:#6b7280; color:white; border:none; padding:8px 12px; border-radius:8px;">Tutup</button>
        </div>
        </div>
    </div>
</div>

<script>
    const initialPemasukanSiswa = null;

    function bukaModal() {
        var modal = document.getElementById('modalTambah');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        toggleSiswaSection();
    }

    function tutupModal() {
        var modal = document.getElementById('modalTambah');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function hitungKarakter() {
        var txt = document.getElementById('inputKeterangan');
        var counter = document.getElementById('hitungChar');
        if (txt && counter) {
            counter.textContent = txt.value.length;
        }
    }

    function formatSiswaLabel(siswa) {
        return siswa ? siswa.nama + ' (NIS: ' + siswa.nis + ')' : '';
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toggleSiswaSection() {
        var jenisSelect = document.getElementById('jenisPemasukan');
        var siswaListSection = document.getElementById('siswa-list-section');
        var inputJumlah = document.getElementById('inputJumlah');

        if (!jenisSelect) {
            return;
        }

        if (jenisSelect.value === 'SPP') {
            // show bulk SPP list only
            if (siswaListSection) siswaListSection.style.display = 'block';
            // disable global jumlah for SPP
            if (inputJumlah) {
                inputJumlah.disabled = true;
                inputJumlah.style.background = '#f3f4f6';
                inputJumlah.placeholder = 'Diisi per siswa';
            }
        } else {
            if (siswaListSection) siswaListSection.style.display = 'none';
            // reset and re-enable global jumlah
            if (inputJumlah) {
                inputJumlah.disabled = false;
                inputJumlah.style.background = '';
                inputJumlah.placeholder = '0';
            }
            // clear selectedStudents
            selectedStudents = [];
            renderSiswaTable();
        }
    }

    function bukaModalSiswa() {
        var modal = document.getElementById('modalSiswa');
        var searchInput = document.getElementById('searchSiswa');
        if (!modal) {
            return;
        }

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        if (searchInput) {
            searchInput.focus();
            if (!searchInput.value) {
                renderHasilSiswa([]);
            }
        }
    }

    function tutupModalSiswa() {
        var modal = document.getElementById('modalSiswa');
        var modalUtama = document.getElementById('modalTambah');
        if (modal) {
            modal.style.display = 'none';
        }

        document.body.style.overflow = modalUtama && modalUtama.style.display === 'flex' ? 'hidden' : '';
    }

    function renderHasilSiswa(data) {
        var tbody = document.getElementById('hasilSiswa');
        if (!tbody) {
            return;
        }

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px 16px; text-align:center; color:#9ca3af;">Tidak ada data siswa ditemukan.</td></tr>';
            return;
        }
        // When rendering search results, mark already selected students
        tbody.innerHTML = data.map(function (siswa) {
            var already = selectedStudents.find(function (s) { return String(s.id) === String(siswa.id); });
            var btnHtml = already ? '<button type="button" disabled style="background:#9ca3af; color:white; border:none; border-radius:8px; padding:7px 12px; font-size:12px;">Sudah Dipilih</button>' : '<button type="button" class="btn-pilih-siswa" data-id="' + escapeHtml(siswa.id) + '" data-nis="' + escapeHtml(siswa.nis || '') + '" data-nama="' + escapeHtml(siswa.nama || '') + '" data-kelas="' + escapeHtml(siswa.kelas || '') + '" style="background:#1D9E75; color:white; border:none; border-radius:8px; padding:7px 12px; font-size:12px; cursor:pointer;">Pilih</button>';
            return '<tr style="border-bottom:1px solid #f3f4f6;">' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.nis || '-') + '</td>' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.nama || '-') + '</td>' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.kelas || '-') + '</td>' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.jenis_kelamin || '-') + '</td>' +
                '<td style="padding:12px 16px; text-align:center;">' + btnHtml + '</td>' +
            '</tr>';
        }).join('');
    }

    // -- Bulk SPP client state and helpers --
    let selectedStudents = [];

    function renderSiswaTable() {
        var tbody = document.getElementById('siswa-table-body');
        var badge = document.getElementById('totalSelectedBadge');
        if (!tbody) return;

        if (selectedStudents.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="padding:16px; text-align:center; color:#9ca3af;">Belum ada siswa ditambahkan.</td></tr>';
            badge.textContent = 'Total: 0 siswa dipilih';
            return;
        }

        badge.textContent = 'Total: ' + selectedStudents.length + ' siswa dipilih';

        tbody.innerHTML = selectedStudents.map(function (s, idx) {
            return '<tr style="border-bottom:1px solid #f3f4f6;">' +
                '<td style="padding:12px 16px; text-align:center;">' + (idx+1) + '</td>' +
                '<td style="padding:12px 16px;">' + escapeHtml(s.nama) + '</td>' +
                '<td style="padding:12px 16px;">' + escapeHtml(s.nis || '-') + '</td>' +
                '<td style="padding:12px 16px;">' + escapeHtml(s.kelas || '-') + '</td>' +
                '<td style="padding:12px 16px; text-align:right;">' +
                    '<div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">' +
                        '<input type="number" min="1" value="' + (s.jumlah || '') + '" data-idx="' + idx + '" class="siswa-jumlah-input" style="width:120px; padding:6px 8px; border:1px solid #e5e7eb; border-radius:8px; text-align:right;" />' +
                        '<button type="button" class="btn-kosongkan-siswa" data-idx="' + idx + '" onclick="kosongkanSiswa(this)" style="font-size:11px; padding:4px 8px; background:#e5e7eb; color:#374151; border:none; border-radius:6px; cursor:pointer; white-space:nowrap;">Kosongkan</button>' +
                    '</div>' +
                '</td>' +
                '<td style="padding:12px 16px; text-align:center;"><button type="button" class="btn-hapus-siswa" data-idx="' + idx + '" style="background:#ef4444; color:white; border:none; padding:6px 10px; border-radius:8px;">Hapus</button></td>' +
            '</tr>';
        }).join('');
    }

    function kosongkanSiswa(button) {
        if (!button) {
            return;
        }

        var row = button.closest('tr');
        if (!row) {
            return;
        }

        var idx = button.dataset.idx;
        var jumlahInput = row.querySelector('.siswa-jumlah-input');
        if (jumlahInput) {
            jumlahInput.value = '';
        }

        var checkbox = row.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = false;
        }

        if (typeof idx !== 'undefined' && selectedStudents[Number(idx)]) {
            selectedStudents[Number(idx)].jumlah = '';
        }
    }

    function addSelectedStudent(siswa) {
        if (selectedStudents.find(s => String(s.id) === String(siswa.id))) return;
        selectedStudents.push({ id: siswa.id, nama: siswa.nama, nis: siswa.nis, kelas: siswa.kelas, jumlah: 0 });
        renderSiswaTable();
        // re-render search results to update buttons
        var searchInput = document.getElementById('searchSiswa');
        if (searchInput && searchInput.value.trim().length > 0) {
            cariSiswa();
        }
    }

    function removeSelectedStudent(index) {
        selectedStudents.splice(index, 1);
        renderSiswaTable();
        var searchInput = document.getElementById('searchSiswa');
        if (searchInput && searchInput.value.trim().length > 0) {
            cariSiswa();
        }
    }

    function showBulkMessage(message, type) {
        var el = document.getElementById('bulkSppMessage');
        if (!el) {
            return;
        }

        el.textContent = message || '';
        el.style.color = type === 'success' ? '#059669' : '#ef4444';
        el.classList.toggle('hidden', !message);
    }

    function clearBulkMessage() {
        showBulkMessage('', 'error');
    }

    // Preview modal
    function bukaPreviewModal() {
        // validate
        if (selectedStudents.length === 0) {
            showBulkMessage('Pilih minimal 1 siswa.');
            return;
        }
        var invalid = selectedStudents.find(s => !s.jumlah || Number(s.jumlah) <= 0);
        if (invalid) {
            showBulkMessage('Pastikan semua siswa memiliki jumlah > 0.');
            return;
        }

        clearBulkMessage();

        // build preview HTML
        var modalId = 'modalPreviewSPP';
        var existing = document.getElementById(modalId);
        if (existing) existing.remove();

        var tanggal = document.querySelector('input[name="tanggal"]').value;
        var jenis = document.getElementById('jenisPemasukan').value;
        var keterangan = document.getElementById('inputKeterangan').value;

        var rows = selectedStudents.map(function(s, idx){
            return '<tr style="border-bottom:1px solid #f3f4f6;">' +
                '<td style="padding:8px 12px;">'+(idx+1)+'</td>' +
                '<td style="padding:8px 12px;">'+escapeHtml(s.nama)+'</td>' +
                '<td style="padding:8px 12px;">'+escapeHtml(s.nis || '-')+'</td>' +
                '<td style="padding:8px 12px;">'+escapeHtml(s.kelas || '-')+'</td>' +
                '<td style="padding:8px 12px; text-align:right;">'+formatRupiah(s.jumlah || 0)+'</td>' +
            '</tr>';
        }).join('');

        var total = selectedStudents.reduce(function(acc, s){ return acc + Number(s.jumlah || 0); }, 0);

        var modalHtml = '\n<div id="'+modalId+'" style="display:flex; position:fixed; inset:0; z-index:11000; align-items:flex-start; justify-content:center; overflow-y:auto; padding:24px 16px; background:rgba(0,0,0,0.5);">\n' +
            '<div onclick="document.getElementById(\''+modalId+'\').remove(); document.body.style.overflow = \''+'\';" style="position:absolute; inset:0; background:rgba(0,0,0,0.5);"></div>\n' +
            '<div style="position:relative; background:white; border-radius:12px; width:100%; max-width:680px; margin:auto; max-height:calc(100vh - 48px); overflow:hidden; display:flex; flex-direction:column; z-index:11010; box-shadow:0 20px 60px rgba(0,0,0,0.18);">\n' +
            '<div style="padding:16px 20px; border-bottom:1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; background:#10b981; color:#fff; flex-shrink:0;">\n' +
            '<div><strong>Konfirmasi Pemasukan SPP</strong><div style="font-size:12px;color:#6b7280;margin-top:6px;">Tanggal: '+escapeHtml(tanggal)+' &nbsp; • &nbsp; Jenis: '+escapeHtml(jenis)+'</div></div>' +
            '<button onclick="document.getElementById(\''+modalId+'\').remove(); document.body.style.overflow = \''+'\';" style="background:none;border:none;color:#fff;font-size:20px;line-height:1;">&times;</button></div>' +
            '<div style="padding:20px; overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch;">' +
            '<p id="previewBulkMessage" class="hidden text-sm font-medium" style="margin:0 0 12px;"></p>' +
            '<div style="margin-bottom:12px; color:#374151;">Keterangan: '+escapeHtml(keterangan || '-')+'</div>' +
            '<div style="border:1px solid #f3f4f6; border-radius:8px; overflow:hidden;"><table style="width:100%;">' +
            '<thead><tr style="background:#f9fafb;"><th style="padding:8px 12px;">No</th><th style="padding:8px 12px;">Nama</th><th style="padding:8px 12px;">NIS</th><th style="padding:8px 12px;">Kelas</th><th style="padding:8px 12px; text-align:right;">Jumlah</th></tr></thead>' +
            '<tbody>'+rows+'</tbody>' +
            '<tfoot><tr><td colspan="4" style="padding:8px 12px; text-align:right;"><strong>Total Keseluruhan:</strong></td><td style="padding:8px 12px; text-align:right;"><strong>'+formatRupiah(total)+'</strong></td></tr></tfoot>' +
            '</table></div>' +
            '<div style="display:flex; gap:8px; margin-top:16px;"><button onclick="document.getElementById(\''+modalId+'\').remove(); document.body.style.overflow = \''+'\';" style="flex:1; border:0.5px solid #d1d5db; background:white; padding:8px 20px; border-radius:8px;">Kembali Edit</button>' +
            '<button onclick="submitBulkSPP()" style="flex:1; background:#10b981; color:white; border:none; padding:8px 20px; border-radius:8px;">Simpan Semua Transaksi</button></div>' +
            '</div></div></div>\n';

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        document.body.style.overflow = 'hidden';
    }

    // Helper to format to Rupiah (simple)
    function formatRupiah(num) {
        return (Number(num) || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
    }

    async function submitBulkSPP() {
        var url = '{{ route("admin.pemasukan.store") }}';
        var token = document.querySelector('input[name="_token"]').value;
        var tanggal = document.querySelector('input[name="tanggal"]').value;
        var jenis = document.getElementById('jenisPemasukan').value;
        var keterangan = document.getElementById('inputKeterangan').value;
        var fileInput = document.querySelector('input[name="bukti_transaksi"]');
        clearBulkMessage();

        // prepare siswa_list
        var siswa_list = selectedStudents.map(s => ({ siswa_id: s.id, jumlah: Number(s.jumlah) }));

        // if there's a file, use FormData
        var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
        try {
            var resp;
            if (hasFile) {
                var fd = new FormData();
                fd.append('_token', token);
                fd.append('tanggal', tanggal);
                fd.append('jenis_pemasukan', jenis);
                fd.append('keterangan', keterangan);
                fd.append('siswa_list', JSON.stringify(siswa_list));
                fd.append('bukti_transaksi', fileInput.files[0]);

                resp = await fetch(url, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
            } else {
                var payload = { _token: token, tanggal: tanggal, jenis_pemasukan: jenis, keterangan: keterangan, siswa_list: siswa_list };
                resp = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify(payload) });
            }

            var data = await resp.json();
            if (data.success) {
                showBulkMessage('Berhasil menyimpan ' + data.count + ' transaksi SPP.', 'success');
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            } else {
                showBulkMessage(data.message || 'Terjadi kesalahan.');
            }
        } catch (e) {
            console.error(e);
            showBulkMessage('Terjadi kesalahan saat menyimpan.');
        }
    }

    function pilihSiswa(siswa) {
        var siswaDisplay = document.getElementById('siswa_display');
        var siswaId = document.getElementById('siswa_id');

        if (siswaDisplay) {
            siswaDisplay.value = formatSiswaLabel(siswa);
        }
        if (siswaId) {
            siswaId.value = siswa.id;
        }

        tutupModalSiswa();
    }

    let debounceSiswaSearch = null;

    async function cariSiswa() {
        var searchInput = document.getElementById('searchSiswa');
        var tbody = document.getElementById('hasilSiswa');
        if (!searchInput || !tbody) {
            return;
        }

        var q = searchInput.value.trim();
        if (q.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px 16px; text-align:center; color:#9ca3af;">Ketik minimal 1 karakter untuk mencari siswa.</td></tr>';
            return;
        }

        try {
            var resp = await fetch('{{ route("siswa.search") }}?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
            var result = await resp.json();
            renderHasilSiswa(Array.isArray(result) ? result : (result.data || []));
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px 16px; text-align:center; color:#ef4444;">Gagal memuat data siswa.</td></tr>';
        }
    }

    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'btnTambahSiswa') {
            clearBulkMessage();
            bukaModalSiswa();
        }

        if (event.target && event.target.classList.contains('btn-pilih-siswa') && event.target.closest('#modalSiswa')) {
            var btn = event.target;
            addSelectedStudent({
                id: btn.dataset.id,
                nis: btn.dataset.nis,
                nama: btn.dataset.nama,
                kelas: btn.dataset.kelas,
            });
            btn.textContent = 'Sudah Dipilih';
            btn.disabled = true;
        }

        if (event.target && event.target.classList.contains('btn-hapus-siswa')) {
            var idx = Number(event.target.dataset.idx);
            if (!Number.isNaN(idx)) {
                clearBulkMessage();
                removeSelectedStudent(idx);
            }
        }

        if (event.target && event.target.classList.contains('btn-kosongkan-siswa')) {
            clearBulkMessage();
            kosongkanSiswa(event.target);
        }
    });

    document.addEventListener('input', function(event) {
        if (event.target && event.target.id === 'searchSiswa') {
            clearBulkMessage();
            clearTimeout(debounceSiswaSearch);
            debounceSiswaSearch = setTimeout(cariSiswa, 250);
        }

        // update jumlah per siswa in selectedStudents
        if (event.target && event.target.classList && event.target.classList.contains('siswa-jumlah-input')) {
            clearBulkMessage();
            var idx = event.target.dataset.idx;
            if (typeof idx !== 'undefined' && selectedStudents[Number(idx)]) {
                selectedStudents[Number(idx)].jumlah = event.target.value ? Number(event.target.value) : 0;
            }
        }
    });

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var modalSiswa = document.getElementById('modalSiswa');
            if (modalSiswa && modalSiswa.style.display === 'flex') {
                tutupModalSiswa();
                return;
            }

            tutupModal();
        }
    });

    // Buka otomatis jika ada error validasi
    @if($errors->any())
        window.addEventListener('load', function() { bukaModal(); });
    @endif

    // Init character counter
    window.addEventListener('load', function() {
        hitungKarakter();
        toggleSiswaSection();

        if (initialPemasukanSiswa && document.getElementById('jenisPemasukan') && document.getElementById('jenisPemasukan').value === 'SPP') {
            // add initial siswa into bulk list if present
            addSelectedStudent(initialPemasukanSiswa);
            var siswaListSection = document.getElementById('siswa-list-section');
            if (siswaListSection) {
                siswaListSection.style.display = 'block';
            }
        }
    });
 </script>
