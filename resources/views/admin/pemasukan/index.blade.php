@extends('layouts.admin')

@section('page-title', 'Pemasukan')
@section('page-subtitle', 'Kelola semua data pemasukan sekolah')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Dashboard</a>
    <a href="{{ route('admin.pemasukan.index') }}" class="block px-3 py-2 rounded mb-1 bg-[var(--accent)] text-white">Pemasukan</a>
    <a href="{{ route('admin.pengeluaran.index') }}" class="block px-3 py-2 rounded mb-1 text-gray-700 hover:bg-gray-100">Pengeluaran</a>
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
    @endphp

    <div class="space-y-4" x-data="{ showFlash: true, modalOpen: {{ $errors->any() ? 'true' : 'false' }} }" @keydown.escape.window="modalOpen = false">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-base font-medium text-gray-800">Pemasukan</h1>
                <p class="text-xs text-gray-400 mt-0.5">Kelola semua data pemasukan sekolah</p>
            </div>
            <button @click="modalOpen = true" class="inline-flex items-center gap-2 bg-[#1D9E75] text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-[#0F6E56] transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah Pemasukan</span>
            </button>
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
                        <div class="text-xs text-gray-400">Total Pemasukan bulan ini</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $formatRupiah($total_pemasukan) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Dari transaksi yang sudah disetujui</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5-5 4 4 5-5M5 19h14" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-400">Menunggu Approval</div>
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $transaksis->where('status', 'pending')->count() }}</div>
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
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $transaksis->where('status', 'approved')->count() }}</div>
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
                        <div class="mt-1 text-xl font-medium text-gray-800">{{ $transaksis->where('status', 'rejected')->count() }}</div>
                        <div class="text-xs text-gray-400 mt-1">Transaksi rejected</div>
                    </div>
                    <div class="rounded-lg p-2 w-8 h-8 bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
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
                <a href="{{ route('admin.pemasukan.index') }}" class="bg-white border border-gray-200 text-xs rounded-lg px-4 h-9 inline-flex items-center">Reset</a>
            </form>
        </div>

        @if($transaksis->count() > 0)
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
                            @foreach($transaksis as $index => $item)
                                @php $jenis = $statusValue($item->jenis); @endphp
                                <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-b-0">
                                    <td class="text-sm px-4 py-2.5">{{ $transaksis->firstItem() + $index }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ optional($item->tanggal)->format('d/m/Y') }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ $item->keterangan }}</td>
                                    <td class="text-sm px-4 py-2.5">{{ $item->siswa?->nama ?? '-' }}</td>
                                    <td class="text-sm px-4 py-2.5 font-medium text-emerald-600">{{ $formatRupiah($item->jumlah) }}</td>
                                    <td class="text-sm px-4 py-2.5">
                                        @if($item->bukti_transaksi)
                                            <a href="{{ Storage::url($item->bukti_transaksi) }}" target="_blank" class="text-[11px] px-3 py-1 rounded-lg border font-medium border-gray-200 text-gray-600 hover:bg-gray-50">Lihat</a>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="text-sm px-4 py-2.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass($item->status) }}">{{ $statusLabel($item->status) }}</span>
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
                <h3 class="text-sm text-gray-400 mb-2">Belum ada data pemasukan</h3>
                <button @click="modalOpen = true" class="inline-flex items-center gap-2 bg-[#1D9E75] text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-[#0F6E56]">+ Tambah Pemasukan</button>
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH PEMASUKAN -->
    <div x-show="modalOpen" class="fixed inset-0 z-50" @click="modalOpen = false" style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

        <!-- Modal Box -->
        <div class="fixed inset-0 flex items-center justify-center p-4" @click.stop>
            <div x-show="modalOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl w-full max-w-lg shadow-xl" @click.stop>
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">Tambah Pemasukan</h2>
                        <p class="text-xs text-gray-500 mt-1">Isi form di bawah untuk menambah transaksi pemasukan baru</p>
                    </div>
                    <button @click="modalOpen = false" class="text-gray-400 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <!-- Content -->
                <form method="POST" action="{{ route('admin.pemasukan.store') }}" enctype="multipart/form-data" x-data="formData()" class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                    @csrf

                    <!-- Tanggal -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#1D9E75] focus:border-transparent" required>
                        @error('tanggal')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <span class="absolute left-3 top-2.5 text-sm text-gray-500">Rp</span>
                                <input type="text" x-model="rupiah.display" @input="rupiah.format()" placeholder="0" class="w-full text-sm border border-gray-200 rounded-lg pl-8 pr-3 py-2.5 focus:ring-2 focus:ring-[#1D9E75] focus:border-transparent">
                                <input type="hidden" name="jumlah" x-model="rupiah.raw">
                            </div>
                        </div>
                        @if($errors->has('jumlah'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('jumlah') }}</p>
                        @endif
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-xs text-gray-400">(<span x-text="keterangan.length"></span>/500)</span></label>
                        <textarea name="keterangan" x-model="keterangan" maxlength="500" rows="3" placeholder="Deskripsi pemasukan..." class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#1D9E75] focus:border-transparent resize-none"></textarea>
                        @error('keterangan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Siswa -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Siswa <span class="text-xs text-gray-400">(Opsional)</span></label>
                        <select name="id_siswa" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-[#1D9E75] focus:border-transparent">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}" {{ old('id_siswa') == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }} ({{ $siswa->kelas }})</option>
                            @endforeach
                        </select>
                        @error('id_siswa')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Bukti -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti <span class="text-xs text-gray-400">(Opsional)</span></label>
                        <div x-data="fileUpload()" class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center cursor-pointer hover:border-[#1D9E75] transition-colors"
                             @dragover.prevent="$el.classList.add('border-[#1D9E75]', 'bg-emerald-50')"
                             @dragleave.prevent="$el.classList.remove('border-[#1D9E75]', 'bg-emerald-50')"
                             @drop.prevent="handleDrop($event); $el.classList.remove('border-[#1D9E75]', 'bg-emerald-50')">
                            
                            <input type="file" @change="handleFile($event)" x-ref="fileInput" class="hidden" accept="image/*,.pdf" name="bukti_transaksi">

                            <template x-if="!preview">
                                <div @click="$refs.fileInput.click()" class="py-3">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Drag atau klik untuk upload file</p>
                                    <p class="text-xs text-gray-400 mt-1">Gambar atau PDF</p>
                                </div>
                            </template>

                            <template x-if="preview">
                                <div class="py-3">
                                    <template x-if="isImage">
                                        <img :src="preview" class="h-20 mx-auto mb-2 rounded">
                                    </template>
                                    <template x-if="!isImage">
                                        <div class="w-10 h-10 bg-red-50 rounded mx-auto mb-2 flex items-center justify-center">
                                            <span class="text-xs text-red-600 font-medium">PDF</span>
                                        </div>
                                    </template>
                                    <p class="text-sm font-medium text-gray-800" x-text="fileName"></p>
                                    <button type="button" @click="clearFile()" class="text-xs text-red-500 hover:text-red-700 mt-2">Hapus File</button>
                                </div>
                            </template>
                        </div>
                        @error('bukti_transaksi')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="hidden" name="status" value="pending">
                    <input type="hidden" name="jenis" value="pemasukan">

                    <!-- Footer -->
                    <div class="flex gap-2 pt-6 border-t border-gray-100">
                        <button type="button" @click="modalOpen = false" class="flex-1 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" class="flex-1 text-sm font-medium text-white bg-[#1D9E75] rounded-lg px-4 py-2 hover:bg-[#0F6E56] transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function formData() {
            return {
                rupiah: {
                    display: '{{ old('jumlah') ? number_format(old('jumlah')) : '' }}',
                    raw: '{{ old('jumlah') ?? '' }}',
                    format() {
                        let angka = this.display.replace(/\D/g, '');
                        this.raw = angka;
                        this.display = angka ? parseInt(angka).toLocaleString('id-ID') : '';
                    }
                },
                keterangan: '{{ old('keterangan') ?? '' }}'
            }
        }

        function fileUpload() {
            return {
                preview: null,
                fileName: '',
                isImage: false,
                handleFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.fileName = file.name;
                    this.isImage = file.type.startsWith('image/');
                    if (this.isImage) {
                        const reader = new FileReader();
                        reader.onload = e => this.preview = e.target.result;
                        reader.readAsDataURL(file);
                    } else {
                        this.preview = 'pdf';
                    }
                },
                handleDrop(event) {
                    const file = event.dataTransfer.files[0];
                    if (!file) return;
                    this.$refs.fileInput.files = event.dataTransfer.files;
                    this.handleFile({ target: { files: [file] } });
                },
                clearFile() {
                    this.preview = null;
                    this.fileName = '';
                    this.isImage = false;
                    this.$refs.fileInput.value = '';
                }
            }
        }
    </script>
@endsection