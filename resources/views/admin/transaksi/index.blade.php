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
                <a href="{{ route('admin.transaksi.export-csv', request()->query()) }}" class="bg-emerald-600 text-white text-xs rounded-lg px-4 h-9 inline-flex items-center hover:bg-emerald-700">Ekspor</a>
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
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Detail</th>
                                <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Edit</th>
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
                                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Tersedia</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        <button data-id="{{ $t->id }}" class="open-transaksi-detail text-[11px] px-3 py-1 rounded-lg border font-medium border-gray-200 text-gray-600 hover:bg-gray-50">Detail</button>
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        @if($t->pendingEditRequest)
                                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Menunggu Approval</span>
                                        @else
                                            <button
                                                type="button"
                                                class="open-edit-request inline-flex items-center text-[11px] px-3 py-1 rounded-lg border font-medium border-[#1D9E75] text-[#1D9E75] hover:bg-emerald-50"
                                                data-id="{{ $t->id }}"
                                                data-old-jumlah="{{ (int) $t->jumlah }}"
                                                data-old-jenis="{{ $statusValue($t->jenis) }}"
                                                data-old-keterangan="{{ $t->keterangan }}"
                                            >
                                                Edit
                                            </button>
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

    @include('admin.components.detail-transaksi-modal')

    <!-- Edit Request Modal -->
    <div id="edit-request-modal" class="fixed inset-0 hidden items-start justify-center z-[70] overflow-y-auto px-4 py-6 bg-black/50">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-[12px] shadow-xl w-full max-w-[460px] mx-auto overflow-hidden flex flex-col max-h-[calc(100vh-48px)]">
            <div class="px-5 py-4 flex items-center justify-between bg-[#10b981] text-white flex-shrink-0">
                <h3 class="font-semibold text-white">Ajukan Perubahan Transaksi</h3>
                <button id="edit-request-close" class="text-white bg-none border-none text-[20px] leading-none cursor-pointer">✕</button>
            </div>
            <form id="edit-request-form" method="POST" class="p-5 space-y-5 overflow-y-auto flex-1 -webkit-overflow-scrolling-touch">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 border border-gray-100 rounded-lg p-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jumlah Saat Ini</label>
                        <input id="er-old-jumlah" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jenis Saat Ini</label>
                        <input id="er-old-jenis" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Keterangan Saat Ini</label>
                        <textarea id="er-old-keterangan" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100" readonly></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="er-new-jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Baru <span class="text-red-500">*</span></label>
                        <input id="er-new-jumlah" type="number" name="new_jumlah" min="1" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1D9E75]">
                    </div>
                    <div>
                        <label for="er-new-jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Baru <span class="text-red-500">*</span></label>
                        <select id="er-new-jenis" name="new_jenis" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1D9E75]">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="er-new-keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Baru</label>
                        <textarea id="er-new-keterangan" name="new_keterangan" rows="4" maxlength="500" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1D9E75]"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" id="edit-request-cancel" class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-[#10b981] text-white hover:bg-[#0f9d6f]">Kirim Permintaan Edit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function(){
        function el(q){return document.querySelector(q)}
        function els(q){return Array.from(document.querySelectorAll(q))}

        const editRequestModal = el('#edit-request-modal');
        const editRequestForm = el('#edit-request-form');
        const editRequestClose = el('#edit-request-close');
        const editRequestCancel = el('#edit-request-cancel');
        const editRequestActionTemplate = `{{ url('/transaksi/__ID__/edit-request') }}`;

        function formatRupiah(v){ return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits:0 }).format(Number(v||0)); }

        function openEditRequestModal(){ editRequestModal.classList.remove('hidden'); editRequestModal.classList.add('flex'); document.body.style.overflow = 'hidden'; }
        function closeEditRequestModal(){ editRequestModal.classList.add('hidden'); editRequestModal.classList.remove('flex'); document.body.style.overflow = ''; }

        els('.open-transaksi-detail').forEach(btn => {
            btn.addEventListener('click', async function(){
                const id = this.dataset.id;

                try {
                    const res = await fetch(`{{ url('/admin/transaksi') }}/${id}/detail`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!res.ok) {
                        throw new Error('Gagal mengambil data');
                    }

                    const data = await res.json();
                    const fields = [
                        { label: 'ID Transaksi', value: data.id ?? id },
                        { label: 'Tanggal', value: data.tanggal ?? '-' },
                        { label: 'Jumlah', value: formatRupiah(data.jumlah), isPeso: true },
                        { label: 'Jenis', value: data.jenis_transaksi ?? data.jenis ?? '-' },
                        { label: 'Keterangan', value: data.keterangan || '-' },
                        { label: 'Status', value: data.status ?? '-' }
                    ];

                    if ((data.jenis_transaksi ?? data.jenis) === 'pemasukan' && data.siswa) {
                        const siswaNama = data.siswa.nama ?? 'N/A';
                        const siswaNik = data.siswa.nik ?? 'N/A';
                        const siswaKelas = data.siswa.kelas ?? '';
                        fields.push({
                            label: 'Siswa',
                            value: `${siswaNama} (NIK: ${siswaNik}${siswaKelas ? `, Kelas: ${siswaKelas}` : ''})`
                        });
                    }

                    openDetailTransaksi(fields, data.bukti_raw || data.bukti_url || data.bukti_transaksi || '');
                } catch (error) {
                    alert('Gagal memuat detail: ' + error.message);
                }
            });
        });

        els('.open-edit-request').forEach(btn => {
            btn.addEventListener('click', function(){
                const id = this.dataset.id;
                const oldJumlah = this.dataset.oldJumlah ?? '0';
                const oldJenis = this.dataset.oldJenis ?? '-';
                const oldKeterangan = this.dataset.oldKeterangan ?? '';

                editRequestForm.action = editRequestActionTemplate.replace('__ID__', id);
                el('#er-old-jumlah').value = formatRupiah(oldJumlah);
                el('#er-old-jenis').value = oldJenis;
                el('#er-old-keterangan').value = oldKeterangan;

                el('#er-new-jumlah').value = Number(oldJumlah || 0);
                el('#er-new-jenis').value = oldJenis;
                el('#er-new-keterangan').value = oldKeterangan;

                openEditRequestModal();
            });
        });

        // Edit request modal close handlers
        editRequestClose.addEventListener('click', closeEditRequestModal);
        editRequestCancel.addEventListener('click', closeEditRequestModal);
        editRequestModal.addEventListener('click', function(e){ if(e.target === editRequestModal) closeEditRequestModal(); });

        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && editRequestModal && !editRequestModal.classList.contains('hidden')) {
                closeEditRequestModal();
            }
        });
    })();
</script>
@endpush