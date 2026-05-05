@extends('layouts.admin')
@section('content')

@php
    $formatRupiah = fn ($value) => rupiah((int) $value);
    $selectedSiswa = $siswas->firstWhere('id', (int) old('siswa_id'));
    $initialPemasukanSiswa = $selectedSiswa
        ? [
            'id' => $selectedSiswa->id,
            'nik' => $selectedSiswa->nik,
            'nama' => $selectedSiswa->nama,
            'kelas' => $selectedSiswa->kelas,
        ]
        : null;
@endphp

<!-- PAGE HEADER -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
        <h1 style="font-size:16px; font-weight:500; color:#1f2937; margin:0;">
            Daftar Pemasukan
        </h1>
        <p style="font-size:12px; color:#9ca3af; margin:4px 0 0;">
            Kelola semua transaksi pemasukan sekolah
        </p>
    </div>
    <!-- TOMBOL TRIGGER MODAL -->
    <button 
        id="btnBukaModal"
        onclick="bukaModal()"
        style="display:flex; align-items:center; gap:8px; 
               background:#1D9E75; color:white; 
               font-size:13px; font-weight:500;
               border:none; padding:8px 16px; 
               border-radius:8px; cursor:pointer;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Tambah Pemasukan</span>
    </button>
</div>

<!-- STAT CARDS -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:24px;">
    <!-- Total Pemasukan -->
    <div style="background:white; border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
            Total Pemasukan
        </p>
        <p style="font-size:18px; font-weight:600; color:#059669; margin:0;">
            {{ $formatRupiah($totalPemasukan) }}
        </p>
    </div>

    <!-- Total Rows -->
    <div style="background:white; border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
            Total Transaksi
        </p>
        <p style="font-size:18px; font-weight:600; color:#4b5563; margin:0;">
            {{ $pemasukkans->total() }}
        </p>
    </div>
</div>

<!-- TABEL PEMASUKAN -->
<div style="background:white; border:1px solid #f3f4f6; border-radius:12px; overflow:hidden;">
    @if($pemasukkans->count() > 0)
        <table style="width:100%; font-size:13px;">
            <thead>
                <tr style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">No</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Tanggal</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Keterangan</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Jenis Pemasukan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemasukkans as $idx => $transaksi)
                    <tr style="border-bottom:1px solid #f3f4f6; {{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <td style="text-align:left; padding:12px 16px; color:#6b7280;">
                            {{ ($pemasukkans->currentPage() - 1) * $pemasukkans->perPage() + $loop->iteration }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#1f2937;">
                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#1f2937; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $transaksi->keterangan }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#059669; font-weight:500;">
                            + {{ $formatRupiah($transaksi->jumlah) }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#6b7280;">
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
                            <span style="display:inline-block; padding:4px 8px; border-radius:999px; font-size:11px; font-weight:600; {{ $jenisPemasukanColor }}">
                                {{ $jenisPemasukan }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div style="padding:16px; border-top:1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center;">
            <p style="font-size:12px; color:#9ca3af; margin:0;">
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
           z-index:9999; align-items:center; 
           justify-content:center;">
    
    <!-- Backdrop -->
    <div 
        onclick="tutupModal()"
        style="position:absolute; inset:0; 
               background:rgba(0,0,0,0.45);">
    </div>

    <!-- Box Modal -->
    <div style="position:relative; background:white; 
                border-radius:16px; width:100%; 
                max-width:520px; margin:0 16px; 
                max-height:90vh; overflow-y:auto; 
                z-index:10000;">

        <!-- Header Modal -->
        <div style="display:flex; align-items:center; 
                    justify-content:space-between;
                    padding:16px 24px; 
                    border-bottom:1px solid #f3f4f6;">
            <div>
                <p style="font-size:14px; font-weight:500; 
                          color:#1f2937; margin:0;">
                    Tambah Pemasukan
                </p>
                <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">
                    Isi data transaksi dengan lengkap
                </p>
            </div>
            <button onclick="tutupModal()"
                style="width:28px; height:28px; border:none;
                       background:#f9fafb; border-radius:8px;
                       cursor:pointer; font-size:16px; 
                       color:#6b7280; line-height:1;">
                &times;
            </button>
        </div>

        <!-- Form -->
        <form method="POST" 
              action="{{ route('admin.pemasukan.store') }}"
              enctype="multipart/form-data"
              style="padding:20px 24px;">
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
                <select id="jenisPemasukan" name="jenis_pemasukan" required
                    style="width:100%; font-size:13px; 
                           border:1px solid #e5e7eb; 
                           border-radius:8px; padding:0 12px;
                           height:36px; box-sizing:border-box;
                           outline:none; background:white;">
                    <option value="">-- Pilih Jenis Pemasukan --</option>
                    <option value="SPP" {{ old('jenis_pemasukan') === 'SPP' ? 'selected' : '' }}>SPP</option>
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
                <input type="date" name="tanggal"
                    value="{{ old('tanggal', date('Y-m-d')) }}"
                    style="width:100%; font-size:13px; 
                           border:1px solid #e5e7eb; 
                           border-radius:8px; padding:8px 12px; 
                           height:36px; box-sizing:border-box;
                           outline:none;">
                @error('tanggal')
                    <p style="font-size:11px;color:#ef4444;margin:4px 0 0;">
                        {{ $message }}
                    </p>
                @enderror
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
                <div style="position:relative;">
                    <span style="position:absolute; left:12px; 
                                 top:50%; transform:translateY(-50%);
                                 font-size:13px; color:#9ca3af;">
                        Rp
                    </span>
                    <input type="number" name="jumlah" 
                        id="inputJumlah"
                        value="{{ old('jumlah') }}"
                        min="1" placeholder="0"
                        style="width:100%; font-size:13px; 
                               border:1px solid #e5e7eb; 
                               border-radius:8px; 
                               padding:8px 12px 8px 36px;
                               height:36px; box-sizing:border-box;
                               outline:none;">
                </div>
                @error('jumlah')
                    <p style="font-size:11px;color:#ef4444;margin:4px 0 0;">
                        {{ $message }}
                    </p>
                @enderror
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
                <textarea name="keterangan" rows="3"
                    id="inputKeterangan"
                    maxlength="500"
                    placeholder="Tulis keterangan transaksi..."
                    oninput="hitungKarakter()"
                    style="width:100%; font-size:13px; 
                           border:1px solid #e5e7eb; 
                           border-radius:8px; padding:8px 12px;
                           resize:none; box-sizing:border-box;
                           outline:none;">{{ old('keterangan') }}</textarea>
                <p style="font-size:10px;color:#9ca3af;
                          text-align:right;margin:2px 0 0;">
                    <span id="hitungChar">0</span>/500
                </p>
                @error('keterangan')
                    <p style="font-size:11px;color:#ef4444;margin:4px 0 0;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Siswa (SPP only) -->
            <div id="siswa-section" style="display:none; margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Siswa <span style="color:#ef4444;">*</span>
                </label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="text" id="siswa_display"
                        value="{{ $selectedSiswa ? $selectedSiswa->nama_nik : '' }}"
                        readonly placeholder="Pilih siswa"
                        style="flex:1; width:100%; font-size:13px; 
                               border:1px solid #e5e7eb; 
                               border-radius:8px; padding:0 12px;
                               height:36px; box-sizing:border-box;
                               outline:none; background:#f9fafb; color:#374151;">
                    <button type="button" id="btnPilihSiswa"
                        style="flex-shrink:0; background:#1D9E75; color:white; 
                               font-size:13px; font-weight:500;
                               border:none; padding:0 14px; 
                               border-radius:8px; cursor:pointer; height:36px;">
                        Pilih Siswa
                    </button>
                </div>
                <input type="hidden" name="siswa_id" id="siswa_id" value="{{ old('siswa_id') }}">
                @error('siswa_id')
                    <p style="font-size:11px;color:#ef4444;margin:4px 0 0;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Upload Bukti -->
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Bukti Transaksi
                    <span style="color:#d1d5db;
                                 text-transform:none;">
                        (opsional)
                    </span>
                </label>
                <input type="file" name="bukti_transaksi"
                    accept=".jpg,.jpeg,.png,.pdf"
                    style="width:100%; font-size:12px; 
                           border:1px solid #e5e7eb; 
                           border-radius:8px; padding:6px 12px;
                           box-sizing:border-box;">
                <p style="font-size:10px;color:#9ca3af;margin:4px 0 0;">
                    JPG, PNG, PDF maksimal 2MB
                </p>
                @error('bukti_transaksi')
                    <p style="font-size:11px;color:#ef4444;margin:4px 0 0;">
                        {{ $message }}
                    </p>
                @enderror
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

        </form>
    </div>
</div>

<!-- ================================ -->
<!-- MODAL — Pilih Siswa             -->
<!-- ================================ -->
<div id="modalSiswa"
    style="display:none; position:fixed; inset:0; 
           z-index:10010; align-items:center; 
           justify-content:center;">
    <div onclick="tutupModalSiswa()"
        style="position:absolute; inset:0; background:rgba(0,0,0,0.45);"></div>

    <div style="position:relative; background:white; 
                border-radius:16px; width:100%; 
                max-width:720px; margin:0 16px; 
                max-height:90vh; overflow:hidden; 
                z-index:10020;">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:16px 24px; background:#1D9E75; color:white;">
            <div>
                <p style="font-size:14px; font-weight:600; margin:0;">Pilih Siswa</p>
                <p style="font-size:11px; opacity:0.9; margin:4px 0 0;">Cari siswa berdasarkan NIK, nama, atau kelas</p>
            </div>
            <button type="button" onclick="tutupModalSiswa()"
                style="width:28px; height:28px; border:none; background:rgba(255,255,255,0.15); border-radius:8px; cursor:pointer; font-size:16px; color:white; line-height:1;">
                &times;
            </button>
        </div>

        <div style="padding:20px 24px;">
            <input type="text" id="searchSiswa"
                placeholder="Cari NIK, nama, atau kelas..."
                style="width:100%; font-size:13px; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px; box-sizing:border-box; outline:none; margin-bottom:16px;">

            <div style="border:1px solid #f3f4f6; border-radius:12px; overflow:hidden;">
                <table style="width:100%; font-size:13px;">
                    <thead>
                        <tr style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
                            <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">NIK</th>
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
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const initialPemasukanSiswa = @json($initialPemasukanSiswa);

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
        var siswaSection = document.getElementById('siswa-section');
        var siswaDisplay = document.getElementById('siswa_display');
        var siswaId = document.getElementById('siswa_id');

        if (!jenisSelect || !siswaSection) {
            return;
        }

        if (jenisSelect.value === 'SPP') {
            siswaSection.style.display = 'block';
            if (siswaDisplay) {
                siswaDisplay.required = true;
            }
        } else {
            siswaSection.style.display = 'none';
            if (siswaDisplay) {
                siswaDisplay.required = false;
                siswaDisplay.value = '';
            }
            if (siswaId) {
                siswaId.value = '';
            }
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

        tbody.innerHTML = data.map(function (siswa) {
            return '<tr style="border-bottom:1px solid #f3f4f6;">' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.nik || '-') + '</td>' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.nama || '-') + '</td>' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.kelas || '-') + '</td>' +
                '<td style="padding:12px 16px; color:#1f2937;">' + escapeHtml(siswa.jenis_kelamin || '-') + '</td>' +
                '<td style="padding:12px 16px; text-align:center;">' +
                    '<button type="button" class="btn-pilih-siswa" data-id="' + escapeHtml(siswa.id) + '" data-nik="' + escapeHtml(siswa.nik || '') + '" data-nama="' + escapeHtml(siswa.nama || '') + '" data-kelas="' + escapeHtml(siswa.kelas || '') + '" style="background:#1D9E75; color:white; border:none; border-radius:8px; padding:7px 12px; font-size:12px; cursor:pointer;">Pilih</button>' +
                '</td>' +
            '</tr>';
        }).join('');
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
            toggleSiswaSection();
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'btnPilihSiswa') {
            bukaModalSiswa();
        }

        if (event.target && event.target.classList.contains('btn-pilih-siswa')) {
            pilihSiswa({
                id: event.target.dataset.id,
                nik: event.target.dataset.nik,
                nama: event.target.dataset.nama,
                kelas: event.target.dataset.kelas,
            });
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target && event.target.id === 'searchSiswa') {
            clearTimeout(debounceSiswaSearch);
            debounceSiswaSearch = setTimeout(cariSiswa, 250);
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
            pilihSiswa(initialPemasukanSiswa);
            var siswaSection = document.getElementById('siswa-section');
            if (siswaSection) {
                siswaSection.style.display = 'block';
            }
        }
    });
</script>
@endpush
