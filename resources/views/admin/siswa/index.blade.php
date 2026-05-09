@extends('layouts.admin')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola data siswa dan orang tua')

@section('content')

{{-- FLASH MESSAGE --}}
@if(session('success'))
<div id="alertSuccess"
  style="background:#f0fdf4; border:1px solid #bbf7d0;
         color:#15803d; font-size:13px; border-radius:8px;
         padding:12px 16px; margin-bottom:16px;
         display:flex; align-items:center;
         justify-content:space-between;">
  <span>✓ {{ session('success') }}</span>
  <button onclick="this.parentElement.remove()"
    style="background:none;border:none;
           cursor:pointer;color:#15803d;
           font-size:16px;">&times;</button>
</div>
@endif

{{-- PAGE HEADER --}}
<div style="display:flex; align-items:center;
            justify-content:space-between; 
            margin-bottom:20px;">
  <div>
    <h1 style="font-size:15px; font-weight:500;
               color:#1f2937; margin:0;">
      Data Siswa
    </h1>
    <p style="font-size:12px; color:#9ca3af; margin:4px 0 0;">
      Kelola data siswa dan orang tua
    </p>
  </div>
  <button 
    id="btnTambahSiswa"
    style="display:flex; align-items:center; gap:6px;
           background:#1D9E75; color:white; border:none;
           border-radius:8px; padding:8px 16px;
           font-size:13px; font-weight:500; cursor:pointer;">
    + Tambah Siswa
  </button>
</div>

{{-- STAT CARDS --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:24px;">

  <div style="background:white; border:1px solid #f3f4f6;
              border-radius:12px; padding:16px;">
    <p style="font-size:11px; color:#9ca3af;
              text-transform:uppercase; letter-spacing:0.05em;
              margin:0 0 8px;">
      Total Siswa
    </p>
    <p style="font-size:18px; font-weight:600; color:#1f2937; margin:0;">
      {{ $total_siswa }}
    </p>
  </div>

  <div style="background:white; border:1px solid #f3f4f6;
              border-radius:12px; padding:16px;">
    <p style="font-size:11px; color:#9ca3af;
              text-transform:uppercase; letter-spacing:0.05em;
              margin:0 0 8px;">
      Siswa Aktif
    </p>
    <p style="font-size:18px; font-weight:600; color:#16a34a; margin:0;">
      {{ $total_aktif }}
    </p>
  </div>

  <div style="background:white; border:1px solid #f3f4f6;
              border-radius:12px; padding:16px;">
    <p style="font-size:11px; color:#9ca3af;
              text-transform:uppercase; letter-spacing:0.05em;
              margin:0 0 8px;">
      Siswa Non-Aktif
    </p>
    <p style="font-size:18px; font-weight:600; color:#6b7280; margin:0;">
      {{ $total_nonaktif }}
    </p>
  </div>

</div>

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('admin.siswa.index') }}">
<div style="background:white; border:1px solid #f3f4f6;
            border-radius:12px; padding:14px 16px;
            margin-bottom:16px; display:flex;
            align-items:center; gap:8px; flex-wrap:wrap;">

  <input type="text" name="search"
    value="{{ request('search') }}"
    placeholder="Cari NIK atau nama siswa..."
    style="flex:1; min-width:180px; font-size:13px;
           border:1px solid #e5e7eb; border-radius:8px;
           padding:0 12px; height:36px; outline:none;">

  <select name="kelas"
    style="font-size:13px; border:1px solid #e5e7eb;
           border-radius:8px; padding:0 12px;
           height:36px; outline:none; background:white;">
    <option value="">Semua Kelas</option>
    @foreach($daftar_kelas as $kelas)
      <option value="{{ $kelas }}"
        {{ request('kelas') == $kelas ? 'selected' : '' }}>
        Kelas {{ $kelas }}
      </option>
    @endforeach
  </select>

  <select name="status"
    style="font-size:13px; border:1px solid #e5e7eb;
           border-radius:8px; padding:0 12px;
           height:36px; outline:none; background:white;">
    <option value="">Semua Status</option>
    <option value="aktif"
      {{ request('status') == 'aktif' ? 'selected' : '' }}>
      Aktif
    </option>
    <option value="nonaktif"
      {{ request('status') == 'nonaktif' ? 'selected' : '' }}>
      Non-Aktif
    </option>
  </select>

  <button type="submit"
    style="background:#1D9E75; color:white; border:none;
           border-radius:8px; padding:0 16px; height:36px;
           font-size:13px; cursor:pointer;">
    Filter
  </button>

  <a href="{{ route('admin.siswa.index') }}"
    style="background:white; color:#6b7280;
           border:1px solid #e5e7eb; border-radius:8px;
           padding:0 16px; height:36px; font-size:13px;
           display:flex; align-items:center;
           text-decoration:none;">
    Reset
  </a>

</div>
</form>

{{-- TABEL --}}
<div style="background:white; border:1px solid #f3f4f6;
            border-radius:12px; overflow:hidden;">
  <table style="width:100%; border-collapse:collapse;">
    <thead>
      <tr style="border-bottom:1px solid #f9fafb;">
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">No</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">NIK</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">Nama Siswa</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">Nama Orang Tua</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">Kelas</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">No. Telepon</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">Status</th>
        <th style="font-size:10px; color:#9ca3af;
                   font-weight:500; text-align:left;
                   padding:12px 16px; text-transform:uppercase;
                   letter-spacing:0.05em;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($siswas as $index => $siswa)
      <tr style="border-bottom:1px solid #f3f4f6; cursor:pointer;"
          class="siswa-row"
          data-id="{{ $siswa->id }}"
          data-nik="{{ $siswa->nik }}"
          data-nama="{{ $siswa->nama }}"
          data-nama-orangtua="{{ $siswa->nama_orangtua }}"
          data-kelas="{{ $siswa->kelas }}"
          data-jenis-kelamin="{{ $siswa->jenis_kelamin }}"
          data-no-telepon="{{ $siswa->no_telepon ?? '' }}"
          data-alamat="{{ $siswa->alamat ?? '' }}"
          data-is-active="{{ $siswa->is_active ? 'true' : 'false' }}"
          onmouseover="this.style.background='#f9fafb'"
          onmouseout="this.style.background='white'">

        <td style="font-size:12px; color:#9ca3af;
                   padding:12px 16px;">
          {{ $siswas->firstItem() + $index }}
        </td>

        <td style="font-size:12px; color:#1f2937;
                   padding:12px 16px; font-family:monospace;">
          {{ $siswa->nik }}
        </td>

        <td style="padding:12px 16px;">
          <div style="font-size:13px; font-weight:500;
                      color:#1f2937;">
            {{ $siswa->nama }}
          </div>
          <div style="font-size:11px; color:#9ca3af;">
            {{ $siswa->jenis_kelamin_label }}
          </div>
        </td>

        <td style="font-size:12px; color:#6b7280;
                   padding:12px 16px;">
          {{ $siswa->nama_orangtua }}
        </td>

        <td style="padding:12px 16px;">
          <span style="font-size:11px; font-weight:500;
                       background:#eff6ff; color:#2563eb;
                       padding:4px 10px; border-radius:6px;
                       display:inline-block;">
            {{ $siswa->kelas }}
          </span>
        </td>

        <td style="font-size:12px; color:#6b7280;
                   padding:12px 16px;">
          {{ $siswa->no_telepon ?? '—' }}
        </td>

        <td style="padding:12px 16px;">
          @if($siswa->is_active)
            <span style="font-size:11px; font-weight:500;
                         background:#d1fae5; color:#065f46;
                         padding:4px 10px; border-radius:6px;
                         display:inline-block;">
              Aktif
            </span>
          @else
            <span style="font-size:11px; font-weight:500;
                         background:#f3f4f6; color:#6b7280;
                         padding:4px 10px; border-radius:6px;
                         display:inline-block;">
              Non-Aktif
            </span>
          @endif
        </td>

        <td style="padding:12px 16px;">
          <div style="display:flex; gap:6px;">

            {{-- Tombol Edit --}}
            <button
              class="btn-edit-action"
              onclick="event.stopPropagation(); bukaModalEdit(
                '{{ $siswa->id }}',
                '{{ $siswa->nik }}',
                '{{ addslashes($siswa->nama) }}',
                '{{ addslashes($siswa->nama_orangtua) }}',
                '{{ $siswa->kelas }}',
                '{{ $siswa->jenis_kelamin }}',
                '{{ $siswa->no_telepon }}',
                '{{ addslashes($siswa->alamat ?? '') }}',
                {{ $siswa->is_active ? 'true' : 'false' }}
              )"
              style="font-size:11px; padding:6px 12px;
                     border-radius:6px; cursor:pointer;
                     border:1px solid #e5e7eb;
                     background:white; color:#374151;
                     transition:all 0.2s;">
              Edit
            </button>

            {{-- Tombol Hapus --}}
            <button
              onclick="event.stopPropagation(); konfirmasiHapus(
                '{{ $siswa->id }}',
                '{{ $siswa->nama }}'
              )"
              style="font-size:11px; padding:6px 12px;
                     border-radius:6px; cursor:pointer;
                     border:1px solid #fecaca;
                     background:#fff5f5; color:#dc2626;
                     transition:all 0.2s;">
              Hapus
            </button>

            {{-- Form hapus tersembunyi --}}
            <form id="formHapus{{ $siswa->id }}"
              method="POST"
              action="{{ route('admin.siswa.destroy', $siswa) }}"
              style="display:none;">
              @csrf
              @method('DELETE')
            </form>

          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" style="text-align:center;
                                padding:48px 16px;">
          <div style="color:#9ca3af; font-size:13px;">
            Belum ada data siswa
          </div>
          <button type="button" id="btnTambahSiswaEmpty"
            style="margin-top:12px; background:#1D9E75;
                   color:white; border:none;
                   border-radius:8px; padding:8px 16px;
                   font-size:13px; cursor:pointer;">
            + Tambah Siswa Pertama
          </button>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Pagination --}}
  @if($siswas->hasPages())
  <div style="padding:16px; border-top:1px solid #f3f4f6;
              display:flex; justify-content:space-between;
              align-items:center;">
    <p style="font-size:12px; color:#9ca3af; margin:0;">
      Menampilkan {{ $siswas->firstItem() }} - {{ $siswas->lastItem() }} dari {{ $siswas->total() }} data
    </p>
    <div style="display:flex; gap:4px;">
      @if($siswas->onFirstPage())
        <button style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#9ca3af; border-radius:4px; cursor:not-allowed; font-size:12px;" disabled>← Sebelumnya</button>
      @else
        <a href="{{ $siswas->previousPageUrl() }}" style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#1f2937; border-radius:4px; cursor:pointer; font-size:12px; text-decoration:none;">← Sebelumnya</a>
      @endif

      @if($siswas->hasMorePages())
        <a href="{{ $siswas->nextPageUrl() }}" style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#1f2937; border-radius:4px; cursor:pointer; font-size:12px; text-decoration:none;">Selanjutnya →</a>
      @else
        <button style="padding:6px 10px; border:1px solid #e5e7eb; background:white; color:#9ca3af; border-radius:4px; cursor:not-allowed; font-size:12px;" disabled>Selanjutnya →</button>
      @endif
    </div>
  </div>
  @endif
</div>

{{-- ══════════════════════════════ --}}
{{-- MODAL TAMBAH                  --}}
{{-- ══════════════════════════════ --}}
<div id="modalTambahSiswa"
  style="display:none; position:fixed; inset:0;
         z-index:9999; align-items:center;
         justify-content:center;">

  <div id="backdropTambah"
    style="position:absolute; inset:0;
           background:rgba(0,0,0,0.45);">
  </div>

  <div style="position:relative; background:white;
              border-radius:16px; width:100%;
              max-width:560px; margin:0 16px;
              max-height:90vh; overflow-y:auto;
              z-index:10000;">

    {{-- Header --}}
    <div style="display:flex; align-items:center;
                justify-content:space-between;
                padding:16px 24px;
                border-bottom:1px solid #f3f4f6;
                position:sticky; top:0;
                background:white; z-index:1;">
      <div>
        <p style="font-size:14px; font-weight:500;
                  color:#1f2937; margin:0;">
          Tambah Data Siswa
        </p>
        <p style="font-size:11px; color:#9ca3af;
                  margin:4px 0 0;">
          Kolom bertanda * wajib diisi
        </p>
      </div>
      <button id="closeTambah"
        style="width:28px; height:28px; border:none;
               background:#f9fafb; border-radius:8px;
               cursor:pointer; font-size:18px;
               color:#6b7280; line-height:1;">
        &times;
      </button>
    </div>

    {{-- Form --}}
    <form id="formTambahSiswa" method="POST"
      action="{{ route('admin.siswa.store') }}"
      style="padding:20px 24px;">
      @csrf

      {{-- NIK + Kelas --}}
      <div style="display:grid;
                  grid-template-columns:1fr 1fr;
                  gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            NIK * (16 digit)
          </label>
          <input type="text" name="nik"
            value="{{ old('nik') }}"
            maxlength="16"
            placeholder="3271XXXXXXXXXXXX"
            oninput="this.value=
              this.value.replace(/\D/g,'')"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px;
                   padding:8px 12px; height:36px;
                   box-sizing:border-box; outline:none;
                   font-family:monospace;">
          @error('nik')
            <p style="font-size:11px; color:#ef4444;
                      margin:4px 0 0;">
              {{ $message }}
            </p>
          @enderror
        </div>

        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            Kelas *
          </label>
          <select name="kelas"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:0 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; background:white;">
            <option value="">-- Pilih --</option>
            @foreach(['7A','7B','7C','8A','8B','8C',
                      '9A','9B','9C'] as $k)
              <option value="{{ $k }}"
                {{ old('kelas')==$k ? 'selected':'' }}>
                Kelas {{ $k }}
              </option>
            @endforeach
          </select>
          @error('kelas')
            <p style="font-size:11px; color:#ef4444;
                      margin:4px 0 0;">
              {{ $message }}
            </p>
          @enderror
        </div>
      </div>

      {{-- Nama Siswa --}}
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em;
                      margin-bottom:4px;">
          Nama Siswa *
        </label>
        <input type="text" name="nama"
          value="{{ old('nama') }}"
          placeholder="Nama lengkap siswa"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb;
                 border-radius:8px; padding:8px 12px;
                 height:36px; box-sizing:border-box;
                 outline:none;">
        @error('nama')
          <p style="font-size:11px; color:#ef4444;
                    margin:4px 0 0;">
            {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Nama Orang Tua --}}
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em;
                      margin-bottom:4px;">
          Nama Orang Tua *
        </label>
        <input type="text" name="nama_orangtua"
          value="{{ old('nama_orangtua') }}"
          placeholder="Nama lengkap orang tua / wali"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb;
                 border-radius:8px; padding:8px 12px;
                 height:36px; box-sizing:border-box;
                 outline:none;">
        @error('nama_orangtua')
          <p style="font-size:11px; color:#ef4444;
                    margin:4px 0 0;">
            {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Jenis Kelamin + No Telepon --}}
      <div style="display:grid;
                  grid-template-columns:1fr 1fr;
                  gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            Jenis Kelamin
          </label>
          <select name="jenis_kelamin"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:0 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; background:white;">
            <option value="">-- Pilih --</option>
            <option value="L"
              {{ old('jenis_kelamin')=='L'
                 ? 'selected':'' }}>
              Laki-laki
            </option>
            <option value="P"
              {{ old('jenis_kelamin')=='P'
                 ? 'selected':'' }}>
              Perempuan
            </option>
          </select>
        </div>

        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            No. Telepon Ortu
          </label>
          <input type="text" name="no_telepon"
            value="{{ old('no_telepon') }}"
            maxlength="15"
            placeholder="08xxxxxxxxxx"
            oninput="this.value=
              this.value.replace(/[^0-9\+\-]/g,'')"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:8px 12px;
                   height:36px; box-sizing:border-box;
                   outline:none;">
          @error('no_telepon')
            <p style="font-size:11px; color:#ef4444;
                      margin:4px 0 0;">
              {{ $message }}
            </p>
          @enderror
        </div>
      </div>

      {{-- Alamat --}}
      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em;
                      margin-bottom:4px;">
          Alamat (opsional)
        </label>
        <textarea name="alamat" rows="2"
          placeholder="Alamat lengkap siswa..."
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb;
                 border-radius:8px; padding:8px 12px;
                 resize:none; box-sizing:border-box;
                 outline:none;">{{ old('alamat') }}</textarea>
      </div>

      {{-- Footer --}}
      <div style="display:flex; gap:8px;
                  padding-top:16px;
                  border-top:1px solid #f3f4f6;">
        <button type="button" id="btnBatalTambah"
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
          Simpan Data Siswa
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════ --}}
{{-- MODAL EDIT                    --}}
{{-- ══════════════════════════════ --}}
<div id="modalEditSiswa"
  style="display:none; position:fixed; inset:0;
         z-index:9999; align-items:center;
         justify-content:center;">

  <div id="backdropEdit"
    style="position:absolute; inset:0;
           background:rgba(0,0,0,0.45);">
  </div>

  <div style="position:relative; background:white;
              border-radius:16px; width:100%;
              max-width:560px; margin:0 16px;
              max-height:90vh; overflow-y:auto;
              z-index:10000;">

    <div style="display:flex; align-items:center;
                justify-content:space-between;
                padding:16px 24px;
                border-bottom:1px solid #f3f4f6;
                position:sticky; top:0;
                background:white; z-index:1;">
      <div>
        <p style="font-size:14px; font-weight:500;
                  color:#1f2937; margin:0;">
          Edit Data Siswa
        </p>
        <p style="font-size:11px; color:#9ca3af;
                  margin:4px 0 0;">
          Perbarui data siswa yang dipilih
        </p>
      </div>
      <button id="closeEdit"
        style="width:28px; height:28px; border:none;
               background:#f9fafb; border-radius:8px;
               cursor:pointer; font-size:18px;
               color:#6b7280; line-height:1;">
        &times;
      </button>
    </div>

    <form id="formEditSiswa" method="POST"
      style="padding:20px 24px;">
      @csrf
      @method('PUT')

      {{-- NIK + Kelas --}}
      <div style="display:grid;
                  grid-template-columns:1fr 1fr;
                  gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            NIK * (16 digit)
          </label>
          <input type="text" id="editNik" name="nik"
            maxlength="16"
            oninput="this.value=
              this.value.replace(/\D/g,'')"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:8px 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; font-family:monospace;">
        </div>

        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            Kelas *
          </label>
          <select id="editKelas" name="kelas"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:0 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; background:white;">
            <option value="">-- Pilih --</option>
            @foreach(['7A','7B','7C','8A','8B','8C',
                      '9A','9B','9C'] as $k)
              <option value="{{ $k }}">
                Kelas {{ $k }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em;
                      margin-bottom:4px;">
          Nama Siswa *
        </label>
        <input type="text" id="editNama" name="nama"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb;
                 border-radius:8px; padding:8px 12px;
                 height:36px; box-sizing:border-box;
                 outline:none;">
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em;
                      margin-bottom:4px;">
          Nama Orang Tua *
        </label>
        <input type="text" id="editNamaOrtu"
          name="nama_orangtua"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb;
                 border-radius:8px; padding:8px 12px;
                 height:36px; box-sizing:border-box;
                 outline:none;">
      </div>

      <div style="display:grid;
                  grid-template-columns:1fr 1fr;
                  gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            Jenis Kelamin
          </label>
          <select id="editJK" name="jenis_kelamin"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:0 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; background:white;">
            <option value="">-- Pilih --</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>

        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            No. Telepon Ortu
          </label>
          <input type="text" id="editTelp"
            name="no_telepon" maxlength="15"
            oninput="this.value=
              this.value.replace(/[^0-9\+\-]/g,'')"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:8px 12px;
                   height:36px; box-sizing:border-box;
                   outline:none;">
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em;
                      margin-bottom:4px;">
          Alamat
        </label>
        <textarea id="editAlamat" name="alamat"
          rows="2"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb;
                 border-radius:8px; padding:8px 12px;
                 resize:none; box-sizing:border-box;
                 outline:none;"></textarea>
      </div>

      <div style="margin-bottom:20px;
                  display:flex; align-items:center;
                  gap:8px;">
        <input type="checkbox" id="editIsActive"
          name="is_active" value="1"
          style="width:16px; height:16px;
                 cursor:pointer; accent-color:#1D9E75;">
        <label for="editIsActive"
          style="font-size:13px; color:#374151;
                 cursor:pointer;">
          Siswa masih aktif
        </label>
      </div>

      <div style="display:flex; gap:8px;
                  padding-top:16px;
                  border-top:1px solid #f3f4f6;">
        <button type="button" id="btnBatalEdit"
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
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

  var mTambah = document.getElementById('modalTambahSiswa');
  var mEdit   = document.getElementById('modalEditSiswa');

  function buka(el) {
    el.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  function tutup(el) {
    el.style.display = 'none';
    document.body.style.overflow = '';
  }

  // Tombol buka modal tambah (dari header)
  var btnTambah = document.getElementById('btnTambahSiswa');
  if (btnTambah) {
    btnTambah.addEventListener('click', function() {
      buka(mTambah);
    });
  }

  // Tombol buka modal tambah (dari tabel kosong)
  var btnTambahEmpty = document.getElementById('btnTambahSiswaEmpty');
  if (btnTambahEmpty) {
    btnTambahEmpty.addEventListener('click', function() {
      buka(mTambah);
    });
  }

  // Tombol tutup modal tambah
  document.getElementById('closeTambah')
    .addEventListener('click', function() {
      tutup(mTambah);
    });
  document.getElementById('btnBatalTambah')
    .addEventListener('click', function() {
      tutup(mTambah);
    });
  document.getElementById('backdropTambah')
    .addEventListener('click', function() {
      tutup(mTambah);
    });

  // Tombol tutup modal edit
  document.getElementById('closeEdit')
    .addEventListener('click', function() {
      tutup(mEdit);
    });
  document.getElementById('btnBatalEdit')
    .addEventListener('click', function() {
      tutup(mEdit);
    });
  document.getElementById('backdropEdit')
    .addEventListener('click', function() {
      tutup(mEdit);
    });

  // Tutup dengan ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      tutup(mTambah);
      tutup(mEdit);
    }
  });

  // Auto buka tambah jika ada error validasi atau dari menu
  @if($errors->any() || request('open') === 'tambah')
    buka(mTambah);
  @endif

  // Auto hide flash message 4 detik
  var alert = document.getElementById('alertSuccess');
  if (alert) {
    setTimeout(function() {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(function() { alert.remove(); }, 500);
    }, 4000);
  }

  // Client-side validation for tambah siswa form
  var formTambah = document.getElementById('formTambahSiswa');
  if (formTambah) {
    formTambah.addEventListener('submit', function(e) {
      var nik = formTambah.querySelector('input[name="nik"]').value.trim();
      var kelas = formTambah.querySelector('select[name="kelas"]').value.trim();
      var nama = formTambah.querySelector('input[name="nama"]').value.trim();
      var namaOrtu = formTambah.querySelector('input[name="nama_orangtua"]').value.trim();
      var messages = [];

      if (nik.length !== 16) messages.push('NIK harus 16 digit.');
      if (!kelas) messages.push('Kelas harus dipilih.');
      if (!nama) messages.push('Nama siswa wajib diisi.');
      if (!namaOrtu) messages.push('Nama orang tua wajib diisi.');

      if (messages.length > 0) {
        e.preventDefault();
        alert(messages.join('\n'));
        return false;
      }
    });
  }

  // Client-side validation for edit siswa form
  var formEdit = document.getElementById('formEditSiswa');
  if (formEdit) {
    formEdit.addEventListener('submit', function(e) {
      var nik = formEdit.querySelector('input[name="nik"]').value.trim();
      var kelas = formEdit.querySelector('select[name="kelas"]').value.trim();
      var nama = formEdit.querySelector('input[name="nama"]').value.trim();
      var namaOrtu = formEdit.querySelector('input[name="nama_orangtua"]').value.trim();
      var messages = [];

      if (nik.length !== 16) messages.push('NIK harus 16 digit.');
      if (!kelas) messages.push('Kelas harus dipilih.');
      if (!nama) messages.push('Nama siswa wajib diisi.');
      if (!namaOrtu) messages.push('Nama orang tua wajib diisi.');

      if (messages.length > 0) {
        e.preventDefault();
        alert(messages.join('\n'));
        return false;
      }
    });
  }

  // ─── Click row untuk edit modal ──────────────
  var siswaRows = document.querySelectorAll('.siswa-row');
  siswaRows.forEach(function(row) {
    row.addEventListener('click', function(e) {
      // Jangan trigger jika klik tombol aksi
      if (e.target.closest('button') || 
          e.target.closest('.btn-edit-action')) {
        return;
      }
      
      var id = this.dataset.id;
      var nik = this.dataset.nik;
      var nama = this.dataset.nama;
      var namaOrangtua = this.dataset['nama-orangtua'];
      var kelas = this.dataset.kelas;
      var jk = this.dataset['jenis-kelamin'];
      var telp = this.dataset['no-telepon'];
      var alamat = this.dataset.alamat;
      var isActive = this.dataset['is-active'] === 'true';
      
      bukaModalEdit(id, nik, nama, namaOrangtua, 
                    kelas, jk, telp, alamat, isActive);
    });
  });

});

// Fungsi isi & buka modal edit (dipanggil dari tombol tabel)
function bukaModalEdit(id, nik, nama, namaOrtu,
  kelas, jk, telp, alamat, isActive) {

  var form = document.getElementById('formEditSiswa');
  form.action = '/admin/siswa/' + id;

  document.getElementById('editNik').value    = nik;
  document.getElementById('editNama').value   = nama;
  document.getElementById('editNamaOrtu').value = namaOrtu;
  document.getElementById('editTelp').value   = telp  || '';
  document.getElementById('editAlamat').value = alamat || '';
  document.getElementById('editIsActive').checked = isActive;

  var selK = document.getElementById('editKelas');
  for (var i = 0; i < selK.options.length; i++) {
    selK.options[i].selected =
      (selK.options[i].value === kelas);
  }

  var selJK = document.getElementById('editJK');
  for (var j = 0; j < selJK.options.length; j++) {
    selJK.options[j].selected =
      (selJK.options[j].value === jk);
  }

  var mEdit = document.getElementById('modalEditSiswa');
  mEdit.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

// Fungsi konfirmasi hapus
function konfirmasiHapus(id, nama) {
  if (confirm(
    'Hapus data siswa "' + nama + '"?\n\n' +
    'Data akan dihapus sementara.'
  )) {
    document.getElementById('formHapus' + id).submit();
  }
}
</script>
@endpush
