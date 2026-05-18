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
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Detail</th>
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
                                    <button
                                        type="button"
                                        class="detail-button inline-flex items-center justify-center w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-gray-200 text-gray-700 hover:bg-gray-50"
                                        data-title="Detail Permintaan Edit"
                                        data-subtitle="Permintaan edit transaksi dari bendahara"
                                        data-bukti="{{ $request->transaksi->bukti_transaksi ?? '' }}"
                                        data-points='{{ json_encode([['label' => 'Tanggal', 'value' => optional($request->created_at)->format('d/m/Y H:i')], ['label' => 'Diajukan oleh', 'value' => $request->requestedBy->name ?? '-'], ['label' => 'Jumlah lama', 'value' => $formatRupiah($request->old_jumlah)], ['label' => 'Jumlah baru', 'value' => $formatRupiah($request->new_jumlah)], ['label' => 'Jenis lama', 'value' => ucfirst((string) $request->old_jenis)], ['label' => 'Jenis baru', 'value' => ucfirst((string) $request->new_jenis)], ['label' => 'Keterangan lama', 'value' => $request->old_keterangan ?? '-'], ['label' => 'Keterangan baru', 'value' => $request->new_keterangan ?? '-']]) }}'
                                    >Detail</button>
                                </td>
                                <td class="text-sm px-4 py-3">
                                    <div class="flex flex-col gap-2 min-w-48">
                                        <form method="POST" action="{{ route('kepsek.edit-request.approve', $request->id) }}" onsubmit="return confirmApprove('edit transaksi')">
                                            @csrf
                                            <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-[#1D9E75] bg-[#1D9E75] text-white hover:bg-[#188864]">Setujui</button>
                                        </form>

                                        <details>
                                            <summary class="cursor-pointer text-center text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-200 text-red-600 hover:bg-red-50">Tolak</summary>
                                            <form method="POST" action="{{ route('kepsek.edit-request.reject', $request->id) }}" class="mt-2 space-y-2" onsubmit="return confirmReject('edit transaksi')">
                                                @csrf
                                                <textarea name="catatan_kepsek" rows="2" maxlength="300" class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs" placeholder="Catatan penolakan (min. 10 karakter)">{{ old('catatan_kepsek') }}</textarea>
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
                            <th class="text-[10px] uppercase tracking-wide text-gray-400 font-medium px-4 py-3 border-b border-gray-50 text-left">Detail</th>
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
                                    <button
                                        type="button"
                                        class="detail-button inline-flex items-center justify-center w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-gray-200 text-gray-700 hover:bg-gray-50"
                                        data-title="Detail Pengeluaran"
                                        data-subtitle="Detail transaksi pengeluaran yang menunggu persetujuan"
                                        data-bukti="{{ $item->bukti_transaksi ?? '' }}"
                                        data-points='{{ json_encode([['label' => 'Tanggal', 'value' => \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')], ['label' => 'Jumlah', 'value' => 'Rp ' . number_format($item->jumlah, 0, ',', '.')], ['label' => 'Jenis', 'value' => $item->jenis ?? '-'], ['label' => 'Keterangan', 'value' => $item->keterangan ?? '-'], ['label' => 'Status', 'value' => ucfirst($toValue($item->status))]]) }}'
                                    >Detail</button>
                                </td>
                                <td class="text-sm px-4 py-3">
                                    <div class="flex flex-col gap-2 min-w-48">
                                        <form method="POST" action="{{ route('kepsek.pengeluaran.approve', $item->id) }}" onsubmit="return confirmApprove('pengeluaran Rp ' + formatRupiah({{ $item->jumlah }}))">
                                            @csrf
                                            <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-[#1D9E75] bg-[#1D9E75] text-white hover:bg-[#188864]">Setujui</button>
                                        </form>

                                        <details>
                                            <summary class="cursor-pointer text-center text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-200 text-red-600 hover:bg-red-50">Tolak</summary>
                                            <form method="POST" action="{{ route('kepsek.pengeluaran.reject', $item->id) }}" class="mt-2 space-y-2" onsubmit="return confirmReject('pengeluaran Rp ' + formatRupiah({{ $item->jumlah }}))">
                                                @csrf
                                                <textarea name="catatan_kepsek" rows="2" maxlength="300" class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs" placeholder="Catatan penolakan (min. 10 karakter)">{{ old('catatan_kepsek') }}</textarea>
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

    {{-- include shared transaksi detail modal for Kepala Sekolah role --}}
    @include('admin.components.detail-transaksi-modal')

    <div id="detailModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/40" data-detail-close></div>
        <div class="relative w-full max-w-2xl mx-4 rounded-2xl bg-white shadow-xl overflow-hidden">
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-4">
                <div>
                    <h3 id="detailModalTitle" class="text-base font-semibold text-gray-800">Detail Transaksi</h3>
                    <p id="detailModalSubtitle" class="mt-1 text-xs text-gray-500"></p>
                </div>
                <button type="button" class="rounded-lg bg-gray-100 px-3 py-1.5 text-sm text-gray-500 hover:bg-gray-200" data-detail-close>&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto px-6 py-5">
                <div id="detailModalBody" class="space-y-3 text-sm text-gray-700"></div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Format rupiah untuk display
         */
        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function getFileIcon(filename) {
            if (!filename) return '📄';
            const ext = filename.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return '🖼️';
            if (ext === 'pdf') return '📕';
            return '📄';
        }

        function renderBuktiTransaksi(buktiPath) {
            if (!buktiPath) return '';
            const fileUrl = '/storage/' + buktiPath;
            const safeUrl = encodeURI(fileUrl);
            const filename = buktiPath.split('/').pop();
            const icon = getFileIcon(filename);
            const ext = filename.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            const isPdf = ext === 'pdf';
            
            return `
                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                    <div class="text-[11px] uppercase tracking-wide text-gray-400">Bukti Transaksi</div>
                    <div class="mt-3 overflow-hidden rounded-xl border border-blue-100 bg-white">
                        ${isImage ? `<img src="${safeUrl}" alt="Bukti transaksi" class="block w-full max-h-[280px] object-contain bg-white">` : isPdf ? `<div class="p-4 text-center text-blue-700 bg-blue-50"><div class="text-3xl leading-none">${icon}</div><div class="mt-2 text-xs font-semibold">Pratinjau PDF tersedia melalui tombol Lihat File</div></div>` : `<div class="p-4 text-center text-gray-500 bg-gray-50"><div class="text-3xl leading-none">${icon}</div><div class="mt-2 text-xs font-semibold">File siap dibuka</div></div>`}
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <a href="${safeUrl}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 transition">
                            <span>${icon}</span>
                            <span>Lihat File</span>
                        </a>
                        <a href="${safeUrl}" download class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                            <span>⬇️</span>
                            <span>Download</span>
                        </a>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 break-all">${escapeHtml(filename)}</div>
                </div>
            `;
        }

        function renderDetailPoints(points) {
            return points.map((point) => `
                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                    <div class="text-[11px] uppercase tracking-wide text-gray-400">${escapeHtml(point.label)}</div>
                    <div class="mt-1 text-sm text-gray-800 whitespace-pre-wrap break-words">${escapeHtml(point.value || '-')}</div>
                </div>
            `).join('');
        }

        function openDetailModal(title, subtitle, points, buktiPath = '') {
            document.getElementById('detailModalTitle').textContent = title;
            document.getElementById('detailModalSubtitle').textContent = subtitle || '';
            let modalBody = renderDetailPoints(points);
            if (buktiPath) {
                modalBody += renderBuktiTransaksi(buktiPath);
            }
            document.getElementById('detailModalBody').innerHTML = modalBody;
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('click', function (event) {
            const detailButton = event.target.closest('.detail-button');
                if (detailButton) {
                    try {
                        const points = JSON.parse(detailButton.dataset.points || '[]');
                        const buktiPath = detailButton.dataset.bukti || '';
                        // transform points into fields expected by shared modal
                        const fields = (points || []).map(p => ({
                            label: p.label,
                            value: p.value,
                            isPeso: /jumlah/i.test(String(p.label)) || (typeof p.value === 'string' && String(p.value).trim().startsWith('Rp'))
                        }));

                        if (typeof openDetailTransaksi === 'function') {
                            openDetailTransaksi(fields, buktiPath);
                        } else {
                            openDetailModal(detailButton.dataset.title || 'Detail Transaksi', detailButton.dataset.subtitle || '', points, buktiPath);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                    return;
                }

            if (event.target.matches('[data-detail-close]') || event.target.closest('[data-detail-close]')) {
                closeDetailModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDetailModal();
            }
        });

        /**
         * Konfirmasi approve
         */
        function confirmApprove(item) {
            return confirm(
                '🔍 KONFIRMASI APPROVAL\n\n' +
                'Anda yakin ingin menyetujui ' + item + ' ini?\n\n' +
                'Tindakan ini tidak dapat dibatalkan.'
            );
        }

        /**
         * Konfirmasi reject dengan pengecekan catatan
         */
        function confirmReject(item) {
            const form = event.target;
            const catatan = form.querySelector('textarea[name="catatan_kepsek"]').value.trim();

            if (catatan.length < 10) {
                alert('⚠️ Catatan penolakan harus minimal 10 karakter');
                return false;
            }

            return confirm(
                '🔍 KONFIRMASI PENOLAKAN\n\n' +
                'Anda yakin ingin menolak ' + item + ' ini?\n\n' +
                'Catatan: ' + catatan.substring(0, 50) + (catatan.length > 50 ? '...' : '') + '\n\n' +
                'Tindakan ini tidak dapat dibatalkan.'
            );
        }
    </script>
@endsection
