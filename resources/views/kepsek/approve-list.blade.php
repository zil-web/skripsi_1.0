@extends('layouts.kepsek')

@section('page-title', 'Approval')
@section('page-subtitle', 'Persetujuan permintaan edit transaksi dan pemasukan')

@section('content')
    @php
        $toValue = fn ($value) => $value instanceof \BackedEnum ? $value->value : $value;
        $formatRupiah = fn ($value) => rupiah((int) $value);
    @endphp

    <div class="space-y-6">
        <section class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Permintaan Edit Transaksi</h2>
                <p class="text-xs text-gray-500 mt-1">Daftar perubahan transaksi yang diajukan bendahara.</p>
            </div>

            @if($editRequests->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">No</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Tanggal</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Diajukan oleh</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Perubahan</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($editRequests as $i => $request)
                            <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0 align-top">
                                <td class="text-sm px-4 py-3">{{ $i + 1 }}</td>
                                <td class="text-sm px-4 py-3">{{ optional($request->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-sm px-4 py-3">{{ $request->requestedBy->name ?? '-' }}</td>
                                <td class="text-sm px-4 py-3 text-gray-700">
                                    <div><span class="text-gray-500">Jumlah:</span> {{ $formatRupiah($request->old_jumlah) }} <span class="text-gray-400">→</span> {{ $formatRupiah($request->new_jumlah) }}</div>
                                    <div class="mt-1"><span class="text-gray-500">Jenis:</span> {{ ucfirst((string) $request->old_jenis) }} <span class="text-gray-400">→</span> {{ ucfirst((string) $request->new_jenis) }}</div>
                                    <div class="mt-1"><span class="text-gray-500">Keterangan:</span> {{ $request->old_keterangan ?? '-' }} <span class="text-gray-400">→</span> {{ $request->new_keterangan ?? '-' }}</div>
                                </td>
                                <td class="text-sm px-4 py-3">
                                    <div class="flex flex-col gap-2 min-w-48">
                                        <form method="POST" action="{{ route('kepsek.edit-request.approve', $request->id) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-[#1D9E75] bg-[#1D9E75] text-white hover:bg-[#188864]">Setujui</button>
                                        </form>

                                        <details>
                                            <summary class="cursor-pointer text-center text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-200 text-red-600 hover:bg-red-50">Tolak</summary>
                                            <form method="POST" action="{{ route('kepsek.edit-request.reject', $request->id) }}" class="mt-2 space-y-2">
                                                @csrf
                                                <textarea name="catatan_kepsek" rows="2" maxlength="300" class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs" placeholder="Catatan penolakan (opsional)">{{ old('catatan_kepsek') }}</textarea>
                                                <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-300 text-red-700 hover:bg-red-50">Kirim Penolakan</button>
                                            </form>
                                        </details>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center text-sm text-gray-500">Tidak ada permintaan edit</div>
            @endif
        </section>

        <section class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Approval Pengeluaran</h2>
                <p class="text-xs text-gray-500 mt-1">Daftar transaksi pengeluaran yang menunggu persetujuan.</p>
            </div>

            @if($pengeluaran->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-gray-500">Tidak ada pengeluaran yang menunggu</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">No</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Tanggal</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Jumlah</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Jenis</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Keterangan</th>
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pengeluaran as $i => $item)
                            <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0 align-top">
                                <td class="text-sm px-4 py-3">{{ $i + 1 }}</td>
                                <td class="text-sm px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="text-sm px-4 py-3 font-medium text-emerald-700">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                <td class="text-sm px-4 py-3">{{ $item->jenis ?? '-' }}</td>
                                <td class="text-sm px-4 py-3">{{ Str::limit($item->keterangan, 40) }}</td>
                                <td class="text-sm px-4 py-3">
                                    <div class="flex flex-col gap-2 min-w-48">
                                        <form method="POST" action="{{ route('kepsek.pengeluaran.approve', $item->id) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-[#1D9E75] bg-[#1D9E75] text-white hover:bg-[#188864]">Setujui</button>
                                        </form>

                                        <details>
                                            <summary class="cursor-pointer text-center text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-200 text-red-600 hover:bg-red-50">Tolak</summary>
                                            <form method="POST" action="{{ route('kepsek.pengeluaran.reject', $item->id) }}" class="mt-2 space-y-2">
                                                @csrf
                                                <textarea name="catatan_kepsek" rows="2" maxlength="300" class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs" placeholder="Catatan penolakan (opsional)">{{ old('catatan_kepsek') }}</textarea>
                                                <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-300 text-red-700 hover:bg-red-50">Kirim Penolakan</button>
                                            </form>
                                        </details>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
