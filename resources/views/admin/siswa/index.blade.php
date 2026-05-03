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
  <button onclick="bukaModal('tambah')"
    style="display:flex; align-items:center; gap:6px;
           background:#1D9E75; color:white;
           border:none; border-radius:8px;
           padding:8px 16px; font-size:13px;
           font-weight:500; cursor:pointer;">
    + Tambah Siswa
  </button>
</div>

{{-- STAT CARDS --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr);
            gap:12px; margin-bottom:16px;">

  <div style="background:white; border:1px solid #f3f4f6;
              border-radius:12px; padding:16px;">
    <div style="display:flex; align-items:center;
                justify-content:space-between; 
                margin-bottom:10px;">
      <div style="background:#eff6ff; border-radius:8px;
                  width:32px; height:32px; display:flex;
                  align-items:center; justify-content:center;">
        <svg width="16" height="16" viewBox="0 0 24 24"
          fill="none" stroke="#2563eb" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5
                   a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <span style="font-size:10px; font-weight:500;
                   background:#eff6ff; color:#2563eb;
                   padding:2px 8px; border-radius:10px;">
        Total
      </span>
    </div>
    <div style="font-size:22px; font-weight:500;
                color:#1f2937;">
      {{ $total_siswa }}
    </div>
    <div style="font-size:11px; color:#9ca3af; margin-top:2px;">
      Total Siswa
    </div>
  </div>

  <div style="background:white; border:1px solid #f3f4f6;
              border-radius:12px; padding:16px;">
    <div style="display:flex; align-items:center;
                justify-content:space-between;
                margin-bottom:10px;">
      <div style="background:#f0fdf4; border-radius:8px;
                  width:32px; height:32px; display:flex;
                  align-items:center; justify-content:center;">
        <svg width="16" height="16" viewBox="0 0 24 24"
          fill="none" stroke="#16a34a" stroke-width="2">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <span style="font-size:10px; font-weight:500;
                   background:#f0fdf4; color:#16a34a;
                   padding:2px 8px; border-radius:10px;">
        Aktif
      </span>
    </div>
    <div style="font-size:22px; font-weight:500;
                color:#1f2937;">
      {{ $total_aktif }}
    </div>
    <div style="font-size:11px; color:#9ca3af; margin-top:2px;">
      Siswa Aktif
    </div>
  </div>

  <div style="background:white; border:1px solid #f3f4f6;
              border-radius:12px; padding:16px;">
    <div style="display:flex; align-items:center;
                justify-content:space-between;
                margin-bottom:10px;">
      <div style="background:#f9fafb; border-radius:8px;
                  width:32px; height:32px; display:flex;
                  align-items:center; justify-content:center;">
        <svg width="16" height="16" viewBox="0 0 24 24"
          fill="none" stroke="#6b7280" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/>
          <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
      </div>
      <span style="font-size:10px; font-weight:500;
                   background:#f9fafb; color:#6b7280;
                   padding:2px 8px; border-radius:10px;">
        Non-Aktif
      </span>
    </div>
    <div style="font-size:22px; font-weight:500;
                color:#1f2937;">
      {{ $total_nonaktif }}
    </div>
    <div style="font-size:11px; color:#9ca3af; margin-top:2px;">
      Siswa Non-Aktif
    </div>
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
      <tr style="border-bottom:1px solid #f9fafb;"
          onmouseover="this.style.background='#fafafa'"
          onmouseout="this.style.background='white'">

        <td style="font-size:12px; color:#9ca3af;
                   padding:10px 16px;">
          {{ $siswas->firstItem() + $index }}
        </td>

        <td style="font-size:12px; color:#1f2937;
                   padding:10px 16px; font-family:monospace;">
          {{ $siswa->nik }}
        </td>

        <td style="padding:10px 16px;">
          <div style="font-size:13px; font-weight:500;
                      color:#1f2937;">
            {{ $siswa->nama }}
          </div>
          <div style="font-size:11px; color:#9ca3af;">
            {{ $siswa->jenis_kelamin_label }}
          </div>
        </td>

        <td style="font-size:12px; color:#6b7280;
                   padding:10px 16px;">
          {{ $siswa->nama_orangtua }}
        </td>

        <td style="padding:10px 16px;">
          <span style="font-size:11px; font-weight:500;
                       background:#eff6ff; color:#2563eb;
                       padding:2px 10px; border-radius:10px;">
            {{ $siswa->kelas }}
          </span>
        </td>

        <td style="font-size:12px; color:#6b7280;
                   padding:10px 16px;">
          {{ $siswa->no_telepon ?? '—' }}
        </td>

        <td style="padding:10px 16px;">
          @if($siswa->is_active)
            <span style="font-size:11px; font-weight:500;
                         background:#f0fdf4; color:#16a34a;
                         padding:2px 10px; border-radius:10px;">
              Aktif
            </span>
          @else
            <span style="font-size:11px; font-weight:500;
                         background:#f9fafb; color:#6b7280;
                         padding:2px 10px; border-radius:10px;">
              Non-Aktif
            </span>
          @endif
        </td>

        <td style="padding:10px 16px;">
          <div style="display:flex; gap:6px;">

            {{-- Tombol Edit --}}
            <button
              onclick="bukaModalEdit(
                '{{ $siswa->id }}',
                '{{ $siswa->nik }}',
                '{{ $siswa->nama }}',
                '{{ $siswa->nama_orangtua }}',
                '{{ $siswa->kelas }}',
                '{{ $siswa->jenis_kelamin }}',
                '{{ $siswa->no_telepon }}',
                '{{ $siswa->alamat }}',
                {{ $siswa->is_active ? 'true' : 'false' }}
              )"
              style="font-size:11px; padding:4px 12px;
                     border-radius:6px; cursor:pointer;
                     border:1px solid #e5e7eb;
                     background:white; color:#374151;">
              Edit
            </button>

            {{-- Tombol Hapus --}}
            <button
              onclick="konfirmasiHapus(
                '{{ $siswa->id }}',
                '{{ $siswa->nama }}'
              )"
              style="font-size:11px; padding:4px 12px;
                     border-radius:6px; cursor:pointer;
                     border:1px solid #fecaca;
                     background:#fff5f5; color:#dc2626;">
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
                                padding:40px 16px;">
          <div style="color:#9ca3af; font-size:13px;">
            Belum ada data siswa
          </div>
          <button onclick="bukaModal('tambah')"
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
  <div style="padding:12px 16px; 
              border-top:1px solid #f9fafb;">
    {{ $siswas->withQueryString()->links() }}
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

  <div onclick="tutupModal('tambah')"
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
          Tambah Data Siswa
        </p>
        <p style="font-size:11px; color:#9ca3af;
                  margin:4px 0 0;">
          Isi semua kolom yang wajib diisi (*)
        </p>
      </div>
      <button onclick="tutupModal('tambah')"
        style="width:28px; height:28px; border:none;
               background:#f9fafb; border-radius:8px;
               cursor:pointer; font-size:18px;
               color:#6b7280; line-height:1;">
        &times;
      </button>
    </div>

    <form method="POST"
      action="{{ route('admin.siswa.store') }}"
      style="padding:20px 24px;">
      @csrf

      {{-- NIK + Kelas (2 kolom) --}}
      <div style="display:grid;
                  grid-template-columns:1fr 1fr;
                  gap:14px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            NIK <span style="color:#ef4444;">*</span>
            <span style="text-transform:none;
                         color:#9ca3af;">(16 digit)</span>
          </label>
          <input type="text" name="nik"
            value="{{ old('nik') }}"
            maxlength="16"
            placeholder="3271XXXXXXXXXXXX"
            oninput="this.value=this.value.replace(/\D/g,'')"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:8px 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; font-family:monospace;">
          @error('nik')
            <p style="font-size:11px; color:#ef4444;
                      margin:4px 0 0;">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em;
                        margin-bottom:4px;">
            Kelas <span style="color:#ef4444;">*</span>
          </label>
          <select name="kelas"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb;
                   border-radius:8px; padding:0 12px;
                   height:36px; box-sizing:border-box;
                   outline:none; background:white;">
            <option value="">-- Pilih Kelas --</option>
            @foreach(['7A','7B','7C','8A','8B','8C','9A','9B','9C'] as $k)
              <option value="{{ $k }}"
                {{ old('kelas') == $k ? 'selected' : '' }}>
                Kelas {{ $k }}
              </option>
            @endforeach
          </select>
          @error('kelas')
            <p style="font-size:11px; color:#ef4444;
                      margin:4px 0 0;">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Nama Siswa --}}
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em; margin-bottom:4px;">
          Nama Siswa <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="nama"
          value="{{ old('nama') }}"
          placeholder="Nama lengkap siswa"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb; border-radius:8px;
                 padding:8px 12px; height:36px;
                 box-sizing:border-box; outline:none;">
        @error('nama')
          <p style="font-size:11px; color:#ef4444;
                    margin:4px 0 0;">{{ $message }}</p>
        @enderror
      </div>

      {{-- Nama Orang Tua --}}
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em; margin-bottom:4px;">
          Nama Orang Tua <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="nama_orangtua"
          value="{{ old('nama_orangtua') }}"
          placeholder="Nama lengkap orang tua / wali"
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb; border-radius:8px;
                 padding:8px 12px; height:36px;
                 box-sizing:border-box; outline:none;">
        @error('nama_orangtua')
          <p style="font-size:11px; color:#ef4444;
                    margin:4px 0 0;">{{ $message }}</p>
        @enderror
      </div>

      {{-- Jenis Kelamin + No Telepon (2 kolom) --}}
      <div style="display:grid;
                  grid-template-columns:1fr 1fr;
                  gap:14px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em; margin-bottom:4px;">
            Jenis Kelamin
          </label>
          <select name="jenis_kelamin"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb; border-radius:8px;
                   padding:0 12px; height:36px;
                   box-sizing:border-box; outline:none;
                   background:white;">
            <option value="">-- Pilih --</option>
            <option value="L"
              {{ old('jenis_kelamin')=='L' ? 'selected':'' }}>
              Laki-laki
            </option>
            <option value="P"
              {{ old('jenis_kelamin')=='P' ? 'selected':'' }}>
              Perempuan
            </option>
          </select>
        </div>

        <div>
          <label style="display:block; font-size:11px;
                        font-weight:500; color:#6b7280;
                        text-transform:uppercase;
                        letter-spacing:0.05em; margin-bottom:4px;">
            No. Telepon Orang Tua
          </label>
          <input type="text" name="no_telepon"
            value="{{ old('no_telepon') }}"
            maxlength="15" placeholder="08xxxxxxxxxx"
            oninput="this.value=this.value.replace(/[^0-9\+\-]/g,'')"
            style="width:100%; font-size:13px;
                   border:1px solid #e5e7eb; border-radius:8px;
                   padding:8px 12px; height:36px;
                   box-sizing:border-box; outline:none;">
          @error('no_telepon')
            <p style="font-size:11px; color:#ef4444;
                      margin:4px 0 0;">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Alamat --}}
      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:11px;
                      font-weight:500; color:#6b7280;
                      text-transform:uppercase;
                      letter-spacing:0.05em; margin-bottom:4px;">
          Alamat
          <span style="text-transform:none; color:#9ca3af;">
            (opsional)
          </span>
        </label>
        <textarea name="alamat" rows="2"
          placeholder="Alamat lengkap siswa..."
          style="width:100%; font-size:13px;
                 border:1px solid #e5e7eb; border-radius:8px;
                 padding:8px 12px; resize:none;
                 box-sizing:border-box; outline:none;">{{ old('alamat') }}</textarea>
      </div>

      {{-- Footer Tombol --}}
      <div style="display:flex; gap:8px; padding-top:16px;
                  border-top:1px solid #f3f4f6;">
        <button type="button"
          onclick="tutupModal('tambah')"
          style="flex:1; font-size:13px;
                 border:1px solid #e5e7eb; background:white;
                 color:#6b7280; border-radius:8px;
                 padding:9px; cursor:pointer;">
          Batal
        </button>
        <button type="submit"
          style="flex:1; font-size:13px; background:#1D9E75;
                 color:white; border:none; border-radius:8px;
                 padding:9px; cursor:pointer; font-weight:500;">
          Simpan Data Siswa
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
