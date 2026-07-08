@extends('layouts.kepsek')

@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Cari dan saring semua transaksi pemasukan & pengeluaran berdasarkan nominal')

@push('styles')
<style>
.custom-flatpickr .flatpickr-monthDropdown-months {
    appearance: auto;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 2px 6px;
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
    cursor: pointer;
}
.custom-flatpickr .numInputWrapper input.numInput {
    appearance: auto;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 2px 6px;
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
    width: 60px;
}
.custom-flatpickr .flatpickr-day.selected {
    background: #1D9E75;
    border-color: #1D9E75;
}
.custom-flatpickr .flatpickr-day:hover {
    background: #e6f7f2;
}
</style>
@endpush

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
                <p class="text-xs text-gray-400 mt-0.5">Cari dan Saring semua transaksi pemasukan & pengeluaran</p>
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
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($total_pemasukan ?? 0) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi disetujui</div>
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
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($total_pengeluaran ?? 0) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi disetujui</div>
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
                        <div class="mt-1 text-xl font-medium {{ ($saldo_bersih ?? 0) >= 0 ? 'text-blue-800' : 'text-red-600' }}">{{ $formatRupiah($saldo_bersih ?? 0) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Pemasukan - pengeluaran</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 {{ ($saldo_bersih ?? 0) >= 0 ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M7 8l-4 4 4 4M17 8l4 4-4 4" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Total Transaksi</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $total_count ?? 0 }}</div>
                        <div class="text-xs text-gray-400 mt-1">Jumlah data</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-gray-100 text-gray-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl p-4 mb-4" x-data="{ showFilter: false }" @keydown.window.escape="showFilter = false">
            <form method="GET" class="space-y-4">
                <div class="flex flex-col gap-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <label class="sr-only" for="q">Cari transaksi</label>
                            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Cari transaksi..." class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full focus:ring-emerald-500">
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="min-w-0 max-w-[150px] relative">
                                <label class="sr-only" for="tanggal_dari">Dari Tanggal</label>
                                <input type="text" id="tanggal_dari" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="sr-only" aria-hidden="true">
                                <button type="button" id="btn_tanggal_dari" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full bg-white text-left">
                                    <div class="text-[10px] text-gray-500">Dari</div>
                                    <div id="btn_tanggal_dari_text" class="text-sm text-gray-800">
                                        {{ request('tanggal_dari') ? \Carbon\Carbon::parse(request('tanggal_dari'))->format('d M Y') : 'Pilih tanggal' }}
                                    </div>
                                </button>
                            </div>
                            <span class="text-sm text-gray-400">→</span>
                            <div class="min-w-0 max-w-[150px] relative">
                                <label class="sr-only" for="tanggal_sampai">Sampai Tanggal</label>
                                <input type="text" id="tanggal_sampai" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="sr-only" aria-hidden="true">
                                <button type="button" id="btn_tanggal_sampai" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full bg-white text-left">
                                    <div class="text-[10px] text-gray-500">Sampai</div>
                                    <div id="btn_tanggal_sampai_text" class="text-sm text-gray-800">
                                        {{ request('tanggal_sampai') ? \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d M Y') : 'Pilih tanggal' }}
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="showFilter = true" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-100 transition">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h10M4 18h7" /></svg>
                            Saring
                        </button>
                        <a href="{{ route('kepsek.transaksi') }}" class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">Ulang</a>
                        <a href="{{ route('kepsek.admin.transaksi.export-excel', request()->query()) }}" class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 transition">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><path d="M7 10l5 5 5-5" /><path d="M12 15V3" /></svg>
                            Excel
                        </a>
                    </div>
                </div>

                <div x-show="showFilter" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center overflow-auto p-4 sm:p-6">
                    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showFilter = false"></div>
                    <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[32px] border border-emerald-200 bg-white p-6 shadow-2xl">
                        <div class="flex items-center justify-between gap-3 mb-6">
                            <div class="flex items-center gap-3 text-sm font-semibold text-emerald-800">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h10M4 18h7" /></svg>
                                </span>
                                Saring Transaksi
                            </div>
                            <button type="button" @click="showFilter = false" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-700">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                Tutup
                            </button>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Jenis</label>
                                <select name="jenis" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full focus:ring-emerald-500">
                                    <option value="">Semua Jenis</option>
                                    <option value="pemasukan" {{ request('jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="pengeluaran" {{ request('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Status</label>
                                <select name="status" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full focus:ring-emerald-500">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Tertunda</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nominal</label>
                                <input type="text" name="nominal" value="{{ request('nominal') }}" placeholder="Contoh: 1000000" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full focus:ring-emerald-500">
                            </div>
                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Minimal</label>
                                    <input type="text" name="nominal_min" value="{{ request('nominal_min') }}" placeholder="Minimal" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Maksimal</label>
                                    <input type="text" name="nominal_max" value="{{ request('nominal_max') }}" placeholder="Maksimal" class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-full focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition">Terapkan</button>
                            <button type="button" @click="showFilter = false" class="inline-flex items-center justify-center rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">Batal</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="detailLoadMessage" class="hidden mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-500"></div>

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
                                        {{ $statusValue($t->jenis) === 'pengeluaran' ? '- ' : '+ ' }}{{ $t->format_uang }}
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
                                        <button data-id="{{ $t->id }}" class="open-transaksi-detail text-[11px] px-3 py-1 rounded-lg border font-medium border-gray-200 text-gray-600 hover:bg-gray-50">Rincian</button>
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
    {{-- include reusable detail transaksi modal (shared with admin) --}}
    @include('admin.components.detail-transaksi-modal')
@endsection

@push('scripts')
<script>
    (function(){
        function formatRupiah(v){
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(Number(v || 0));
        }

        function setDetailMessage(message) {
            const el = document.getElementById('detailLoadMessage');
            if (!el) return;

            el.textContent = message || '';
            el.classList.toggle('hidden', !message);
        }

        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.open-transaksi-detail');
            if (!btn) return;

            const id = btn.dataset.id;
            setDetailMessage('');

            try {
                const res = await fetch(`{{ url('/kepsek/transaksi') }}/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!res.ok) throw new Error('Gagal mengambil data');

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
                    const siswaNis = data.siswa.nis ?? 'N/A';
                    const siswaKelas = data.siswa.kelas ?? '';
                    fields.push({
                        label: 'Siswa',
                        value: `${siswaNama} (NIS: ${siswaNis}${siswaKelas ? `, Kelas: ${siswaKelas}` : ''})`
                    });
                }

                const buktiPath = data.bukti_url || data.bukti_raw || data.bukti_transaksi || '';

                if (typeof openDetailTransaksi === 'function') {
                    openDetailTransaksi(fields, buktiPath);
                } else {
                    setDetailMessage('Fungsi detail belum tersedia.');
                }
            } catch (err) {
                setDetailMessage('Gagal memuat detail: ' + err.message);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && typeof tutupDetailTransaksiModal === 'function') {
                tutupDetailTransaksiModal();
            }
        });

        function initTransaksiDatePickers(availableDates = null) {
            if (typeof flatpickr !== 'function') return;

            const dariEl = document.getElementById('tanggal_dari');
            const sampaiEl = document.getElementById('tanggal_sampai');
            if (!dariEl || !sampaiEl) return;

            let pickerSampai;

            const pickerDariOptions = {
                locale: 'id',
                dateFormat: 'Y-m-d',
                allowInput: false,
                disableMobile: true,
                clickOpens: true,
                onReady: function(_, __, fp) {
                    fp.calendarContainer.classList.add('custom-flatpickr');
                    const btn = document.getElementById('btn_tanggal_dari');
                    if (btn) btn.addEventListener('click', function(){ fp.open(); });
                },
                onChange: function(selectedDates, dateStr, instance) {
                    pickerSampai.set('minDate', dateStr);
                    if (selectedDates.length) pickerSampai.open();
                    const btnText = document.getElementById('btn_tanggal_dari_text');
                    if (btnText) btnText.textContent = selectedDates.length ? instance.formatDate(selectedDates[0], 'd M Y') : 'Pilih tanggal';
                    dariEl.value = dateStr || '';
                }
            };

            if (Array.isArray(availableDates)) {
                pickerDariOptions.enable = availableDates;
            }

            const pickerSampaiOptions = {
                locale: 'id',
                dateFormat: 'Y-m-d',
                allowInput: false,
                disableMobile: true,
                clickOpens: true,
                onReady: function(_, __, fp) {
                    fp.calendarContainer.classList.add('custom-flatpickr');
                    const btn = document.getElementById('btn_tanggal_sampai');
                    if (btn) btn.addEventListener('click', function(){ fp.open(); });
                },
                onChange: function(selectedDates, dateStr, instance) {
                    pickerDari.set('maxDate', dateStr);
                    const btnText = document.getElementById('btn_tanggal_sampai_text');
                    if (btnText) btnText.textContent = selectedDates.length ? instance.formatDate(selectedDates[0], 'd M Y') : 'Pilih tanggal';
                    sampaiEl.value = dateStr || '';
                }
            };

            if (Array.isArray(availableDates)) {
                pickerSampaiOptions.enable = availableDates;
            }

            const pickerDari = flatpickr(dariEl, pickerDariOptions);
            pickerSampai = flatpickr(sampaiEl, pickerSampaiOptions);

            // expose instances for external buttons
            try { window.kepsekPickerDari = pickerDari; window.kepsekPickerSampai = pickerSampai; } catch(e) {}

            @if(request('tanggal_dari'))
                pickerDari.setDate("{{ request('tanggal_dari') }}", false);
            @endif
            @if(request('tanggal_sampai'))
                pickerSampai.setDate("{{ request('tanggal_sampai') }}", false);
            @endif
        }

        const availableDatesRoute = {!! Route::has('kepsek.transaksi.available-dates') ? json_encode(route('kepsek.transaksi.available-dates')) : 'null' !!};

        function setupTransaksiDatePickers() {
            if (availableDatesRoute) {
                fetch(availableDatesRoute)
                    .then(res => res.json())
                    .then(data => initTransaksiDatePickers(Array.isArray(data) ? data : null))
                    .catch(() => initTransaksiDatePickers());
            } else {
                initTransaksiDatePickers();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupTransaksiDatePickers);
        } else {
            setupTransaksiDatePickers();
        }
    })();
</script>
@endpush
