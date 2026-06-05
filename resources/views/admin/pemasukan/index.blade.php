@extends('layouts.admin')
@section('content')

@php
    $formatRupiah = fn ($value) => rupiah((int) $value);
@endphp

<!-- PAGE HEADER -->
<div style="margin-bottom:24px; padding:22px 24px; border:1px solid #e4edf9; border-radius:20px; background:linear-gradient(135deg, #f7fbff 0%, #eef6ff 52%, #ffffff 100%); box-shadow:0 18px 45px rgba(15, 23, 42, 0.06);">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
        <div style="max-width:620px;">
            <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; background:#dbeafe; color:#1d4ed8; font-size:11px; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:12px;">
                Ringkasan Pemasukan
            </div>
            <h1 style="font-size:24px; line-height:1.2; font-weight:700; color:#0f172a; margin:0;">
                Daftar Pemasukan
            </h1>
            <p style="font-size:13px; line-height:1.6; color:#64748b; margin:10px 0 0;">
                Kelola semua transaksi pemasukan sekolah, lihat nominal secara cepat, dan buka detail bukti transaksi kapan saja.
            </p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:nowrap;">
            <button
                id="btnBukaModal"
                onclick="bukaModal()"
                style="display:inline-flex; align-items:center; gap:10px; min-height:46px; margin:0; padding:11px 16px; border:none; border-radius:14px; background:linear-gradient(135deg, #1D9E75, #16765a); color:white; font-size:13px; font-weight:700; line-height:1; text-decoration:none; cursor:pointer; box-shadow:0 12px 24px rgba(29, 158, 117, 0.24); transition:transform .2s ease, box-shadow .2s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Tambah Pemasukan</span>
            </button>
            <a href="{{ route('admin.pemasukan.spp.create') }}" style="display:inline-flex; align-items:center; gap:10px; min-height:46px; margin:0; padding:11px 16px; border:none; border-radius:14px; background:linear-gradient(135deg, #2563eb, #1d4ed8); color:white; font-size:13px; font-weight:700; line-height:1; text-decoration:none; cursor:pointer; box-shadow:0 12px 24px rgba(37, 99, 235, 0.18); transition:transform .2s ease, box-shadow .2s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                <span>Tambah SPP</span>
            </a>
        </div>
    </div>
</div>

<!-- STAT CARDS -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:24px;">
    <!-- Total Pemasukan -->
    <div style="background:white; border:1px solid #eef2f7; border-radius:18px; padding:18px; box-shadow:0 12px 30px rgba(15, 23, 42, 0.05);">
        <p style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 10px; font-weight:700;">
            Total Pemasukan
        </p>
        <p style="font-size:22px; font-weight:700; color:#059669; margin:0; line-height:1.2;">
            {{ $formatRupiah($totalPemasukan) }}
        </p>
    </div>

    <!-- Total Rows -->
    <div style="background:white; border:1px solid #eef2f7; border-radius:18px; padding:18px; box-shadow:0 12px 30px rgba(15, 23, 42, 0.05);">
        <p style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 10px; font-weight:700;">
            Total Transaksi
        </p>
        <p style="font-size:22px; font-weight:700; color:#0f172a; margin:0; line-height:1.2;">
            {{ $pemasukkans->total() }}
        </p>
    </div>
</div>

<!-- TABEL PEMASUKAN -->
<div style="background:white; border:1px solid #eef2f7; border-radius:20px; overflow:hidden; box-shadow:0 16px 40px rgba(15, 23, 42, 0.06);">
    @if($pemasukkans->count() > 0)
        <table style="width:100%; font-size:13px;">
            <thead>
                <tr style="background:linear-gradient(180deg, #f8fafc 0%, #f3f7fb 100%); border-bottom:1px solid #e5edf5;">
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">No</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Tanggal</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Keterangan</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Jumlah</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Jenis Pemasukan</th>
                    <th style="text-align:center; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemasukkans as $idx => $transaksi)
                    <tr style="border-bottom:1px solid #eef2f7; {{ $loop->last ? 'border-bottom:none;' : '' }}; transition:background-color .2s ease;">
                        <td style="text-align:left; padding:14px 16px; color:#64748b; font-weight:600;">
                            {{ ($pemasukkans->currentPage() - 1) * $pemasukkans->perPage() + $loop->iteration }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#0f172a; font-weight:600;">
                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#334155; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $transaksi->keterangan }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#059669; font-weight:700;">
                            + {{ $transaksi->format_uang }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#64748b;">
                            @php
                                $jenisPemasukan = $transaksi->jenis_transaksi ?? 'Lain-lain';
                                $jenisPemasukanColor = match($jenisPemasukan) {
                                    'SPP' => 'background:#dbeafe; color:#1d4ed8;',
                                    'Donasi' => 'background:#fef3c7; color:#92400e;',
                                    'Dana BOS' => 'background:#d1fae5; color:#065f46;',
                                    'Lain-lain' => 'background:#f3f4f6; color:#4b5563;',
                                    default => 'background:#ede9fe; color:#5b21b6;',
                                };
                            @endphp
                            <span style="display:inline-block; padding:6px 10px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:0.02em; {{ $jenisPemasukanColor }}">
                                {{ $jenisPemasukan }}
                            </span>
                        </td>
                        <td style="text-align:center; padding:14px 16px;">
                            <button 
                                onclick="openDetailModalPemasukan('{{ $transaksi->id }}', '{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}', '{{ $transaksi->format_uang }}', '{{ $transaksi->jenis_transaksi }}', '{{ addslashes($transaksi->keterangan) }}', '{{ $transaksi->bukti_transaksi }}')" 
                                style="background:linear-gradient(135deg, #2563eb, #1d4ed8); color:white; border:none; padding:8px 14px; border-radius:10px; font-size:11px; font-weight:700; cursor:pointer; box-shadow:0 10px 20px rgba(37, 99, 235, 0.18);">
                                Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div style="padding:16px 18px; border-top:1px solid #eef2f7; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; background:#fcfdff;">
            <p style="font-size:12px; color:#64748b; margin:0; font-weight:600;">
                Menampilkan {{ $pemasukkans->firstItem() }} - {{ $pemasukkans->lastItem() }} dari {{ $pemasukkans->total() }} data
            </p>
            <div style="display:flex; gap:4px;">
                @if($pemasukkans->onFirstPage())
                    <button style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#9ca3af; border-radius:4px; cursor:not-allowed; font-size:12px;" disabled>← Sebelumnya</button>
                @else
                    <a href="{{ $pemasukkans->previousPageUrl() }}" style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#1f2937; border-radius:4px; cursor:pointer; font-size:12px; text-decoration:none;">← Sebelumnya</a>
                @endif

                @if($pemasukkans->hasMorePages())
                    <a href="{{ $pemasukkans->nextPageUrl() }}" style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#1f2937; border-radius:4px; cursor:pointer; font-size:12px; text-decoration:none;">Selanjutnya →</a>
                @else
                    <button style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#9ca3af; border-radius:4px; cursor:not-allowed; font-size:12px;" disabled>Selanjutnya →</button>
                @endif
            </div>
        </div>
    @else
        <div style="text-align:center; padding:48px 24px;">
            <svg style="width:64px; height:64px; color:#d1d5db; margin:0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 style="font-size:14px; color:#d1d5db; margin:0 0 12px;">
                Belum ada data pemasukan
            </h3>
            <button 
                onclick="bukaModal()"
                style="background:#1D9E75; color:white; 
                       font-size:13px; font-weight:500;
                       border:none; padding:8px 16px; 
                       border-radius:8px; cursor:pointer;">
                + Tambah Pemasukan Pertama
            </button>
        </div>
    @endif
</div>

<!-- ================================ -->
<!-- MODAL — Tambah Pemasukan        -->
<!-- ================================ -->
<div id="modalTambah"
    style="display:none; position:fixed; inset:0; 
           z-index:9999; align-items:flex-start; 
           justify-content:center; overflow-y:auto; 
           padding:24px 16px; background:rgba(0,0,0,0.5);">
    
    <!-- Backdrop -->
    <div 
        onclick="tutupModal()"
         style="position:absolute; inset:0; 
             background:rgba(0,0,0,0.5);">
    </div>

    <!-- Box Modal -->
    <div style="position:relative; background:white; 
                border-radius:12px; width:100%; 
                max-width:520px; margin:auto; 
                max-height:calc(100vh - 48px); overflow:hidden; 
                display:flex; flex-direction:column; 
                z-index:10000; box-shadow:0 20px 60px rgba(0,0,0,0.18);">

        <!-- Header Modal -->
        <div style="display:flex; align-items:center; 
                justify-content:space-between;
                padding:16px 20px; 
                border-bottom:1px solid #f3f4f6; 
                background:#10b981; color:#ffffff; flex-shrink:0;">
            <div>
                <p style="font-size:14px; font-weight:500; 
                          color:#ffffff; margin:0;">
                    Tambah Pemasukan
                </p>
                <p style="font-size:11px; color:rgba(255,255,255,0.85); margin:4px 0 0;">
                    Isi data transaksi dengan lengkap
                </p>
            </div>
            <button onclick="tutupModal()"
                style="background:none; border:none; color:#ffffff;
                       cursor:pointer; font-size:20px; line-height:1;">
                &times;
            </button>
        </div>

        <!-- Form -->
        <form method="POST" 
              action="{{ route('admin.pemasukan.store') }}"
              enctype="multipart/form-data"
              style="padding:20px; overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch;">
            @csrf
            <input type="hidden" name="jenis" value="pemasukan">

            <!-- Jenis Pemasukan -->
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280; 
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Jenis Pemasukan <span style="color:#ef4444;">*</span>
                </label>
                @error('jenis_pemasukan')
                    <p style="font-size:11px;color:#ef4444;margin:0 0 4px;">
                        {{ $message }}
                    </p>
                @enderror
                <select id="jenisPemasukan" name="jenis_pemasukan" required
                          style="width:100%; font-size:13px; 
                              border:1px solid {{ $errors->has('jenis_pemasukan') ? '#ef4444' : '#e5e7eb' }}; 
                           border-radius:8px; padding:0 12px;
                           height:36px; box-sizing:border-box;
                           outline:none; background:white;">
                    <option value="">-- Pilih Jenis Pemasukan --</option>
                    <option value="Donasi" {{ old('jenis_pemasukan') === 'Donasi' ? 'selected' : '' }}>Donasi</option>
                    <option value="Dana BOS" {{ old('jenis_pemasukan') === 'Dana BOS' ? 'selected' : '' }}>Dana BOS</option>
                    <option value="Lain-lain" {{ old('jenis_pemasukan') === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                </select>
            </div>

            <!-- Tanggal -->
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280; 
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Tanggal
                </label>
                @error('tanggal')
                    <p style="font-size:11px;color:#ef4444;margin:0 0 4px;">
                        {{ $message }}
                    </p>
                @enderror
                <input type="date" name="tanggal"
                    value="{{ old('tanggal', date('Y-m-d')) }}"
                          style="width:100%; font-size:13px; 
                              border:1px solid {{ $errors->has('tanggal') ? '#ef4444' : '#e5e7eb' }}; 
                           border-radius:8px; padding:8px 12px; 
                           height:36px; box-sizing:border-box;
                           outline:none;">
            </div>

            <!-- Jumlah -->
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Jumlah
                </label>
                @error('jumlah')
                    <p style="font-size:11px;color:#ef4444;margin:0 0 4px;">
                        {{ $message }}
                    </p>
                @enderror
                <div style="position:relative;">
                    <span style="position:absolute; left:12px; 
                                 top:50%; transform:translateY(-50%);
                                 font-size:13px; color:#9ca3af;">
                        Rp
                    </span>
                    <input type="text" name="jumlah" 
                        id="inputJumlah"
                        value="{{ old('jumlah') ? number_format((int) old('jumlah'), 0, ',', '.') : '' }}"
                        inputmode="numeric" placeholder="0"
                           style="width:100%; font-size:13px; 
                               border:1px solid {{ $errors->has('jumlah') ? '#ef4444' : '#e5e7eb' }}; 
                               border-radius:8px; 
                               padding:8px 12px 8px 36px;
                               height:36px; box-sizing:border-box;
                               outline:none;">
                </div>
            </div>

            <!-- Keterangan -->
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Keterangan
                </label>
                @error('keterangan')
                    <p style="font-size:11px;color:#ef4444;margin:0 0 4px;">
                        {{ $message }}
                    </p>
                @enderror
                <textarea name="keterangan" rows="3"
                    id="inputKeterangan"
                    maxlength="500"
                    placeholder="Tulis keterangan transaksi..."
                    oninput="hitungKarakter()"
                          style="width:100%; font-size:13px; 
                              border:1px solid {{ $errors->has('keterangan') ? '#ef4444' : '#e5e7eb' }}; 
                           border-radius:8px; padding:8px 12px;
                           resize:none; box-sizing:border-box;
                           outline:none;">{{ old('keterangan') }}</textarea>
                <p style="font-size:10px;color:#9ca3af;
                          text-align:right;margin:2px 0 0;">
                    <span id="hitungChar">0</span>/500
                </p>
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

            <p id="pemasukanMainMessage" class="hidden text-sm font-medium text-red-500 mb-3"></p>

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


        </form>
    </div>
</div>

@include('admin.components.detail-transaksi-modal')

<script>
    function openDetailModalPemasukan(id, tanggal, jumlah, jenis, keterangan, buktiPath) {
        const fields = [
            { label: 'ID Transaksi', value: id },
            { label: 'Tanggal', value: tanggal },
            { label: 'Jumlah', value: jumlah, isPeso: true },
            { label: 'Jenis Pemasukan', value: jenis },
            { label: 'Keterangan', value: keterangan || '-' }
        ];
        openDetailTransaksi(fields, buktiPath);
    }
</script>

@push('scripts')
<script>
    const initialPemasukanSiswa = null;

    function setInlineMessage(elementId, message, type) {
        var el = document.getElementById(elementId);
        if (!el) {
            return;
        }

        el.textContent = message || '';
        el.classList.remove('hidden', 'text-red-500', 'text-emerald-600');

        if (!message) {
            el.classList.add('hidden');
            return;
        }

        el.classList.add(type === 'success' ? 'text-emerald-600' : 'text-red-500');
    }

    function clearInlineMessage(elementId) {
        setInlineMessage(elementId, '', 'error');
    }

    function digitsOnly(value) {
        return String(value || '').replace(/\D/g, '');
    }

    function formatMoneyValue(value) {
        var digits = digitsOnly(value);
        if (!digits || Number(digits) <= 0) {
            return '';
        }

        return Number(digits).toLocaleString('id-ID');
    }

    function formatMoneyInput(input) {
        if (!input) {
            return '';
        }

        var raw = digitsOnly(input.value);
        input.dataset.raw = raw;
        input.value = formatMoneyValue(raw);
        return raw;
    }

    function formatSiswaJumlahInput(input) {
        var raw = formatMoneyInput(input);
        var idx = Number(input.dataset.idx);

        if (!Number.isNaN(idx) && selectedStudents[idx]) {
            selectedStudents[idx].jumlah = raw ? Number(raw) : 0;
        }

        clearInlineMessage('pemasukanMainMessage');
        updateSummary();
    }

    function bukaModal() {
        var modal = document.getElementById('modalTambah');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        clearInlineMessage('pemasukanMainMessage');
        formatMoneyInput(document.getElementById('inputJumlah'));
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
        return siswa ? siswa.nama + ' (NIK: ' + siswa.nik + ')' : '';
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
                formatMoneyInput(inputJumlah);
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
            var btnHtml = already ? '<button type="button" disabled style="background:#9ca3af; color:white; border:none; border-radius:8px; padding:7px 12px; font-size:12px;">Sudah Dipilih</button>' : '<button type="button" class="btn-pilih-siswa" data-id="' + escapeHtml(siswa.id) + '" data-nik="' + escapeHtml(siswa.nik || '') + '" data-nama="' + escapeHtml(siswa.nama || '') + '" data-kelas="' + escapeHtml(siswa.kelas || '') + '" style="background:#1D9E75; color:white; border:none; border-radius:8px; padding:7px 12px; font-size:12px; cursor:pointer;">Pilih</button>';
            return '<tr style="border-bottom:1px solid #f3f4f6;">' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.nik || '-') + '</td>' +
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
                '<td style="padding:12px 16px;">' + escapeHtml(s.nik || '-') + '</td>' +
                '<td style="padding:12px 16px;">' + escapeHtml(s.kelas || '-') + '</td>' +
                '<td style="padding:12px 16px; text-align:right;">' +
                    '<div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">' +
                        '<input type="text" inputmode="numeric" value="' + formatMoneyValue(s.jumlah || '') + '" data-raw="' + (s.jumlah || '') + '" data-idx="' + idx + '" oninput="formatSiswaJumlahInput(this)" class="siswa-jumlah-input" style="width:120px; padding:6px 8px; border:1px solid #e5e7eb; border-radius:8px; text-align:right;" />' +
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
            jumlahInput.dataset.raw = '';
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
        selectedStudents.push({ id: siswa.id, nama: siswa.nama, nik: siswa.nik, kelas: siswa.kelas, jumlah: 0 });
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

    // Preview modal
    function bukaPreviewModal() {
        // validate
        if (selectedStudents.length === 0) {
            setInlineMessage('pemasukanMainMessage', 'Pilih minimal 1 siswa.', 'error');
            return;
        }
        var invalid = selectedStudents.find(s => !s.jumlah || Number(s.jumlah) <= 0);
        if (invalid) {
            setInlineMessage('pemasukanMainMessage', 'Pastikan semua siswa memiliki jumlah > 0.', 'error');
            return;
        }

        clearInlineMessage('pemasukanMainMessage');

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
                '<td style="padding:8px 12px;">'+escapeHtml(s.nik || '-')+'</td>' +
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
            '<p id="previewBulkMessage" class="hidden mb-3 text-sm font-medium"></p>' +
            '<div style="margin-bottom:12px; color:#374151;">Keterangan: '+escapeHtml(keterangan || '-')+'</div>' +
            '<div style="border:1px solid #f3f4f6; border-radius:8px; overflow:hidden;"><table style="width:100%;">' +
            '<thead><tr style="background:#f9fafb;"><th style="padding:8px 12px;">No</th><th style="padding:8px 12px;">Nama</th><th style="padding:8px 12px;">NIK</th><th style="padding:8px 12px;">Kelas</th><th style="padding:8px 12px; text-align:right;">Jumlah</th></tr></thead>' +
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

        clearInlineMessage('previewBulkMessage');

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
                setInlineMessage('previewBulkMessage', 'Berhasil menyimpan ' + data.count + ' transaksi SPP.', 'success');
                setTimeout(function () {
                    window.location.reload();
                }, 1200);
            } else {
                setInlineMessage('previewBulkMessage', data.message || 'Terjadi kesalahan.', 'error');
            }
        } catch (e) {
            console.error(e);
            setInlineMessage('previewBulkMessage', 'Terjadi kesalahan saat menyimpan.', 'error');
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

        tbody.innerHTML = '<tr><td colspan="5" style="padding:20px 16px; text-align:center; color:#9ca3af;">Mencari data siswa...</td></tr>';

        try {
            var response = await fetch('/siswa/search?q=' + encodeURIComponent(q), {
                headers: {
                    'Accept': 'application/json'
                }
            });
            var result = await response.json();
            renderHasilSiswa(Array.isArray(result) ? result : (result.data || []));
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px 16px; text-align:center; color:#ef4444;">Gagal memuat data siswa.</td></tr>';
        }
    }

    document.addEventListener('change', function (event) {
        if (event.target && event.target.id === 'jenisPemasukan') {
            clearInlineMessage('pemasukanMainMessage');
            toggleSiswaSection();
        }

        if (event.target && event.target.name === 'bukti_transaksi') {
            clearInlineMessage('pemasukanMainMessage');
        }
    });

    document.addEventListener('input', function (event) {
        if (!event.target) {
            return;
        }

        if (event.target.id === 'inputJumlah') {
            formatMoneyInput(event.target);
            clearInlineMessage('pemasukanMainMessage');
        }

        if (event.target.classList.contains('siswa-jumlah-input')) {
            formatSiswaJumlahInput(event.target);
            return;
        }

        if (event.target.id === 'inputKeterangan') {
            clearInlineMessage('pemasukanMainMessage');
        }
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || !form.querySelector('#inputJumlah')) {
            return;
        }

        var inputJumlah = form.querySelector('#inputJumlah');
        if (inputJumlah && !inputJumlah.disabled) {
            inputJumlah.value = digitsOnly(inputJumlah.dataset.raw || inputJumlah.value);
        }
    });

    document.addEventListener('click', function (event) {
        // Open student popup (single-select shortcut)
        if (event.target && event.target.id === 'btnPilihSiswa') {
            bukaModalSiswa();
            return;
        }

        // Open student popup for bulk add
        if (event.target && event.target.id === 'btnTambahSiswa') {
            bukaModalSiswa();
            return;
        }

        // Single-select 'Pilih' from search results (keeps old behavior)
        if (event.target && event.target.classList.contains('btn-pilih-siswa') && event.target.closest('#modalSiswa')) {
            // If siswa-list-section is visible (bulk mode), add to selectedStudents
            var siswaObj = {
                id: event.target.dataset.id,
                nik: event.target.dataset.nik,
                nama: event.target.dataset.nama,
                kelas: event.target.dataset.kelas
            };

            var siswaListSection = document.getElementById('siswa-list-section');
            if (siswaListSection && siswaListSection.style.display === 'block') {
                addSelectedStudent(siswaObj);
            } else {
                // fallback: single-select behavior
                pilihSiswa(siswaObj);
            }
            return;
        }

        // Handle remove from selected list
        if (event.target && event.target.classList.contains('btn-hapus-siswa')) {
            var idx = event.target.dataset.idx;
            if (typeof idx !== 'undefined') {
                removeSelectedStudent(Number(idx));
            }
            return;
        }

        // Clear selected students
        if (event.target && event.target.id === 'btnKosongkanSiswa') {
            selectedStudents = [];
            renderSiswaTable();
            return;
        }

        // Kosongkan per baris siswa
        if (event.target && event.target.classList.contains('btn-kosongkan-siswa')) {
            kosongkanSiswa(event.target);
            return;
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target && event.target.id === 'searchSiswa') {
            clearTimeout(debounceSiswaSearch);
            debounceSiswaSearch = setTimeout(cariSiswa, 250);
        }

        // update jumlah per siswa in selectedStudents
        if (event.target && event.target.classList && event.target.classList.contains('siswa-jumlah-input')) {
            var idx = event.target.dataset.idx;
            if (typeof idx !== 'undefined' && selectedStudents[Number(idx)]) {
                var rawJumlah = digitsOnly(event.target.dataset.raw || event.target.value);
                selectedStudents[Number(idx)].jumlah = rawJumlah ? Number(rawJumlah) : 0;
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
@endpush

@endsection
