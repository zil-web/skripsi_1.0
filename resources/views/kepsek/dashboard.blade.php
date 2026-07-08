@extends('layouts.kepsek')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan keuangan bulan ini')

@section('content')
    @php
        $toValue = fn ($value) => $value instanceof \BackedEnum ? $value->value : $value;
        $formatRupiah = fn ($value) => rupiah((int) $value);
        $statusData = $status_transaksi_bulan_ini ?? ['approved' => 0, 'pending' => 0, 'rejected' => 0];
        $grafik = $data_grafik_6bulan ?? ['labels' => [], 'pemasukan' => [], 'pengeluaran' => []];
    @endphp

    <div class="space-y-6" x-data>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Pemasukan</p>
                        <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $formatRupiah($total_pemasukan_bulan ?? 0) }}</div>
                        <p class="mt-2 text-xs text-gray-400">Transaksi approved bulan ini</p>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5-5 4 4 5-5M5 19h14" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Pengeluaran</p>
                        <div class="mt-2 text-2xl font-bold text-rose-700">{{ $formatRupiah($total_pengeluaran_bulan ?? 0) }}</div>
                        <p class="mt-2 text-xs text-gray-400">Transaksi approved bulan ini</p>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12l-5 5-4-4-5 5M5 5h14" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Saldo Bersih</p>
                        <div class="mt-2 text-2xl font-bold text-sky-700">{{ $formatRupiah($saldo_bersih ?? 0) }}</div>
                        <p class="mt-2 text-xs text-gray-400">Pemasukan - pengeluaran</p>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M7 8l-4 4 4 4M17 8l4 4-4 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Pending</p>
                        <div class="mt-2 text-2xl font-bold text-amber-700">{{ $total_pending }}</div>
                        <p class="mt-2 text-xs text-gray-400">Harus divalidasi</p>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m-3-9a9 9 0 100 18 9 9 0 000-18z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Pemasukan vs Pengeluaran</h2>
                        <p class="text-sm text-gray-500">6 bulan terakhir</p>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="chartPemasukanPengeluaran"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Status Transaksi Bulan Ini</h2>
                        <p class="text-sm text-gray-500">Distribusi transaksi saat ini</p>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="chartStatusTransaksi"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-900">Ringkasan Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">Dashboard Kepala Sekolah tetap menampilkan ringkasan utama tanpa tabel validasi.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grafik = @json($grafik);
            const statusData = @json($statusData);

            const lineCanvas = document.getElementById('chartPemasukanPengeluaran');
            if (lineCanvas) {
                new Chart(lineCanvas, {
                    type: 'bar',
                    data: {
                        labels: grafik.labels,
                        datasets: [
                            { label: 'Pemasukan', data: grafik.pemasukan, backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 8 },
                            { label: 'Pengeluaran', data: grafik.pengeluaran, backgroundColor: 'rgba(244, 63, 94, 0.8)', borderRadius: 8 },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true, ticks: { callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } } },
                    },
                });
            }

            const donutCanvas = document.getElementById('chartStatusTransaksi');
            if (donutCanvas) {
                new Chart(donutCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Disetujui', 'Pending', 'Ditolak'],
                        datasets: [{
                            data: [statusData.approved ?? 0, statusData.pending ?? 0, statusData.rejected ?? 0],
                            backgroundColor: ['rgba(16, 185, 129, 0.9)', 'rgba(245, 158, 11, 0.9)', 'rgba(239, 68, 68, 0.9)'],
                            borderWidth: 0,
                        }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } },
                });
            }
        });
    </script>
@endsection