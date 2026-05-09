@extends('layouts.admin')

@section('page-title', 'Ajukan Perubahan Transaksi')
@section('page-subtitle', 'Ajukan perubahan data transaksi untuk persetujuan Kepala Sekolah')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Dashboard</a>
    <a href="{{ route('admin.pemasukan.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Pemasukan</a>
    <a href="{{ route('admin.pengeluaran.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Pengeluaran</a>
    <a href="{{ route('admin.transaksi.index') }}" class="block px-3 py-2 rounded mb-1 bg-[var(--accent)] text-white">Transaksi</a>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-[12px] border border-gray-100 overflow-hidden shadow-sm">
            <div class="bg-[#1D9E75] px-6 py-4">
                <h1 class="text-white text-base font-semibold">Ajukan Perubahan Transaksi</h1>
                <p class="text-emerald-100 text-xs mt-1">Perubahan akan berlaku setelah disetujui Kepala Sekolah.</p>
            </div>

            <form action="{{ route('edit-request.store', $transaksi->id) }}" method="POST" class="p-6 space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">Data Saat Ini</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 border border-gray-100 rounded-lg p-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Jumlah</label>
                            <input type="text" value="{{ rupiah((int) $transaksi->jumlah) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Jenis</label>
                            <input type="text" value="{{ $transaksi->jenis }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100" readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Keterangan</label>
                            <textarea class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100" rows="3" readonly>{{ $transaksi->keterangan }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">Ajukan Nilai Baru</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Baru <span class="text-red-500">*</span></label>
                            <input id="new_jumlah" type="number" name="new_jumlah" min="1" value="{{ old('new_jumlah', (int) $transaksi->jumlah) }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1D9E75]">
                        </div>
                        <div>
                            <label for="new_jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Baru <span class="text-red-500">*</span></label>
                            <select id="new_jenis" name="new_jenis" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1D9E75]">
                                <option value="pemasukan" {{ old('new_jenis', (string) $transaksi->jenis) === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                <option value="pengeluaran" {{ old('new_jenis', (string) $transaksi->jenis) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="new_keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Baru</label>
                            <textarea id="new_keterangan" name="new_keterangan" rows="4" maxlength="500" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1D9E75]">{{ old('new_keterangan', $transaksi->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <a href="{{ route('admin.transaksi.index') }}" class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#1D9E75] text-white hover:bg-[#188864]">Kirim Permintaan Edit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
