@extends('layouts.admin')

@section('page-title', 'Pengeluaran')
@section('page-subtitle', 'Kelola semua data pengeluaran sekolah')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Dashboard</a>
    <a href="{{ route('admin.pemasukan.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Pemasukan</a>
    <a href="{{ route('admin.pengeluaran.index') }}" class="block px-3 py-2 rounded mb-1 bg-[var(--accent)] text-white">Pengeluaran</a>
    <a href="{{ route('admin.transaksi.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Transaksi</a>
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
        $formatRupiah = fn ($value) => rupiah((int) $value);
        $query = \App\Models\Transaksi::query()->where('jenis', 'pengeluaran')->where('id_admin', auth()->id());
        if (request()->filled('tanggal_dari') && request()->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [request('tanggal_dari'), request('tanggal_sampai')]);
        }
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('search')) {
            $query->where('keterangan', 'like', '%' . request('search') . '%');
        }
        $statsTotal = (clone $query)->sum('jumlah');
        $statsPending = (clone $query)->where('status', 'pending')->count();
        $statsApproved = (clone $query)->where('status', 'approved')->count();
        $statsRejected = (clone $query)->where('status', 'rejected')->count();
    @endphp

    <div class="space-y-4" x-data="{ showFlash: true }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-base font-medium text-gray-800">Pengeluaran</h1>
                <p class="text-xs text-gray-400 mt-0.5">Kelola semua data pengeluaran sekolah</p>
            </div>
            <a href="{{ route('admin.pengeluaran.create') }}" class="inline-flex items-center gap-2 bg-[var(--accent)] text-white rounded-lg px-4 py-2 text-sm font-medium">
                <span>+</span>
                <span>Tambah Pengeluaran</span>
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
                        <div class="text-xs text-gray-400">Total Pengeluaran bulan ini</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($statsTotal) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Dari transaksi yang sudah disetujui</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12l-5 5-4-4-5 5" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Menunggu Approval</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $statsPending }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi pending</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m-3-9a9 9 0 100 18 9 9 0 000-18z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Sudah Disetujui</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $statsApproved }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi approved</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Ditolak</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $statsRejected }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi rejected</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 mb-4">
            <form method="GET" action="{{ route('admin.pengeluaran.index') }}" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
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
                    <label class="block text-sm text-gray-600 mb-1">Keterangan</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan..." class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-[#1D9E75] h-9">
                </div>
                <button type="submit" class="bg-[#1D9E75] text-white text-xs rounded-lg px-4 h-9">Filter</button>
                <a href="{{ route('admin.pengeluaran.index') }}" class="bg-white border border-gray-200 text-xs rounded-lg px-4 h-9 inline-flex items-center">Reset</a>
            </form>
        </div>

        @if($pengeluarans->count() > 0)
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">No</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Tanggal</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Keterangan</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Siswa</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Jumlah</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Bukti</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Status</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengeluarans as $index => $pengeluaran)
                                @php $jenis = $statusValue($pengeluaran->jenis); @endphp
                                <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0">
                                    <td class="text-sm px-4 py-2.5">{{ ($pengeluarans->currentPage() - 1) * $pengeluarans->perPage() + $index + 1 }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ optional($pengeluaran->tanggal)->format('d/m/Y') }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ $pengeluaran->keterangan }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ $pengeluaran->siswa?->nama ?? '-' }}</td>
                                    <td class="text-sm px-4 py-2.5 font-medium text-red-500">{{ $formatRupiah($pengeluaran->jumlah) }}</td>
                                    <td class="text-sm px-4 py-2.5">
                                        @if($pengeluaran->bukti_transaksi)
                                            <a href="{{ Storage::url($pengeluaran->bukti_transaksi) }}" target="_blank" class="text-[11px] px-3 py-1 rounded-lg border font-medium border-gray-200 text-gray-600 hover:bg-gray-50">Detail</a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass($pengeluaran->status) }}">{{ $statusLabel($pengeluaran->status) }}</span>
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
                    {{ $pengeluarans->withQueryString()->links() }}
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-sm text-gray-400 mb-2">Tidak ada data pengeluaran</h3>
                <a href="{{ route('admin.pengeluaran.create') }}" class="inline-flex items-center gap-2 bg-[var(--accent)] text-white rounded-lg px-4 py-2 text-sm font-medium">+ Tambah Pengeluaran</a>
            </div>
        @endif
    </div>
@endsection