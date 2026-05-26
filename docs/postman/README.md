# Postman Tests SIKEU

File yang bisa di-import:

- `SIKEU.postman_collection.json`
- `SIKEU.local.postman_environment.json`

## Step-by-step

### 1. Jalankan aplikasi Laravel

Pastikan aplikasi SIKEU sedang aktif di lokal.

```bash
php artisan serve
```

Kalau aplikasi berjalan di port lain, sesuaikan `base_url` di environment Postman.

### 2. Buka Postman

Karena Postman sudah terpasang, buka aplikasinya lalu pilih workspace yang akan dipakai untuk pengujian.

### 3. Import collection dan environment

Klik `Import`, lalu pilih dua file berikut:

- `SIKEU.postman_collection.json`
- `SIKEU.local.postman_environment.json`

Setelah import selesai, koleksi `SIKEU Laravel Blackbox Tests` akan muncul di sidebar.

### 4. Pilih environment `SIKEU Local`

Di kanan atas Postman, pilih environment `SIKEU Local`.

Environment ini berisi variabel yang dipakai request, seperti:

- `base_url`
- `csrf_token`
- `admin_username`
- `admin_password`
- `kepsek_username`
- `kepsek_password`
- `siswa_id`
- `transaksi_id`

### 5. Ambil CSRF token dulu

Jalankan request `GET Login Page` terlebih dahulu.

Request ini akan membaca halaman login dan menyimpan `csrf_token` otomatis ke collection variable.

### 6. Login sebagai admin atau kepsek

Jalankan request login sesuai akun yang mau dites:

- `POST Login Admin`
- `POST Login Kepsek`

Kalau login berhasil, Postman biasanya menerima redirect ke dashboard yang sesuai.

### 7. Jalankan request protected

Setelah login, lanjutkan ke request lain sesuai kebutuhan:

- `GET Dashboard Admin`
- `GET Cari Siswa`
- `POST Siswa Create Valid`
- `PUT Siswa Update Valid`
- `DELETE Siswa`
- `POST Pemasukan Valid Non-SPP`
- `POST Pengeluaran Valid`
- `GET Dashboard Kepsek`
- `POST Approve Pengeluaran`

### 8. Siapkan file upload untuk pemasukan dan pengeluaran

Untuk request yang memakai `bukti_transaksi`, pilih file lokal sebelum request dijalankan.

Gunakan file dengan format:

- JPG
- JPEG
- PNG
- PDF

Pastikan ukuran file tidak lebih dari 2MB.

### 9. Ubah data pengujian jika perlu

Kalau ingin menguji data yang sudah ada di database, ubah variabel environment berikut:

- `siswa_id`
- `transaksi_id`
- `siswa_search_query`

### 10. Baca hasil response

Perhatikan status code hasil request:

- `200` atau `302` biasanya berarti flow web berhasil
- `422` berarti validasi gagal sesuai skenario negatif

## Catatan

- Koleksi ini fokus pada smoke test dan blackbox test manual.
- Request valid menggunakan redirect yang umum di aplikasi web Laravel.
- Request invalid disetel memakai `Accept: application/json` agar validasi mudah diperiksa dari response `422`.
- Kalau session login habis, ulangi dari langkah `GET Login Page` lalu login lagi.
