@extends('layouts.admin')

@section('page-title', 'Transaksi')
@section('page-subtitle', 'Daftar semua transaksi pemasukan dan pengeluaran')

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
        $totalPemasukan = $transaksis->where('jenis', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $transaksis->where('jenis', 'pengeluaran')->sum('jumlah');
        $totalPending = $transaksis->where('status', 'pending')->count();
        $saldoBersih = $totalPemasukan - $totalPengeluaran;
    @endphp

    <div class="space-y-4" x-data="{ showFlash: true }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-base font-medium text-gray-800">Transaksi</h1>
                <p class="text-xs text-gray-400 mt-0.5">Daftar semua transaksi pemasukan dan pengeluaran</p>
            </div>
            <a href="{{ route('admin.transaksi.create') }}" class="inline-flex items-center gap-2 bg-[var(--accent)] text-white rounded-lg px-4 py-2 text-sm font-medium">
                <span>+</span>
                <span>Tambah Pemasukan</span>
            </a>
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
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($totalPemasukan) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Semua pemasukan</div>
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
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($totalPengeluaran) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Semua pengeluaran</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12l-5 5-4-4-5 5" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Total Pending</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $totalPending }}</div>
                        <div class="text-xs text-gray-400 mt-1">Menunggu approval</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m-3-9a9 9 0 100 18 9 9 0 000-18z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Saldo Bersih</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($saldoBersih) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Pemasukan - pengeluaran</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M7 8l-4 4 4 4M17 8l4 4-4 4" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 mb-4">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tanggal Dari</label>
                    <input type="date" name="tanggal_from" value="{{ request('tanggal_from') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tanggal Sampai</label>
                    <input type="date" name="tanggal_to" value="{{ request('tanggal_to') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Status</label>
                    <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9 min-w-32">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Cari Keterangan</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik keterangan..." class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
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
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Jumlah</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Status</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksis as $index => $t)
                                @php $jenis = $statusValue($t->jenis); @endphp
                                <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0">
                                    <td class="text-sm px-4 py-2.5">{{ $transaksis->firstItem() + $index }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ optional($t->tanggal)->format('d/m/Y') }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ $t->keterangan }}</td>
                                    <td class="text-sm px-4 py-2.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $jenisClass($t->jenis) }}">{{ $statusLabel($t->jenis) }}</span>
                                    </td>
                                    <td class="text-sm px-4 py-2.5 font-medium {{ $jenis === 'pengeluaran' ? 'text-red-500' : 'text-emerald-600' }}">{{ $formatRupiah($t->jumlah) }}</td>
                                    <td class="text-sm px-4 py-2.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass($t->status) }}">{{ $statusLabel($t->status) }}</span>
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        <a href="#" class="text-[11px] px-3 py-1 rounded-lg border font-medium border-gray-200 text-gray-600 hover:bg-gray-50">Detail</a>
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
                <h3 class="text-sm text-gray-400 mb-2">Belum ada transaksi</h3>
                <a href="{{ route('admin.transaksi.create') }}" class="inline-flex items-center gap-2 bg-[var(--accent)] text-white rounded-lg px-4 py-2 text-sm font-medium">+ Tambah Pemasukan</a>
            </div>
        @endif
    </div>
@endsection