@extends('layouts.admin')

@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Cari dan filter semua transaksi pemasukan & pengeluaran berdasarkan nominal')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Dashboard</a>
    <a href="{{ route('admin.pemasukan.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Pemasukan</a>
    <a href="{{ route('admin.pengeluaran.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Pengeluaran</a>
    <a href="{{ route('admin.transaksi.index') }}" class="block px-3 py-2 rounded mb-1 bg-[var(--accent)] text-white">Transaksi</a>
@endsection

@section('content')
    @php
        $statusValue = fn ($value) => $value instanceof \BackedEnum ? $value->value : $value;
        $statusLabel = fn ($value) => ucfirst($statusValue($value));
        $statusClass = fn ($value) => match ($statusValue($value)) {
            'pending' => 'bg-amber-50 text-amber-700',
            'approved' => 'bg-emerald-50 text-emerald-700',
            'rejected' => 'bg-red-50 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
        $jenisClass = fn ($value) => match ($statusValue($value)) {
            'pemasukan' => 'bg-emerald-50 text-emerald-700',
            'pengeluaran' => 'bg-red-50 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
        $formatRupiah = fn ($value) => rupiah((int) $value);
    @endphp

    <div class="space-y-4" x-data="{ showFlash: true }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-base font-medium text-gray-800">Riwayat Transaksi</h1>
                <p class="text-xs text-gray-400 mt-0.5">Cari dan filter semua transaksi pemasukan & pengeluaran</p>
            </div>
        </div>

        @if(session('success'))
            <div x-show="showFlash" x-init="setTimeout(() => showFlash = false, 4000)" class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div x-show="showFlash" x-init="setTimeout(() => showFlash = false, 4000)" class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-4 gap-3 mb-4">
            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Total Pemasukan</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($total_pemasukan) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi approved</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5-5 4 4 5-5M5 19h14" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Total Pengeluaran</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($total_pengeluaran) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi approved</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12l-5 5-4-4-5 5" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Saldo Bersih</div>
                        <div class="mt-1 text-xl font-medium {{ $saldo_bersih >= 0 ? 'text-blue-800' : 'text-red-600' }}">{{ $formatRupiah($saldo_bersih) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Pemasukan - pengeluaran</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 {{ $saldo_bersih >= 0 ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M7 8l-4 4 4 4M17 8l4 4-4 4" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Total Transaksi</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $total_count }}</div>
                        <div class="text-xs text-gray-400 mt-1">Jumlah data</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-gray-100 text-gray-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 mb-4">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jenis</label>
                    <select name="jenis" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9 min-w-32">
                        <option value="">Semua Jenis</option>
                        <option value="pemasukan" {{ request('jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Status</label>
                    <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9 min-w-32">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nominal</label>
                    <input type="text" name="nominal" value="{{ request('nominal') }}" placeholder="Contoh: 1000000" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nominal Minimum</label>
                    <input type="text" name="nominal_min" value="{{ request('nominal_min') }}" placeholder="Min" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nominal Maksimum</label>
                    <input type="text" name="nominal_max" value="{{ request('nominal_max') }}" placeholder="Max" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <button type="submit" class="bg-[#1D9E75] text-white text-xs rounded-lg px-4 h-9">Filter</button>
                <a href="{{ route('admin.transaksi.index') }}" class="bg-white border border-gray-200 text-xs rounded-lg px-4 h-9 inline-flex items-center">Reset</a>
            </form>
        </div>

        @if($transaksis->count())
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">No</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Tanggal</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Keterangan</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Jenis</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Siswa</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Jumlah</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Status</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksis as $index => $t)
                                <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0">
                                    <td class="text-sm px-4 py-2.5">{{ $transaksis->firstItem() + $index }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ optional($t->tanggal)->format('d/m/Y') }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ $t->keterangan }}</td>
                                    <td class="text-sm px-4 py-2.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $jenisClass($t->jenis) }}">{{ $statusLabel($t->jenis) }}</span>
                                    </td>
                                    <td class="text-sm px-4 py-2.5">{{ $t->siswa?->nama ?? '-' }}</td>
                                    <td class="text-sm px-4 py-2.5 font-medium {{ $statusValue($t->jenis) === 'pengeluaran' ? 'text-red-500' : 'text-emerald-600' }}">
                                        {{ $statusValue($t->jenis) === 'pengeluaran' ? '- ' : '+ ' }}{{ $formatRupiah($t->jumlah) }}
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass($t->status) }}">{{ $statusLabel($t->status) }}</span>
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        @if($t->bukti_transaksi)
                                            <a href="{{ Storage::url($t->bukti_transaksi) }}" target="_blank" class="text-[11px] px-3 py-1 rounded-lg border font-medium border-gray-200 text-gray-600 hover:bg-gray-50">Lihat</a>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-50">
                    {{ $transaksis->withQueryString()->links() }}
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm text-gray-400">Belum ada transaksi</h3>
            </div>
        @endif
    </div>
@endsection