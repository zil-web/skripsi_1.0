@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Tambah Pengeluaran</h1>
            <p class="text-gray-600 mt-2">Isi formulir di bawah untuk menambahkan pengeluaran baru</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('admin.pengeluaran.store') }}" method="POST" enctype="multipart/form-data"
                x-data="formData()" @submit="handleSubmit($event)">
                @csrf

                <!-- Tanggal -->
                <div class="mb-6">
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    @error('tanggal')
                        <p class="text-red-500 text-sm mb-1">{{ $message }}</p>
                    @enderror
                    <input type="date" id="tanggal" name="tanggal"
                        value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('tanggal') border-red-500 @else border-gray-300 @enderror">
                </div>

                <!-- Jumlah dengan Format Rupiah -->
                <div class="mb-6">
                    <label for="jumlah_display" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    @error('jumlah')
                        <p class="text-red-500 text-sm mb-1">{{ $message }}</p>
                    @enderror
                    <div class="relative">
                        <span class="absolute left-4 top-2 text-gray-600 font-medium">Rp</span>
                        <input type="text" id="jumlah_display" placeholder="0"
                            x-model="jumlahDisplay" @input="handleJumlahInput($event)" @blur="formatJumlah"
                            class="w-full px-4 py-2 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('jumlah') border-red-500 @else border-gray-300 @enderror"
                            inputmode="numeric">
                        <input type="hidden" id="jumlah" name="jumlah" x-model="jumlahActual">
                    </div>
                    <p id="jumlahInlineError" class="text-red-500 text-sm mt-1 hidden"></p>
                    <p class="text-gray-500 text-xs mt-1">Masukkan angka tanpa separator (misal: 1500000)</p>
                </div>

                <!-- Keterangan dengan Counter -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700">
                            Keterangan
                        </label>
                        <span class="text-xs text-gray-500" x-text="`${charCount}/500`"></span>
                    </div>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mb-1">{{ $message }}</p>
                    @enderror
                    <textarea id="keterangan" name="keterangan" rows="4" maxlength="500"
                        x-model="keterangan" @input="updateCharCount"
                        placeholder="Contoh: Pembelian buku pelajaran untuk kelas 1-3"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none @error('keterangan') border-red-500 @else border-gray-300 @enderror">{{ old('keterangan') }}</textarea>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all"
                            :style="`width: ${(charCount / 500) * 100}%`"></div>
                    </div>
                </div>

                <!-- Jenis Pengeluaran -->
                <div class="mb-6">
                    <label for="jenis_pengeluaran" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jenis Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    @error('jenis_pengeluaran')
                        <p class="text-red-500 text-sm mb-1">{{ $message }}</p>
                    @enderror
                    <select id="jenis_pengeluaran" name="jenis_pengeluaran" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('jenis_pengeluaran') border-red-500 @else border-gray-300 @enderror">
                        <option value="">-- Pilih Jenis Pengeluaran --</option>
                        <option value="ATK" {{ old('jenis_pengeluaran') === 'ATK' ? 'selected' : '' }}>ATK (Alat Tulis Kantor)</option>
                        <option value="Konsumsi Harian" {{ old('jenis_pengeluaran') === 'Konsumsi Harian' ? 'selected' : '' }}>Konsumsi Harian</option>
                        <option value="Pembelian Aset" {{ old('jenis_pengeluaran') === 'Pembelian Aset' ? 'selected' : '' }}>Pembelian Aset</option>
                        <option value="Renovasi" {{ old('jenis_pengeluaran') === 'Renovasi' ? 'selected' : '' }}>Renovasi</option>
                        <option value="Kegiatan Besar" {{ old('jenis_pengeluaran') === 'Kegiatan Besar' ? 'selected' : '' }}>Kegiatan Besar</option>
                        <option value="Lain-lain" {{ old('jenis_pengeluaran') === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                </div>

                <!-- Upload Bukti dengan Drag & Drop -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bukti Transaksi <span class="text-red-500">*</span>
                    </label>
                    @error('bukti_transaksi')
                        <p class="text-red-500 text-sm mb-1">{{ $message }}</p>
                    @enderror

                    <div class="relative">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center transition cursor-pointer hover:border-blue-500 hover:bg-blue-50"
                            @dragover.prevent="isDragOver = true" @dragleave.prevent="isDragOver = false"
                            @drop.prevent="handleFileDrop" :class="isDragOver ? 'border-blue-500 bg-blue-50' : ''"
                            @click="$refs.fileInput.click()">

                            <!-- Upload Icon & Text -->
                            <template x-show="!filePreview">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-8-12l-4-4m4 4v12m0 0l3 3m-3-3l-3 3"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-gray-600 font-medium mt-2">Drag & drop file Anda di sini</p>
                                <p class="text-gray-500 text-sm">atau klik untuk memilih file</p>
                                <p class="text-gray-400 text-xs mt-2">Format: JPG, JPEG, PNG, PDF (Max: 2MB)</p>
                            </template>

                            <!-- Preview Gambar -->
                            <template x-show="filePreview && fileType.startsWith('image')">
                                <img :src="filePreview" alt="Preview"
                                    class="mx-auto h-40 w-40 object-cover rounded-lg mb-3">
                                <p class="text-gray-700 font-medium" x-text="fileName"></p>
                                <button type="button" @click="removeFile($event)"
                                    class="text-red-600 hover:text-red-800 text-sm mt-2">
                                    Hapus File
                                </button>
                            </template>

                            <!-- Preview PDF -->
                            <template x-show="filePreview && fileType === 'application/pdf'">
                                <svg class="mx-auto h-16 w-16 text-red-500 mb-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z" />
                                    <path d="M7 7h6v2H7V7zm0 3h6v2H7v-2z" />
                                </svg>
                                <p class="text-gray-700 font-medium" x-text="fileName"></p>
                                <button type="button" @click="removeFile($event)"
                                    class="text-red-600 hover:text-red-800 text-sm mt-2">
                                    Hapus File
                                </button>
                            </template>
                        </div>

                        <!-- Hidden File Input -->
                        <input type="file" id="bukti_transaksi" name="bukti_transaksi" @change="handleFileSelect($event)"
                            accept=".jpg,.jpeg,.png,.pdf" x-ref="fileInput" class="hidden"
                            {{ old('bukti_transaksi') ? '' : '' }}>
                    </div>
                    <p id="buktiTransaksiError" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow">
                        Simpan
                    </button>
                    <a href="{{ route('admin.pengeluaran.index') }}"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alpine.js Data & Methods -->
<script>
    function formData() {
        return {
            jumlahDisplay: '{{ old('jumlah') ? number_format(old('jumlah'), 0, ',', '.') : '' }}',
            jumlahActual: '{{ old('jumlah', 0) }}',
            keterangan: '{{ old('keterangan', '') }}',
            charCount: '{{ old('keterangan', '') }}'.length,
            isDragOver: false,
            filePreview: null,
            fileName: '',
            fileType: '',

            /**
             * Handle jumlah input dan format rupiah
             */
            handleJumlahInput(event) {
                let value = event.target.value.replace(/\D/g, '');
                this.jumlahActual = value || '0';
                this.jumlahDisplay = value ? parseInt(value, 10).toLocaleString('id-ID') : '';
                if (Number(this.jumlahActual) > 0) {
                    this.clearFieldError('jumlahInlineError');
                }
            },

            /**
             * Format jumlah saat blur
             */
            formatJumlah() {
                if (this.jumlahActual && this.jumlahActual !== '0') {
                    this.jumlahDisplay = parseInt(this.jumlahActual).toLocaleString('id-ID');
                } else {
                    this.jumlahDisplay = '';
                    this.jumlahActual = '0';
                }
            },

            /**
             * Update character counter
             */
            updateCharCount() {
                this.charCount = this.keterangan.length;
            },

            /**
             * Handle file select dari input
             */
            handleFileSelect(event) {
                const file = event.target.files[0];
                this.clearFieldError('buktiTransaksiError');
                if (file) {
                    this.processFile(file);
                }
            },

            /**
             * Handle file drop dari drag & drop
             */
            handleFileDrop(event) {
                this.isDragOver = false;
                const file = event.dataTransfer.files[0];
                this.clearFieldError('buktiTransaksiError');
                if (file) {
                    this.$refs.fileInput.files = event.dataTransfer.files;
                    this.processFile(file);
                }
            },

            /**
             * Process file untuk preview
             */
            processFile(file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    this.showFieldError('buktiTransaksiError', 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
                    this.$refs.fileInput.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    this.showFieldError('buktiTransaksiError', 'Ukuran file terlalu besar. Maksimal 2MB.');
                    this.$refs.fileInput.value = '';
                    return;
                }

                this.clearFieldError('buktiTransaksiError');

                this.fileName = file.name;
                this.fileType = file.type;

                if (file.type.startsWith('image')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.filePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    this.filePreview = 'pdf'; // Marker untuk menampilkan PDF icon
                }
            },

            /**
             * Remove file
             */
            removeFile(event) {
                event.preventDefault();
                this.filePreview = null;
                this.fileName = '';
                this.fileType = '';
                this.$refs.fileInput.value = '';
                this.clearFieldError('buktiTransaksiError');
            },

            /**
             * Handle form submit
             */
            handleSubmit(event) {
                if (!this.jumlahActual || this.jumlahActual === '0') {
                    this.showFieldError('jumlahInlineError', 'Jumlah harus lebih dari 0');
                    event.preventDefault();
                    return;
                }

                this.clearFieldError('jumlahInlineError');
            },

            showFieldError(fieldId, message) {
                const field = document.getElementById(fieldId);
                if (!field) return;

                field.textContent = message;
                field.classList.remove('hidden');
            },

            clearFieldError(fieldId) {
                const field = document.getElementById(fieldId);
                if (!field) return;

                field.textContent = '';
                field.classList.add('hidden');
            }
        };
    }
</script>
@endsection
