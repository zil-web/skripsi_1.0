@extends('layouts.admin')
@section('content')

@php
    $formatRupiah = fn ($value) => rupiah((int) $value);
@endphp

<!-- PAGE HEADER -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
        <h1 style="font-size:16px; font-weight:500; color:#1f2937; margin:0;">
            Daftar Pengeluaran
        </h1>
        <p style="font-size:12px; color:#9ca3af; margin:4px 0 0;">
            Kelola semua transaksi pengeluaran sekolah
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
        <span>Tambah Pengeluaran</span>
    </button>
</div>

<!-- STAT CARDS -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:24px;">
    <!-- Total Pengeluaran -->
    <div style="background:white; border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
            Total Pengeluaran
        </p>
        <p style="font-size:18px; font-weight:600; color:#dc2626; margin:0;">
            {{ $formatRupiah($totalPengeluaran) }}
        </p>
    </div>

    <!-- Total Pending -->
    <div style="background:white; border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
            Pending Review
        </p>
        <p style="font-size:18px; font-weight:600; color:#d97706; margin:0;">
            {{ $formatRupiah($totalPending) }}
        </p>
    </div>

    <!-- Total Approved -->
    <div style="background:white; border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
            Sudah Disetujui
        </p>
        <p style="font-size:18px; font-weight:600; color:#dc2626; margin:0;">
            {{ $formatRupiah($totalApproved) }}
        </p>
    </div>

    <!-- Total Rows -->
    <div style="background:white; border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
            Total Transaksi
        </p>
        <p style="font-size:18px; font-weight:600; color:#4b5563; margin:0;">
            {{ $pengeluarans->total() }}
        </p>
    </div>
</div>

<!-- TABEL PENGELUARAN -->
<div style="background:white; border:1px solid #f3f4f6; border-radius:12px; overflow:hidden;">
    @if($pengeluarans->count() > 0)
        <table style="width:100%; font-size:13px;">
            <thead>
                <tr style="background:#f9fafb; border-bottom:1px solid #f3f4f6;">
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">No</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Tanggal</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Keterangan</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px; font-weight:500; color:#6b7280;">Jenis Pengeluaran</th>
                    <th style="text-align:center; padding:12px 16px; font-weight:500; color:#6b7280;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengeluarans as $idx => $transaksi)
                    <tr style="border-bottom:1px solid #f3f4f6; {{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <td style="text-align:left; padding:12px 16px; color:#6b7280;">
                            {{ ($pengeluarans->currentPage() - 1) * $pengeluarans->perPage() + $loop->iteration }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#1f2937;">
                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#1f2937; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $transaksi->keterangan }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#dc2626; font-weight:500;">
                            - {{ $formatRupiah($transaksi->jumlah) }}
                        </td>
                        <td style="text-align:left; padding:12px 16px; color:#6b7280;">
                            @php
                                $jenisPengeluaran = $transaksi->jenis_transaksi ?? 'Lain-lain';
                                $jenisPengeluaranColor = match($jenisPengeluaran) {
                                    'ATK' => 'background:#e0f2fe; color:#075985;',
                                    'Konsumsi Harian' => 'background:#fef3c7; color:#92400e;',
                                    'Pembelian Aset' => 'background:#fee2e2; color:#991b1b;',
                                    'Renovasi' => 'background:#ffedd5; color:#9a3412;',
                                    'Kegiatan Besar' => 'background:#ede9fe; color:#5b21b6;',
                                    'Lain-lain' => 'background:#f3f4f6; color:#4b5563;',
                                    default => 'background:#e5e7eb; color:#374151;',
                                };
                            @endphp
                            <span style="display:inline-block; padding:4px 8px; border-radius:999px; font-size:11px; font-weight:600; {{ $jenisPengeluaranColor }}">
                                {{ $jenisPengeluaran }}
                            </span>
                        </td>
                        <td style="text-align:center; padding:12px 16px;">
                            <span style="display:inline-block; padding:4px 8px; border-radius:4px; font-size:11px; font-weight:500; {{ match($transaksi->status) {
                                'pending' => 'background:#fef3c7; color:#92400e;',
                                'approved' => 'background:#d1fae5; color:#065f46;',
                                'rejected' => 'background:#fee2e2; color:#991b1b;',
                                default => 'background:#f3f4f6; color:#4b5563;'
                            } }}">
                                {{ ucfirst($transaksi->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div style="padding:16px; border-top:1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center;">
            <p style="font-size:12px; color:#9ca3af; margin:0;">
                Menampilkan {{ $pengeluarans->firstItem() }} - {{ $pengeluarans->lastItem() }} dari {{ $pengeluarans->total() }} data
            </p>
            <div style="display:flex; gap:4px;">
                @if($pengeluarans->onFirstPage())
                    <button style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#9ca3af; border-radius:4px; cursor:not-allowed; font-size:12px;" disabled>← Sebelumnya</button>
                @else
                    <a href="{{ $pengeluarans->previousPageUrl() }}" style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#1f2937; border-radius:4px; cursor:pointer; font-size:12px; text-decoration:none;">← Sebelumnya</a>
                @endif

                @if($pengeluarans->hasMorePages())
                    <a href="{{ $pengeluarans->nextPageUrl() }}" style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#1f2937; border-radius:4px; cursor:pointer; font-size:12px; text-decoration:none;">Selanjutnya →</a>
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
                Belum ada data pengeluaran
            </h3>
            <button 
                onclick="bukaModal()"
                style="background:#1D9E75; color:white; 
                       font-size:13px; font-weight:500;
                       border:none; padding:8px 16px; 
                       border-radius:8px; cursor:pointer;">
                + Tambah Pengeluaran Pertama
            </button>
        </div>
    @endif
</div>

<!-- ================================ -->
<!-- MODAL — Tambah Pengeluaran      -->
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
                    Tambah Pengeluaran
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
              action="{{ route('admin.pengeluaran.store') }}"
              enctype="multipart/form-data"
              style="padding:20px 24px;">
            @csrf
            <input type="hidden" name="jenis" value="pengeluaran">
            <input type="hidden" name="status" value="pending">

            <!-- Jenis Pengeluaran -->
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; 
                              font-weight:500; color:#6b7280;
                              text-transform:uppercase; 
                              letter-spacing:0.05em; 
                              margin-bottom:4px;">
                    Jenis Pengeluaran <span style="color:#ef4444;">*</span>
                </label>
                <select name="jenis_pengeluaran" required
                    style="width:100%; font-size:13px; 
                           border:1px solid #e5e7eb; 
                           border-radius:8px; padding:0 12px;
                           height:36px; box-sizing:border-box;
                           outline:none; background:white;">
                    <option value="">-- Pilih Jenis Pengeluaran --</option>
                    <option value="ATK" {{ old('jenis_pengeluaran') === 'ATK' ? 'selected' : '' }}>ATK (Alat Tulis Kantor)</option>
                    <option value="Konsumsi Harian" {{ old('jenis_pengeluaran') === 'Konsumsi Harian' ? 'selected' : '' }}>Konsumsi Harian</option>
                    <option value="Pembelian Aset" {{ old('jenis_pengeluaran') === 'Pembelian Aset' ? 'selected' : '' }}>Pembelian Aset</option>
                    <option value="Renovasi" {{ old('jenis_pengeluaran') === 'Renovasi' ? 'selected' : '' }}>Renovasi</option>
                    <option value="Kegiatan Besar" {{ old('jenis_pengeluaran') === 'Kegiatan Besar' ? 'selected' : '' }}>Kegiatan Besar</option>
                    <option value="Lain-lain" {{ old('jenis_pengeluaran') === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
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

@endsection

@push('scripts')
<script>
    function bukaModal() {
        var modal = document.getElementById('modalTambah');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
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

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModal();
    });

    // Buka otomatis jika ada error validasi
    @if($errors->any())
        window.addEventListener('load', function() { bukaModal(); });
    @endif

    // Init character counter
    window.addEventListener('load', function() {
        hitungKarakter();
    });
</script>
@endpush
