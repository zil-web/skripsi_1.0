@extends('layouts.admin')
@section('content')

@php
    $formatRupiah = fn ($value) => rupiah((int) $value);
@endphp

<!-- PAGE HEADER -->
<div style="margin-bottom:24px; padding:22px 24px; border:1px solid #e5f3ef; border-radius:20px; background:linear-gradient(135deg, #f7fffd 0%, #eefbf7 52%, #ffffff 100%); box-shadow:0 18px 45px rgba(15, 23, 42, 0.06);">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
        <div style="max-width:620px;">
            <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; background:#dcfce7; color:#166534; font-size:11px; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:12px;">
                Ringkasan Pengeluaran
            </div>
            <h1 style="font-size:24px; line-height:1.2; font-weight:700; color:#0f172a; margin:0;">
                Daftar Pengeluaran
            </h1>
            <p style="font-size:13px; line-height:1.6; color:#64748b; margin:10px 0 0;">
                Kelola semua transaksi pengeluaran sekolah, lihat status review, dan buka bukti transaksi langsung dari detail.
            </p>
        </div>
        <button 
            id="btnBukaModal"
            onclick="bukaModal()"
            style="display:inline-flex; align-items:center; gap:10px; background:linear-gradient(135deg, #1D9E75, #16765a); color:white; font-size:13px; font-weight:700; border:none; padding:11px 16px; border-radius:14px; cursor:pointer; box-shadow:0 12px 24px rgba(29, 158, 117, 0.24); transition:transform .2s ease, box-shadow .2s ease;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Tambah Pengeluaran</span>
        </button>
    </div>
</div>

<!-- STAT CARDS -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:24px;">
    <!-- Total Pengeluaran -->
    <div style="background:white; border:1px solid #eef2f7; border-radius:18px; padding:18px; box-shadow:0 12px 30px rgba(15, 23, 42, 0.05);">
        <p style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 10px; font-weight:700;">
            Total Pengeluaran
        </p>
        <p style="font-size:22px; font-weight:700; color:#dc2626; margin:0; line-height:1.2;">
            {{ $formatRupiah($totalPengeluaran) }}
        </p>
    </div>

    <!-- Total Pending -->
    <div style="background:white; border:1px solid #eef2f7; border-radius:18px; padding:18px; box-shadow:0 12px 30px rgba(15, 23, 42, 0.05);">
        <p style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 10px; font-weight:700;">
            Pending Review
        </p>
        <p style="font-size:22px; font-weight:700; color:#d97706; margin:0; line-height:1.2;">
            {{ $formatRupiah($totalPending) }}
        </p>
    </div>

    <!-- Total Approved -->
    <div style="background:white; border:1px solid #eef2f7; border-radius:18px; padding:18px; box-shadow:0 12px 30px rgba(15, 23, 42, 0.05);">
        <p style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 10px; font-weight:700;">
            Sudah Disetujui
        </p>
        <p style="font-size:22px; font-weight:700; color:#10b981; margin:0; line-height:1.2;">
            {{ $formatRupiah($totalApproved) }}
        </p>
    </div>

    <!-- Total Rows -->
    <div style="background:white; border:1px solid #eef2f7; border-radius:18px; padding:18px; box-shadow:0 12px 30px rgba(15, 23, 42, 0.05);">
        <p style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 10px; font-weight:700;">
            Total Transaksi
        </p>
        <p style="font-size:22px; font-weight:700; color:#0f172a; margin:0; line-height:1.2;">
            {{ $pengeluarans->total() }}
        </p>
    </div>
</div>

<!-- TABEL PENGELUARAN -->
<div style="background:white; border:1px solid #eef2f7; border-radius:20px; overflow:hidden; box-shadow:0 16px 40px rgba(15, 23, 42, 0.06);">
    @if($pengeluarans->count() > 0)
        <table style="width:100%; font-size:13px;">
            <thead>
                <tr style="background:linear-gradient(180deg, #f8fafc 0%, #f3f7fb 100%); border-bottom:1px solid #e5edf5;">
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">No</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Tanggal</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Keterangan</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Jumlah</th>
                    <th style="text-align:left; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Jenis Pengeluaran</th>
                    <th style="text-align:center; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Status</th>
                    <th style="text-align:center; padding:14px 16px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; font-size:10px;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengeluarans as $idx => $transaksi)
                    <tr style="border-bottom:1px solid #eef2f7; {{ $loop->last ? 'border-bottom:none;' : '' }}; transition:background-color .2s ease;">
                        <td style="text-align:left; padding:14px 16px; color:#64748b; font-weight:600;">
                            {{ ($pengeluarans->currentPage() - 1) * $pengeluarans->perPage() + $loop->iteration }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#0f172a; font-weight:600;">
                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#334155; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $transaksi->keterangan }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#dc2626; font-weight:700;">
                            - {{ $transaksi->format_uang }}
                        </td>
                        <td style="text-align:left; padding:14px 16px; color:#64748b;">
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
                            <span style="display:inline-block; padding:6px 10px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:0.02em; {{ $jenisPengeluaranColor }}">
                                {{ $jenisPengeluaran }}
                            </span>
                        </td>
                        <td style="text-align:center; padding:14px 16px;">
                            <span style="display:inline-block; padding:6px 10px; border-radius:999px; font-size:11px; font-weight:700; {{ match($transaksi->status) {
                                'pending' => 'background:#fef3c7; color:#92400e;',
                                'approved' => 'background:#d1fae5; color:#065f46;',
                                'rejected' => 'background:#fee2e2; color:#991b1b;',
                                default => 'background:#f3f4f6; color:#4b5563;'
                            } }}">
                                {{ ucfirst($transaksi->status) }}
                            </span>
                        </td>
                        <td style="text-align:center; padding:14px 16px;">
                            <button 
                                onclick="openDetailModal('{{ $transaksi->id }}', '{{ $transaksi->tanggal }}', '{{ $transaksi->format_uang }}', '{{ $transaksi->jenis_transaksi }}', '{{ addslashes($transaksi->keterangan) }}', '{{ $transaksi->bukti_transaksi }}', '{{ ucfirst($transaksi->status) }}')" 
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
                    Tambah Pengeluaran
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
              action="{{ route('admin.pengeluaran.store') }}"
              enctype="multipart/form-data"
              style="padding:20px; overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch;">
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
                    <input type="text" name="jumlah" 
                        id="inputJumlah"
                        value="{{ old('jumlah') ? number_format((int) old('jumlah'), 0, ',', '.') : '' }}"
                        inputmode="numeric" placeholder="0"
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
                    <span style="color:#ef4444;">*</span>
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

@include('admin.components.detail-transaksi-modal')

<script>
    function openDetailModal(id, tanggal, jumlah, jenis, keterangan, buktiPath, status) {
        const fields = [
            { label: 'ID Transaksi', value: id },
            { label: 'Tanggal', value: tanggal },
            { label: 'Jumlah', value: jumlah, isPeso: true },
            { label: 'Jenis Pengeluaran', value: jenis },
            { label: 'Keterangan', value: keterangan || '-' },
            { label: 'Status', value: status }
        ];
        openDetailTransaksi(fields, buktiPath);
    }
</script>

@endsection

@push('scripts')
<script>
    function bukaModal() {
        var modal = document.getElementById('modalTambah');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        formatMoneyInput(document.getElementById('inputJumlah'));
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

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModal();
    });

    document.addEventListener('input', function (event) {
        if (event.target && event.target.id === 'inputJumlah') {
            formatMoneyInput(event.target);
        }
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || !form.querySelector('#inputJumlah')) {
            return;
        }

        var inputJumlah = form.querySelector('#inputJumlah');
        if (inputJumlah) {
            inputJumlah.value = digitsOnly(inputJumlah.dataset.raw || inputJumlah.value);
        }
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
