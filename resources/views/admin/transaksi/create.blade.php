@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('admin.transaksi.store') }}" method="POST" enctype="multipart/form-data" x-data="transaksiForm()">
            @csrf

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Form Input Pemasukan Baru</h2>
                    <a href="{{ route('admin.transaksi.index') }}" class="text-sm text-gray-600">Kembali</a>
                </div>

                {{-- Tanggal --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    @error('tanggal') <p class="text-sm text-red-600 mb-1">{{ $message }}</p> @enderror
                    <input type="date" name="tanggal" required value="{{ old('tanggal', date('Y-m-d')) }}" class="mt-1 block w-full border rounded px-3 py-2 @error('tanggal') border-red-500 @else border-gray-300 @enderror" />
                </div>

                {{-- Jumlah (formatted + hidden numeric) --}}
                <div class="mb-4" x-cloak>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    @error('jumlah') <p class="text-sm text-red-600 mb-1">{{ $message }}</p> @enderror
                    <div class="mt-1 flex gap-2">
                        <input x-model="displayJumlah" x-on:input="formatRupiah()" type="text" class="block w-full border rounded px-3 py-2 @error('jumlah') border-red-500 @else border-gray-300 @enderror" placeholder="Contoh: 1.500.000" autocomplete="off">
                        <input type="hidden" name="jumlah" x-model.number="rawJumlah">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    @error('keterangan') <p class="text-sm text-red-600 mb-1">{{ $message }}</p> @enderror
                    <textarea name="keterangan" x-on:input="charCount()" x-model="keterangan" maxlength="500" class="mt-1 block w-full border rounded px-3 py-2 @error('keterangan') border-red-500 @else border-gray-300 @enderror" rows="4">{{ old('keterangan') }}</textarea>
                    <div class="text-sm text-gray-500 mt-1">Sisa karakter: <span x-text="500 - keterangan.length"></span></div>
                </div>

                {{-- Siswa select --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Siswa (opsional)</label>
                    @error('id_siswa') <p class="text-sm text-red-600 mb-1">{{ $message }}</p> @enderror
                    <select name="id_siswa" class="mt-1 block w-full border rounded px-3 py-2 @error('id_siswa') border-red-500 @else border-gray-300 @enderror">
                        <option value="">-- Tidak ada --</option>
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}" {{ old('id_siswa') == $siswa->id ? 'selected' : '' }}>{{ $siswa->nama }} - Kelas {{ $siswa->kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Upload bukti (drag & drop) --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Bukti Transaksi (jpg,jpeg,png,pdf) - max 2MB</label>
                    @error('bukti_transaksi') <p class="text-sm text-red-600 mb-1">{{ $message }}</p> @enderror
                    <div class="mt-2">
                        <div class="border-dashed border-2 border-gray-300 rounded p-4 text-center" x-on:dragover.prevent x-on:drop.prevent="handleDrop($event)" x-on:click="$refs.fileInput.click()">
                            <input type="file" x-ref="fileInput" name="bukti_transaksi" class="hidden" x-on:change="previewFile($event)" accept="image/jpeg,image/png,application/pdf" />
                            <template x-if="filePreview">
                                <div class="flex items-center justify-center">
                                    <template x-if="isImage">
                                        <img :src="filePreview" class="max-h-40 object-contain" alt="Preview" />
                                    </template>
                                    <template x-if="!isImage">
                                        <div class="text-sm text-gray-700">File: <span x-text="fileName"></span></div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!filePreview">
                                <div class="text-sm text-gray-500">Tarik file ke sini atau klik untuk memilih file</div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 bg-[#1D9E75] text-white rounded">Simpan Transaksi</button>
                    <a href="{{ route('admin.transaksi.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function transaksiForm() {
        return {
            displayJumlah: '{{ old('jumlah') ? number_format((int) old('jumlah'), 0, ',', '.') : '' }}',
            rawJumlah: {{ old('jumlah') !== null ? (int) old('jumlah') : 'null' }},
            keterangan: `{{ addslashes(old('keterangan', '')) }}`,
            filePreview: null,
            fileName: null,
            isImage: false,

            formatRupiah() {
                // Remove non-digits
                let digits = this.displayJumlah.toString().replace(/[^0-9]/g, '');
                this.rawJumlah = digits ? parseInt(digits, 10) : null;
                // Format with thousands separator
                if (digits) {
                    this.displayJumlah = new Intl.NumberFormat('id-ID').format(digits);
                } else {
                    this.displayJumlah = '';
                }
            },

            charCount() {
                // reactive via x-model on textarea
            },

            previewFile(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.fileName = file.name;
                const type = file.type;
                if (type.startsWith('image/')) {
                    this.isImage = true;
                    const reader = new FileReader();
                    reader.onload = (ev) => { this.filePreview = ev.target.result; };
                    reader.readAsDataURL(file);
                } else {
                    this.isImage = false;
                    this.filePreview = null;
                }
            },

            handleDrop(e) {
                const file = e.dataTransfer.files[0];
                if (!file) return;
                // assign file to input
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.previewFile({ target: { files: e.dataTransfer.files } });
            }
        }
    }
</script>

@endsection
