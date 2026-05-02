{{--
    Dashboard statistic cards partial
    Expects variables: $total_pemasukan, $total_pengeluaran, $saldo_bersih, $total_pending
--}}

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Pemasukan -->
    <div class="rounded-xl shadow-sm p-6 bg-white flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Pemasukan</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ rupiah($total_pemasukan) }}</p>
        </div>
        <div class="flex items-center justify-center w-14 h-14 rounded-full" style="background-color:#E6F6F1;">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#1D9E75" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 17l6-6 4 4 8-8" />
            </svg>
        </div>
    </div>

    <!-- Total Pengeluaran -->
    <div class="rounded-xl shadow-sm p-6 bg-white flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Pengeluaran</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ rupiah($total_pengeluaran) }}</p>
        </div>
        <div class="flex items-center justify-center w-14 h-14 rounded-full" style="background-color:#FEEAEA;">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#D23A2A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15l-6-6-4 4-8-8" />
            </svg>
        </div>
    </div>

    <!-- Saldo Bersih -->
    <div class="rounded-xl shadow-sm p-6 bg-white flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 uppercase font-semibold">Saldo Bersih</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ rupiah($saldo_bersih) }}</p>
        </div>
        <div class="flex items-center justify-center w-14 h-14 rounded-full" style="background-color:#EAF3FF;">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h11" />
                <path d="M16 3v4" />
            </svg>
        </div>
    </div>

    <!-- Pending -->
    <div class="rounded-xl shadow-sm p-6 bg-white flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 uppercase font-semibold">Pending</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $total_pending }}</p>
        </div>
        <div class="flex items-center justify-center w-14 h-14 rounded-full" style="background-color:#FFFBEB;">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#B45309" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 8v4l2 2" />
                <circle cx="12" cy="12" r="9" />
            </svg>
        </div>
    </div>
</div>
