<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h3>
        <a href="{{ route('admin.transaksi.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Tanggal</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keterangan</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Siswa</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Jenis</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Jumlah</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($transaksi_terbaru as $t)
                    @php
                        $jenis = is_object($t->jenis) ? $t->jenis->value : $t->jenis;
                        $status = is_object($t->status) ? $t->status->value : $t->status;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($t->tanggal)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ Str::limit($t->keterangan, 50) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            @if($t->siswa)
                                <div>{{ $t->siswa->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $t->siswa->kelas }}</div>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($jenis === 'pemasukan')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Pemasukan</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Pengeluaran</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ rupiah($t->jumlah) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @switch($status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @break
                                @case('approved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $status }}</span>
                            @endswitch
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada transaksi terbaru</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
