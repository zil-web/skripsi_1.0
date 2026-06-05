@extends('layouts.admin')

@section('title', 'Tambah SPP')
@section('page-title', 'Tambah Pemasukan SPP')
@section('page-subtitle', 'Pilih siswa, atur nominal dan bukti per siswa, lalu simpan.')

@section('content')

@php
  $kelasList = $siswas->pluck('kelas')->unique()->sort()->values();
@endphp

<form action="{{ route('admin.pemasukan.spp.store') }}" method="POST" enctype="multipart/form-data" id="formSpp">
  @csrf
  <input type="hidden" name="jenis_pemasukan" value="SPP">
  <input type="hidden" name="siswa_list" id="siswaListInput">

  <div class="flex gap-4 items-start">

    {{-- ── LEFT COLUMN ── --}}
    <div class="w-80 flex-shrink-0 sticky top-4">
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm text-gray-800">
          Detail Transaksi
        </div>

        <div class="p-4 space-y-4">

          {{-- Tanggal --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              Tanggal <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal"
              value="{{ old('tanggal', date('Y-m-d')) }}"
              required
              class="w-full text-sm rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 @error('tanggal') border-red-400 @enderror">
            @error('tanggal')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Keterangan --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" maxlength="500" placeholder="SPP Bulan..."
              class="w-full text-sm rounded-lg border border-gray-300 px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-green-400 @error('keterangan') border-red-400 @enderror">{{ old('keterangan') }}</textarea>
            <p class="text-xs text-gray-400 mt-0.5">Maksimal 500 karakter.</p>
            @error('keterangan')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Nominal Default --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal Default</label>
            <div class="flex gap-2">
              <div class="flex rounded-lg border border-gray-300 overflow-hidden flex-1">
                <span class="bg-gray-50 px-2 text-xs text-gray-500 flex items-center border-r border-gray-300">Rp</span>
                <input type="text" id="jumlahDefault" inputmode="numeric" placeholder="0"
                  class="flex-1 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400">
              </div>
              <button type="button" id="btnTerapkan"
                class="text-xs px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 whitespace-nowrap transition-colors">
                Terapkan
              </button>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">Diterapkan ke semua siswa yang dicentang.</p>
          </div>

          {{-- Filter Kelas --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2">Filter Kelas</label>
            <div class="flex flex-wrap gap-1" id="filterKelas">
              <button type="button"
                class="kelas-badge text-xs px-3 py-1 rounded-full font-medium transition-colors bg-green-600 text-white"
                data-kelas="semua">Semua</button>
              @foreach($kelasList as $kelas)
                <button type="button"
                  class="kelas-badge text-xs px-3 py-1 rounded-full font-medium transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200"
                  data-kelas="{{ $kelas }}">{{ $kelas }}</button>
              @endforeach
            </div>
          </div>

          {{-- Summary --}}
          <div id="summaryBar" class="rounded-lg bg-green-50 border border-green-200 px-3 py-2.5 text-sm text-green-800">
            <span id="summaryText">0 siswa dipilih — Total Rp 0</span>
          </div>

          <p id="bulkSppMessage" class="hidden text-xs font-medium text-red-500"></p>

          {{-- Submit --}}
          <button type="submit"
            class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition-colors"
            style="background: var(--accent);">
            Simpan Transaksi SPP
          </button>

          {{-- Kembali --}}
          <a href="{{ route('admin.pemasukan.index') }}"
            class="w-full block text-center py-2.5 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
            ← Kembali ke Pemasukan
          </a>

        </div>
      </div>
    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="flex-1 min-w-0">
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Table header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap gap-2 items-center">
          <input type="text" id="searchSiswa" placeholder="Cari nama atau NIK..."
            class="text-sm rounded-lg border border-gray-300 px-3 py-2 flex-1 min-w-0 focus:outline-none focus:ring-2 focus:ring-green-400">
          <span id="selectedCount"
            class="text-xs font-semibold px-3 py-1.5 rounded-full bg-green-100 text-green-700 whitespace-nowrap">
            0 dipilih
          </span>
          <button type="button" id="btnPilihSemua"
            class="text-xs px-3 py-1.5 rounded-lg border border-green-400 text-green-700 bg-white hover:bg-green-50 transition-colors whitespace-nowrap">
            ✓ Pilih Semua
          </button>
          <button type="button" id="btnHapusSemua"
            class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 bg-white hover:bg-red-50 transition-colors whitespace-nowrap">
            ✕ Hapus Semua
          </button>
        </div>

        {{-- Table --}}
        <div class="overflow-auto" style="max-height: 68vh;">
          <table class="w-full text-sm" id="tableSiswa">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide sticky top-0 z-10">
              <tr>
                <th class="px-4 py-3 text-center w-10">
                  <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-green-600 focus:ring-green-400">
                </th>
                <th class="px-4 py-3 text-left w-36">NIK</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-center w-20">Kelas</th>
                <th class="px-4 py-3 text-left w-40">Jumlah (Rp)</th>
                <th class="px-4 py-3 text-left w-48">Bukti Transaksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse($siswas as $siswa)
                <tr class="siswa-row hover:bg-gray-50 transition-colors"
                  data-kelas="{{ $siswa->kelas }}"
                  data-nama="{{ strtolower($siswa->nama) }}"
                  data-nik="{{ $siswa->nik }}">
                <td class="px-4 py-3 text-center">
                  <input type="checkbox" class="siswa-check rounded border-gray-300 text-green-600 focus:ring-green-400"
                    value="{{ $siswa->id }}">
                </td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $siswa->nik }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $siswa->nama }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">
                    {{ $siswa->kelas }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center rounded-lg border border-gray-300 overflow-hidden">
                    <span class="bg-gray-50 px-2 text-xs text-gray-400 border-r border-gray-300 py-2">Rp</span>
                    <input type="text"
                      class="jumlah-input flex-1 text-sm px-2 py-2 focus:outline-none focus:ring-1 focus:ring-green-400 min-w-0"
                      inputmode="numeric" placeholder="0" value="" data-raw="">
                  </div>
                </td>
                <td class="px-4 py-3">
                  <p class="bukti-warning text-xs font-medium text-red-500 mb-1 hidden"
                     data-warning-for="{{ $siswa->id }}">
                    Bukti transaksi wajib diisi.
                  </p>
                  <input type="file"
                    class="bukti-input w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 @error('bukti_siswa.' . $siswa->id) border border-red-400 rounded-lg @enderror"
                    name="bukti_siswa[{{ $siswa->id }}]"
                    accept=".jpg,.jpeg,.png,.pdf"
                    data-siswa-id="{{ $siswa->id }}">
                  <p class="text-xs text-gray-400 mt-0.5">JPG/PDF maks 2MB</p>
                  @error('bukti_siswa.' . $siswa->id)
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                  @enderror
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">
                  Tidak ada data siswa aktif.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.kelas-badge').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.kelas-badge').forEach(b => {
        b.classList.remove('bg-green-600', 'text-white');
        b.classList.add('bg-gray-100', 'text-gray-600');
      });
      this.classList.remove('bg-gray-100', 'text-gray-600');
      this.classList.add('bg-green-600', 'text-white');
      applyFilters();
    });
  });

  document.getElementById('searchSiswa').addEventListener('input', applyFilters);

  function applyFilters() {
    const activeBtn = document.querySelector('.kelas-badge.bg-green-600');
    const kelas = activeBtn ? activeBtn.dataset.kelas : 'semua';
    const q = document.getElementById('searchSiswa').value.toLowerCase();
    document.querySelectorAll('.siswa-row').forEach(row => {
      const km = kelas === 'semua' || row.dataset.kelas === kelas;
      const sm = row.dataset.nama.includes(q) || row.dataset.nik.includes(q);
      row.style.display = (km && sm) ? '' : 'none';
    });
    updateCheckAllState();
  }

  document.getElementById('btnPilihSemua').addEventListener('click', () => {
    visibleRows().forEach(row => row.querySelector('.siswa-check').checked = true);
    clearBulkMessage();
    updateSummary(); updateCheckAllState();
  });

  document.getElementById('btnHapusSemua').addEventListener('click', () => {
    document.querySelectorAll('.siswa-check').forEach(cb => cb.checked = false);
    document.getElementById('checkAll').checked = false;
    clearBulkMessage();
    updateSummary(); updateCheckAllState();
  });

  document.getElementById('checkAll').addEventListener('change', function () {
    visibleRows().forEach(row => row.querySelector('.siswa-check').checked = this.checked);
    clearBulkMessage();
    updateSummary();
  });

  const jumlahDefaultInput = document.getElementById('jumlahDefault');

  function digitsOnly(value) {
    return String(value || '').replace(/\D/g, '');
  }

  function formatAmountValue(value) {
    const digits = digitsOnly(value);
    return digits ? Number(digits).toLocaleString('id-ID') : '';
  }

  function setFormattedAmount(input) {
    if (!input) return '';
    const digits = digitsOnly(input.value);
    input.dataset.raw = digits;
    input.value = formatAmountValue(digits);
    return digits;
  }

  if (jumlahDefaultInput) {
    jumlahDefaultInput.addEventListener('input', () => {
      jumlahDefaultInput.value = formatAmountValue(jumlahDefaultInput.value);
    });
  }

  document.getElementById('btnTerapkan').addEventListener('click', () => {
    const val = setFormattedAmount(jumlahDefaultInput);
    if (!val || Number(val) <= 0) return;
    document.querySelectorAll('.siswa-check:checked').forEach(cb => {
      const jumlahInput = cb.closest('tr').querySelector('.jumlah-input');
      if (!jumlahInput) return;
      jumlahInput.dataset.raw = val;
      jumlahInput.value = formatAmountValue(val);
    });
    clearBulkMessage();
    updateSummary();
  });

  document.querySelectorAll('.siswa-check').forEach(el => {
    el.addEventListener('change', () => { clearBulkMessage(); updateSummary(); updateCheckAllState(); });
  });
  document.querySelectorAll('.jumlah-input').forEach(el => {
    el.addEventListener('input', function () {
      clearBulkMessage();
      setFormattedAmount(this);
      updateSummary();
    });
  });

  document.querySelectorAll('.bukti-input').forEach(input => {
    input.addEventListener('change', function () {
      clearBulkMessage();
      syncBuktiWarning(this.closest('tr'));
    });
  });

  document.querySelectorAll('.siswa-check').forEach(input => {
    input.addEventListener('change', function () {
      clearBulkMessage();
      syncBuktiWarning(this.closest('tr'));
    });
  });

  function visibleRows() {
    return Array.from(document.querySelectorAll('.siswa-row')).filter(r => r.style.display !== 'none');
  }

  function updateSummary() {
    let count = 0, total = 0;
    document.querySelectorAll('.siswa-check:checked').forEach(cb => {
      count++;
      const input = cb.closest('tr').querySelector('.jumlah-input');
      const raw = input ? (input.dataset.raw || digitsOnly(input.value)) : '';
      total += parseFloat(raw) || 0;
    });
    document.getElementById('selectedCount').textContent = count + ' dipilih';
    document.getElementById('summaryText').textContent =
      count + ' siswa dipilih — Total Rp ' + total.toLocaleString('id-ID');
  }

  function syncBuktiWarning(row) {
    if (!row) return;

    const checkbox = row.querySelector('.siswa-check');
    const buktiInput = row.querySelector('.bukti-input');
    const warning = row.querySelector('.bukti-warning');
    const hasFile = buktiInput && buktiInput.files && buktiInput.files.length > 0;
    const shouldShow = checkbox && checkbox.checked && !hasFile;

    if (warning) {
      warning.classList.toggle('hidden', !shouldShow);
    }

    if (buktiInput) {
      buktiInput.classList.toggle('border', shouldShow);
      buktiInput.classList.toggle('border-red-400', shouldShow);
      buktiInput.classList.toggle('rounded-lg', shouldShow);
    }
  }

  function refreshAllBuktiWarnings() {
    document.querySelectorAll('.siswa-row').forEach(row => syncBuktiWarning(row));
  }

  function updateCheckAllState() {
    const vis = visibleRows();
    const checked = vis.filter(r => r.querySelector('.siswa-check').checked).length;
    const ca = document.getElementById('checkAll');
    ca.checked = vis.length > 0 && checked === vis.length;
    ca.indeterminate = checked > 0 && checked < vis.length;
  }

  document.getElementById('formSpp').addEventListener('submit', function (e) {
    const checked = document.querySelectorAll('.siswa-check:checked');
    if (checked.length === 0) {
      e.preventDefault();
      showBulkMessage('Pilih minimal 1 siswa sebelum menyimpan.');
      return;
    }

    const list = [];
    let missingBukti = false;
    checked.forEach(cb => {
      const row = cb.closest('tr');
      const fileInput = row.querySelector('.bukti-input');
      const warning = row.querySelector('.bukti-warning');

      if (!fileInput.files.length) {
        missingBukti = true;
        if (warning) {
          warning.classList.remove('hidden');
        }
        fileInput.classList.add('border', 'border-red-400', 'rounded-lg');
      }

      list.push({
        siswa_id: cb.value,
        jumlah: parseFloat(row.querySelector('.jumlah-input').dataset.raw || digitsOnly(row.querySelector('.jumlah-input').value)) || 0
      });
    });

    if (missingBukti) {
      e.preventDefault();
      showBulkMessage('Semua siswa yang dipilih wajib memiliki bukti transaksi.');
      refreshAllBuktiWarnings();
      return;
    }

    document.getElementById('siswaListInput').value = JSON.stringify(list);
  });

  updateSummary();
  updateCheckAllState();
  refreshAllBuktiWarnings();

  function showBulkMessage(message) {
    const el = document.getElementById('bulkSppMessage');
    if (!el) return;

    el.textContent = message;
    el.classList.remove('hidden');
  }

  function clearBulkMessage() {
    const el = document.getElementById('bulkSppMessage');
    if (!el) return;

    el.textContent = '';
    el.classList.add('hidden');
  }
});
</script>

@endsection