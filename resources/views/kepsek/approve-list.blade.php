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
                                    <div><span class="text-gray-500">Jumlah:</span> {{ $request->format_uang_lama }} <span class="text-gray-400">→</span> {{ $request->format_uang_baru }}</div>
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
                                        data-points='{{ json_encode([['label' => 'Tanggal', 'value' => optional($request->created_at)->format('d/m/Y H:i')], ['label' => 'Diajukan oleh', 'value' => $request->requestedBy->name ?? '-'], ['label' => 'Jumlah lama', 'value' => $request->format_uang_lama], ['label' => 'Jumlah baru', 'value' => $request->format_uang_baru], ['label' => 'Jenis lama', 'value' => ucfirst((string) $request->old_jenis)], ['label' => 'Jenis baru', 'value' => ucfirst((string) $request->new_jenis)], ['label' => 'Keterangan lama', 'value' => $request->old_keterangan ?? '-'], ['label' => 'Keterangan baru', 'value' => $request->new_keterangan ?? '-']]) }}'
                                    >Detail</button>
                                </td>
                                <td class="text-sm px-4 py-3">
                                    <div class="flex flex-col gap-2 min-w-48">
                                        <form method="POST" action="{{ route('kepsek.edit-request.approve', $request->id) }}"
                                              onsubmit="return openApproveModal(event, this)"
                                              data-approve-date="{{ optional($request->created_at)->format('d/m/Y H:i') }}"
                                              data-approve-type="edit transaksi"
                                              data-approve-requested-by="{{ $request->requestedBy->name ?? '-' }}"
                                              data-approve-amount="{{ $request->format_uang_baru }}"
                                              data-approve-amount-detail="{{ $request->format_uang_lama }} → {{ $request->format_uang_baru }}"
                                              data-approve-row-title="Permintaan Edit Transaksi">
                                            @csrf
                                            <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-[#1D9E75] bg-[#1D9E75] text-white hover:bg-[#188864]">Setujui</button>
                                        </form>

                                        <details>
                                            <summary class="cursor-pointer text-center text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-200 text-red-600 hover:bg-red-50">Tolak</summary>
                                            <form method="POST" action="{{ route('kepsek.edit-request.reject', $request->id) }}" class="mt-2 space-y-2" onsubmit="return confirmReject('edit transaksi', event)">
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
                                <td class="text-sm px-4 py-3 font-medium text-emerald-700">{{ $item->format_uang }}</td>
                                <td class="text-sm px-4 py-3">{{ $item->jenis ?? '-' }}</td>
                                <td class="text-sm px-4 py-3">{{ Str::limit($item->keterangan, 40) }}</td>
                                <td class="text-sm px-4 py-3">
                                    <button
                                        type="button"
                                        class="detail-button inline-flex items-center justify-center w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-gray-200 text-gray-700 hover:bg-gray-50"
                                        data-title="Detail Pengeluaran"
                                        data-subtitle="Detail transaksi pengeluaran yang menunggu persetujuan"
                                        data-bukti="{{ $item->bukti_transaksi ?? '' }}"
                                        data-points='{{ json_encode([['label' => 'Tanggal', 'value' => \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')], ['label' => 'Jumlah', 'value' => $item->format_uang], ['label' => 'Jenis', 'value' => $item->jenis ?? '-'], ['label' => 'Keterangan', 'value' => $item->keterangan ?? '-'], ['label' => 'Status', 'value' => ucfirst($toValue($item->status))]]) }}'
                                    >Detail</button>
                                </td>
                                <td class="text-sm px-4 py-3">
                                    <div class="flex flex-col gap-2 min-w-48">
                                        <form method="POST" action="{{ route('kepsek.pengeluaran.approve', $item->id) }}"
                                              onsubmit="return openApproveModal(event, this)"
                                              data-approve-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}"
                                              data-approve-type="pengeluaran"
                                              data-approve-requested-by="-"
                                              data-approve-amount="{{ $item->format_uang }}"
                                              data-approve-amount-detail="{{ $item->format_uang }}"
                                              data-approve-row-title="Detail Pengeluaran">
                                            @csrf
                                            <button type="submit" class="w-full text-[11px] px-3 py-1.5 rounded-lg border font-medium border-[#1D9E75] bg-[#1D9E75] text-white hover:bg-[#188864]">Setujui</button>
                                        </form>

                                        <details>
                                            <summary class="cursor-pointer text-center text-[11px] px-3 py-1.5 rounded-lg border font-medium border-red-200 text-red-600 hover:bg-red-50">Tolak</summary>
                                            <form method="POST" action="{{ route('kepsek.pengeluaran.reject', $item->id) }}" class="mt-2 space-y-2" onsubmit="return confirmReject('pengeluaran ' + '{{ $item->format_uang }}', event)">
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

    <div id="approveModal" class="fixed inset-0 z-[120] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle">
        <div class="absolute inset-0 bg-black/50" data-approve-close></div>
        <div class="relative flex max-h-[90vh] w-full max-w-md mx-4 flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="bg-[#EAF3DE] px-6 pt-8 pb-6 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-green-200 bg-white text-4xl font-bold leading-none text-[#3B6D11]">✓</div>
                <h3 id="approveModalTitle" class="mt-5 text-2xl font-medium text-gray-700">Konfirmasi persetujuan</h3>
                <p class="mt-3 text-sm leading-6 text-gray-600">Pastikan data sudah benar sebelum menyetujui</p>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div class="grid gap-3">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-gray-400">Tanggal transaksi</div>
                        <div id="approveModalDate" class="mt-1 text-sm text-gray-800">-</div>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-gray-400">Jenis</div>
                        <div id="approveModalType" class="mt-1 text-sm text-gray-800">-</div>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-gray-400">Diajukan oleh</div>
                        <div id="approveModalRequestedBy" class="mt-1 text-sm text-gray-800">-</div>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-gray-400">Jumlah</div>
                        <div id="approveModalAmount" class="mt-1 text-sm font-medium text-gray-800">-</div>
                    </div>
                </div>

                <p class="mt-4 text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan setelah disetujui.</p>
            </div>

            <div class="shrink-0 border-t border-gray-100 bg-white px-6 py-4">
                <div class="flex flex-nowrap items-center gap-3">
                    <button type="button" id="approveModalCancel" class="w-24 shrink-0 rounded-md border border-red-500 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Tolak</button>
                    <button type="button" id="approveModalConfirm" class="flex-1 min-w-0 rounded-md px-5 py-2.5 text-sm font-semibold text-white shadow-sm" style="background:#3B6D11; color:#ffffff; min-height:42px;">Ya, setujui</button>
                </div>
            </div>
        </div>
    </div>

    <div id="rejectPopup" class="fixed inset-0 z-[110] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/50" data-reject-close></div>
        <div class="relative flex max-h-[90vh] w-full max-w-md mx-4 flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="bg-[#FDEDED] px-6 pt-8 pb-6 text-center">
                <div id="rejectPopupIconWrap" class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-200 bg-white text-4xl font-bold leading-none text-[#B42318]">!</div>
                <h3 id="rejectPopupTitle" class="mt-5 text-2xl font-medium text-gray-700">Konfirmasi penolakan</h3>
                <p id="rejectPopupMessage" class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-600">Pastikan data sudah benar sebelum menolak</p>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div class="grid gap-3">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-gray-400">Keterangan</div>
                        <div id="rejectPopupNote" class="mt-1 text-sm text-gray-800 whitespace-pre-line">-</div>
                    </div>
                </div>

                <p class="mt-4 text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan setelah ditolak.</p>
            </div>

            <div class="shrink-0 border-t border-gray-100 bg-white px-6 py-4">
                <div class="flex flex-nowrap items-center gap-3">
                    <button type="button" id="rejectPopupConfirm" class="hidden w-24 shrink-0 rounded-md border border-red-500 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Ya</button>
                    <button type="button" id="rejectPopupCancel" class="flex-1 min-w-0 rounded-md border border-blue-500 px-5 py-2.5 text-sm font-medium text-blue-600 hover:bg-blue-50" data-reject-close>Batal</button>
                </div>
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

        let approveModalForm = null;

        function openApproveModal(event, form) {
            event.preventDefault();

            approveModalForm = form;

            const modal = document.getElementById('approveModal');
            document.getElementById('approveModalDate').textContent = form.dataset.approveDate || '-';
            document.getElementById('approveModalType').textContent = form.dataset.approveType || '-';
            document.getElementById('approveModalRequestedBy').textContent = form.dataset.approveRequestedBy || '-';
            document.getElementById('approveModalAmount').textContent = form.dataset.approveAmountDetail || form.dataset.approveAmount || '-';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return false;
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            approveModalForm = null;
        }

        document.getElementById('approveModalCancel').addEventListener('click', closeApproveModal);
        document.getElementById('approveModalConfirm').addEventListener('click', function () {
            if (approveModalForm) {
                const form = approveModalForm;
                closeApproveModal();
                form.submit();
            }
        });

        function openRejectPopup(message, options = {}) {
            const popup = document.getElementById('rejectPopup');
            const popupMessage = document.getElementById('rejectPopupMessage');
            const popupTitle = document.getElementById('rejectPopupTitle');
            const popupIcon = document.getElementById('rejectPopupIconWrap');
            const popupNote = document.getElementById('rejectPopupNote');
            const popupConfirm = document.getElementById('rejectPopupConfirm');
            const popupCancel = document.getElementById('rejectPopupCancel');

            if (!popup || !popupMessage || !popupTitle || !popupIcon || !popupNote || !popupConfirm || !popupCancel) {
                return;
            }

            popupMessage.textContent = message;
            popupTitle.textContent = options.title || 'Perhatian';
            popupNote.textContent = message || '-';
            popupIcon.textContent = options.icon || '!';
            popupIcon.className = options.iconClass || 'mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-200 bg-white text-4xl font-bold leading-none text-[#B42318]';

            popupConfirm.textContent = options.confirmText || 'Ya';
            popupCancel.textContent = options.cancelText || 'Batal';
            popupConfirm.classList.toggle('hidden', !!options.hideConfirm);
            popupCancel.classList.toggle('hidden', !!options.hideCancel);
            popupConfirm.classList.toggle('w-full', !!options.hideCancel);
            popupConfirm.classList.toggle('flex-1', !options.hideCancel);
            popupConfirm.classList.toggle('w-24', !options.hideCancel);
            popupConfirm.classList.toggle('shrink-0', !options.hideCancel);
            if (typeof options.onConfirm === 'function') {
                popupConfirm.onclick = null;
                popupConfirm.onclick = function () {
                    closeRejectPopup();
                    options.onConfirm();
                };
            } else {
                popupConfirm.onclick = null;
                popupConfirm.onclick = closeRejectPopup;
            }

            popup.classList.remove('hidden');
            popup.classList.add('flex');
        }

        function closeRejectPopup() {
            const popup = document.getElementById('rejectPopup');
            if (!popup) {
                return;
            }

            popup.classList.add('hidden');
            popup.classList.remove('flex');
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

            if (event.target.matches('[data-approve-close]') || event.target.closest('[data-approve-close]')) {
                closeApproveModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDetailModal();
                closeApproveModal();
            }
        });

        /**
         * Konfirmasi reject dengan pengecekan catatan
         */
        function confirmReject(item, evt) {
            const form = evt && evt.target ? evt.target : event.target;
            const catatan = form.querySelector('textarea[name="catatan_kepsek"]').value.trim();

            if (catatan.length < 10) {
                openRejectPopup('Catatan penolakan harus minimal 10 karakter.', {
                    title: 'Catatan tidak valid',
                    icon: '!',
                    hideCancel: true,
                    confirmText: 'OK',
                    iconClass: 'mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-200 bg-white text-4xl font-bold leading-none text-[#B42318]'
                });
                return false;
            }

            evt.preventDefault();
            openRejectPopup(
                'Anda yakin ingin menolak ' + item + ' ini?\n\nCatatan: ' + catatan.substring(0, 50) + (catatan.length > 50 ? '...' : ''),
                {
                    title: 'Yakin tolak data?',
                    icon: '!',
                    confirmText: 'Ya',
                    cancelText: 'Batal',
                    iconClass: 'mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-200 bg-white text-4xl font-bold leading-none text-[#B42318]',
                    onConfirm: function () {
                        form.submit();
                    }
                }
            );

            return false;
        }

        document.addEventListener('input', function (event) {
            if (!event.target || event.target.name !== 'catatan_kepsek') {
                return;
            }

            closeRejectPopup();
        });

        document.addEventListener('click', function (event) {
            if (event.target && (event.target.matches('[data-reject-close]') || event.target.closest('[data-reject-close]'))) {
                closeRejectPopup();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeRejectPopup();
            }
        });
    </script>
@endsection
