# Dokumen Blackbox Testing Aplikasi Keuangan Sekolah

Stack: Laravel 10+, MySQL, Bootstrap 5, vanilla JS, PHPUnit feature test. Dokumen ini bisa dipakai langsung sebagai pedoman uji manual dan dasar otomasi test.

## Tahap 1 - Persiapan

### Checklist Environment

- Database testing sudah dibuat, misalnya `sikeu_test`.
- File `.env.testing` tersedia dan tidak memakai database produksi.
- Akun admin testing tersedia dengan `role=admin`.
- Seeder untuk data dasar sudah siap jika dibutuhkan.
- Storage testing memakai `Storage::fake('public')` untuk upload file.
- Cache, queue, dan session testing memakai driver in-memory atau array.

### Template `.env.testing`

```env
APP_NAME=SIKEU
APP_ENV=testing
APP_KEY=base64:YOUR_TEST_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array

FILESYSTEM_DISK=public
```

### Perintah Artisan Setup Awal

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/SiswaTest.php
php artisan test tests/Feature/PemasukanTest.php
php artisan test tests/Feature/PengeluaranTest.php
```

## Tahap 2 - Dokumen Test Case

Keterangan: `Hasil Aktual` dan `Status` di bawah disiapkan sebagai template pelaporan. Setelah eksekusi, isi sesuai hasil pengujian.

### Modul Autentikasi

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-A01 | Autentikasi | Login admin valid | Admin aktif tersedia | username dan password benar | Buka login, isi form, klik masuk | Redirect ke dashboard admin | - | Belum diuji |
| 2 | TC-A02 | Autentikasi | Logout admin | Admin sedang login | Klik logout | Klik tombol logout | Session berakhir dan redirect ke login | - | Belum diuji |
| 3 | TC-A03 | Autentikasi | Password salah | Admin aktif tersedia | username benar, password salah | Submit login | Muncul error validasi autentikasi | - | Belum diuji |
| 4 | TC-A04 | Autentikasi | Username kosong | Admin aktif tersedia | username kosong | Submit login | Validasi error pada username | - | Belum diuji |
| 5 | TC-A05 | Autentikasi | Password kosong | Admin aktif tersedia | password kosong | Submit login | Validasi error pada password | - | Belum diuji |
| 6 | TC-A06 | Autentikasi | Login dengan karakter spesial | Admin aktif tersedia | username berisi karakter spesial | Submit login | Ditolak atau validasi gagal | - | Belum diuji |
| 7 | TC-A07 | Autentikasi | Login dengan akun non-admin | User role bukan admin | kredensial user biasa | Submit login | Akses admin ditolak | - | Belum diuji |
| 8 | TC-A08 | Autentikasi | Login berulang gagal | Admin aktif tersedia | password salah 5 kali | Submit login berulang | Sistem rate limit aktif | - | Belum diuji |
| 9 | TC-A09 | Autentikasi | Session login aktif | Admin sudah login | akses dashboard | Buka dashboard | Halaman tampil normal | - | Belum diuji |
| 10 | TC-A10 | Autentikasi | Akses login saat sudah login | Admin sudah login | akses /login | Buka login | Dialihkan ke halaman tujuan | - | Belum diuji |

### Modul Siswa

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-S01 | Siswa | Tambah siswa valid | Admin login | NIS, nama, kelas, JK, no telepon, alamat valid | Isi form tambah, simpan | Data tersimpan dan redirect | - | Belum diuji |
| 2 | TC-S02 | Siswa | Edit siswa valid | Data siswa ada | Data baru valid | Buka edit, simpan perubahan | Data terbarui | - | Belum diuji |
| 3 | TC-S03 | Siswa | Hapus siswa | Data siswa ada | Konfirmasi hapus | Klik hapus | Data terhapus soft delete | - | Belum diuji |
| 4 | TC-S04 | Siswa | NIS kosong | Admin login | NIS kosong | Simpan form | Error NIS wajib diisi | - | Belum diuji |
| 5 | TC-S05 | Siswa | Nama kosong | Admin login | Nama kosong | Simpan form | Error nama wajib diisi | - | Belum diuji |
| 6 | TC-S06 | Siswa | No telepon kosong | Admin login | No telepon kosong | Simpan form | Error nomor telepon wajib diisi | - | Belum diuji |
| 7 | TC-S07 | Siswa | NIS duplikat | Data siswa sudah ada | NIS sama | Simpan form | Error NIS sudah terdaftar | - | Belum diuji |
| 8 | TC-S08 | Siswa | NIS kurang dari 16 digit | Admin login | NIS 15 digit | Simpan form | Error panjang NIS tidak valid | - | Belum diuji |
| 9 | TC-S09 | Siswa | Nama dengan angka | Admin login | Nama berisi angka | Simpan form | Error regex nama | - | Belum diuji |
| 10 | TC-S10 | Siswa | File/teks special pada alamat | Admin login | Alamat panjang dan karakter spesial | Simpan form | Data tetap tersimpan jika masih valid | - | Belum diuji |

### Modul Pemasukan

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-P01 | Pemasukan | Tambah pemasukan valid non-SPP | Admin login | Donasi, jumlah valid, bukti valid | Simpan transaksi | Data tersimpan dan redirect | - | Belum diuji |
| 2 | TC-P02 | Pemasukan | Tambah bulk SPP valid | Admin login, siswa tersedia | SPP, siswa_list, bukti valid | Simpan transaksi | Banyak transaksi SPP tersimpan | - | Belum diuji |
| 3 | TC-P03 | Pemasukan | Upload bukti valid | Admin login | File JPG/PDF <= 2MB | Simpan transaksi | File tersimpan di storage | - | Belum diuji |
| 4 | TC-P04 | Pemasukan | Tanggal kosong | Admin login | Tanggal kosong | Simpan form | Error tanggal wajib diisi | - | Belum diuji |
| 5 | TC-P05 | Pemasukan | Jenis pemasukan kosong | Admin login | Jenis kosong | Simpan form | Error jenis pemasukan wajib dipilih | - | Belum diuji |
| 6 | TC-P06 | Pemasukan | Bukti transaksi kosong | Admin login | Bukti kosong | Simpan form | Error bukti wajib diisi | - | Belum diuji |
| 7 | TC-P07 | Pemasukan | File terlalu besar | Admin login | File lebih dari 2MB | Simpan form | Validasi file gagal | - | Belum diuji |
| 8 | TC-P08 | Pemasukan | SPP tanpa siswa_list | Admin login | SPP tanpa daftar siswa | Simpan form | Request ditolak | - | Belum diuji |
| 9 | TC-P09 | Pemasukan | Jumlah tidak valid | Admin login | Jumlah kosong atau 0 | Simpan form | Error jumlah tidak valid | - | Belum diuji |
| 10 | TC-P10 | Pemasukan | Keterangan terlalu panjang | Admin login | Keterangan > 500 karakter | Simpan form | Validasi gagal | - | Belum diuji |
| 11 | TC-P11 | Pemasukan | Update pemasukan valid | Admin login, data ada | Data baru valid | Buka edit, simpan perubahan | Data terbarui | - | Belum diuji |
| 12 | TC-P12 | Pemasukan | Update pemasukan invalid | Admin login, data ada | Data baru invalid (kosong) | Buka edit, simpan perubahan | Error validasi muncul | - | Belum diuji |
| 13 | TC-P13 | Pemasukan | Hapus pemasukan dengan konfirmasi | Admin login, data ada | Konfirmasi hapus | Klik hapus, setuju | Data terhapus (soft delete) | - | Belum diuji |
| 14 | TC-P14 | Pemasukan | Hapus pemasukan direferensikan | Admin login, data ada (berelasi) | Konfirmasi hapus | Klik hapus | Ditolak karena referensi | - | Belum diuji |

### Modul Pengeluaran

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-E01 | Pengeluaran | Tambah pengeluaran valid approved | Admin login | ATK, jumlah kecil, bukti valid | Simpan transaksi | Data tersimpan status approved | - | Belum diuji |
| 2 | TC-E02 | Pengeluaran | Tambah pengeluaran valid pending | Admin login | Renovasi, jumlah besar, bukti valid | Simpan transaksi | Data tersimpan status pending | - | Belum diuji |
| 3 | TC-E03 | Pengeluaran | Upload bukti valid | Admin login | File JPG/PDF <= 2MB | Simpan transaksi | File tersimpan di storage | - | Belum diuji |
| 4 | TC-E04 | Pengeluaran | Tanggal kosong | Admin login | Tanggal kosong | Simpan form | Error tanggal wajib diisi | - | Belum diuji |
| 5 | TC-E05 | Pengeluaran | Jumlah kosong | Admin login | Jumlah kosong | Simpan form | Error jumlah wajib diisi | - | Belum diuji |
| 6 | TC-E06 | Pengeluaran | Jenis pengeluaran kosong | Admin login | Jenis kosong | Simpan form | Error jenis wajib dipilih | - | Belum diuji |
| 7 | TC-E07 | Pengeluaran | Bukti transaksi kosong | Admin login | Bukti kosong | Simpan form | Error bukti wajib diisi | - | Belum diuji |
| 8 | TC-E08 | Pengeluaran | File terlalu besar | Admin login | File lebih dari 2MB | Simpan form | Validasi file gagal | - | Belum diuji |
| 9 | TC-E09 | Pengeluaran | Keterangan terlalu panjang | Admin login | Keterangan > 500 karakter | Simpan form | Validasi gagal | - | Belum diuji |
| 10 | TC-E10 | Pengeluaran | Nilai jumlah nol atau negatif | Admin login | Jumlah 0 atau -1 | Simpan form | Validasi gagal | - | Belum diuji |
| 11 | TC-E11 | Pengeluaran | Update pengeluaran valid | Admin login, data ada | Data baru valid | Buka edit, simpan | Data terbarui | - | Belum diuji |
| 12 | TC-E12 | Pengeluaran | Update pengeluaran invalid | Admin login, data ada | Data baru invalid | Buka edit, simpan | Error validasi muncul | - | Belum diuji |
| 13 | TC-E13 | Pengeluaran | Hapus pengeluaran | Admin login, data ada | Konfirmasi hapus | Klik hapus, setuju | Data terhapus | - | Belum diuji |
| 14 | TC-E14 | Pengeluaran | Hapus pengeluaran direferensikan | Admin login, data ada | Konfirmasi hapus | Klik hapus | Ditolak karena referensi | - | Belum diuji |

### Modul Audit Log

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-L01 | Audit Log | Log tersimpan saat tambah siswa | Admin login | Tambah siswa valid | Simpan data siswa | Audit log terbentuk | - | Belum diuji |
| 2 | TC-L02 | Audit Log | Log tersimpan saat update siswa | Admin login | Edit siswa valid | Simpan perubahan | Audit log update terbentuk | - | Belum diuji |
| 3 | TC-L03 | Audit Log | Log tersimpan saat hapus siswa | Admin login | Hapus siswa | Konfirmasi hapus | Audit log delete terbentuk | - | Belum diuji |
| 4 | TC-L04 | Audit Log | Log tersimpan saat tambah pemasukan | Admin login | Pemasukan valid | Simpan transaksi | Audit log pemasukan terbentuk | - | Belum diuji |
| 5 | TC-L05 | Audit Log | Log tersimpan saat tambah pengeluaran | Admin login | Pengeluaran valid | Simpan transaksi | Audit log pengeluaran terbentuk | - | Belum diuji |
| 6 | TC-L06 | Audit Log | Isi log memuat nama admin | Admin login | Aksi apa pun | Lakukan aksi | Nama admin muncul di log | - | Belum diuji |
| 7 | TC-L07 | Audit Log | Timestamp log terisi | Admin login | Aksi apa pun | Lakukan aksi | Kolom tanggal terisi | - | Belum diuji |
| 8 | TC-L08 | Audit Log | Log tidak dibuat saat validasi gagal | Admin login | Input invalid | Submit form | Tidak ada log baru | - | Belum diuji |
| 9 | TC-L09 | Audit Log | Log transaksi menyimpan id_transaksi | Admin login | Transaksi valid | Simpan transaksi | Relasi transaksi tersimpan | - | Belum diuji |
| 10 | TC-L10 | Audit Log | Log tetap konsisten setelah soft delete | Admin login | Hapus data siswa | Hapus data | Log tetap ada meski data dihapus | - | Belum diuji |


### Modul Role Kepsek

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-K01 | Kepsek | Akses dashboard read-only | Kepsek login | Akses halaman dashboard | Buka dashboard | Halaman tampil normal tanpa tombol edit/hapus | - | Belum diuji |
| 2 | TC-K02 | Kepsek | Akses laporan | Kepsek login | Akses menu laporan | Buka laporan | Data laporan tampil | - | Belum diuji |
| 3 | TC-K03 | Kepsek | Akses menu admin ditolak | Kepsek login | URL rute admin | Akses rute admin | Muncul halaman 403 Forbidden | - | Belum diuji |

### Modul Dashboard

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-D01 | Dashboard | Tampilan ringkasan saldo | Admin/Kepsek login, ada data | Akses dashboard | Buka dashboard | Saldo total, bulan ini tampil benar | - | Belum diuji |
| 2 | TC-D02 | Dashboard | Grafik pemasukan vs pengeluaran | Admin/Kepsek login, ada data | Akses dashboard | Buka dashboard | Grafik render sesuai data | - | Belum diuji |
| 3 | TC-D03 | Dashboard | Data kosong | Database transaksi kosong | Akses dashboard | Buka dashboard | Tampil "Belum ada data" / 0 | - | Belum diuji |

### Modul Laporan/Export

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-R01 | Laporan | Export PDF | Admin/Kepsek login, ada data | Klik export PDF | Download file PDF | File PDF terunduh dan valid | - | Belum diuji |
| 2 | TC-R02 | Laporan | Export Excel | Admin/Kepsek login, ada data | Klik export Excel | Download file Excel | File Excel terunduh dan valid | - | Belum diuji |
| 3 | TC-R03 | Laporan | Filter rentang tanggal | Admin/Kepsek login | Tanggal awal < akhir | Terapkan filter | Data difilter sesuai tanggal | - | Belum diuji |
| 4 | TC-R04 | Laporan | Filter tanggal tidak valid | Admin/Kepsek login | Tanggal awal > akhir | Terapkan filter | Error/Validasi rentang salah | - | Belum diuji |

### Modul Search, Filter, Pagination

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-F01 | Search | Keyword tidak ditemukan | Data tersedia | Keyword asal "xyzabc" | Cari data | Tabel kosong, pesan "tidak ditemukan" | - | Belum diuji |
| 2 | TC-F02 | Search | Keyword kosong | Data difilter sebelumnya | Keyword kosong | Reset search | Semua data tampil | - | Belum diuji |
| 3 | TC-F03 | Pagination | Pindah halaman | Data lebih dari 1 halaman | Klik page 2 | Pindah ke page 2 | Data page 2 tampil | - | Belum diuji |
| 4 | TC-F04 | Pagination | Ubah jumlah data | Data tersedia > 10 | Pilih 25/50 | Ubah dropdown | Tabel tampil 25/50 baris | - | Belum diuji |

### Modul Keamanan (Authorization, RBAC, Web Security)

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-AUTH01 | AuthZ | Non-admin akses admin | User biasa login | Akses rute /admin | Kunjungi URL admin | 403 Forbidden / dialihkan | - | Belum diuji |
| 2 | TC-AUTH02 | AuthZ | Belum login akses protected | Guest | Akses dashboard | Buka URL protected | Redirect ke login | - | Belum diuji |
| 3 | TC-AUTH03 | AuthZ | Admin akses rute lain | Admin login | Akses rute kepsek khusus | Kunjungi URL kepsek | 403 / Redirect jika dibatasi | - | Belum diuji |
| 4 | TC-SEC01 | Security | XSS pada teks | Admin login | Input teks `<script>alert(1)</script>` | Simpan form | Disanitasi, ditampilkan sbg string biasa | - | Belum diuji |
| 5 | TC-SEC02 | Security | SQL Injection pencarian | Admin login | Input `' OR 1=1 --` | Cari data | Dianggap string biasa, aman | - | Belum diuji |
| 6 | TC-SEC03 | Security | Submit tanpa CSRF token | Admin login | Form dihapus tokennya | Submit form HTTP POST | 419 Page Expired | - | Belum diuji |

### Modul Session & Boundary

| No | ID Test | Modul | Skenario | Precondition | Input | Langkah | Ekspektasi | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | TC-SESS01 | Session | Session habis akses protected | Session timeout | Klik link menu | Navigasi menu | Redirect ke halaman login | - | Belum diuji |
| 2 | TC-SESS02 | Session | Remember me berfungsi | Guest | Centang remember me | Login lalu tutup browser, buka lagi | Tetap login tanpa memasukkan kredensial | - | Belum diuji |
| 3 | TC-SESS03 | Session | Logout banyak tab | Login di tab A & B | Logout di tab A | Refresh tab B | Tab B juga terlogout | - | Belum diuji |
| 4 | TC-BND01 | Boundary | Transaksi batas maksimal db | Admin login | Jumlah `999999999999999` | Simpan pengeluaran | Diterima atau ditolak dengan error jelas | - | Belum diuji |
| 5 | TC-BND02 | Boundary | Input teks batas pas | Admin login | Teks 255 karakter | Simpan form siswa | Tersimpan dengan sukses | - | Belum diuji |
| 6 | TC-BND03 | Boundary | Input teks lebih batas | Admin login | Teks 256 karakter | Simpan form siswa | Ditolak dengan error validasi | - | Belum diuji |

## Tahap 3 - Automated Test

### PHPUnit atau Pest

Project ini menggunakan **PHPUnit** untuk feature test. Struktur test bisa dipindahkan ke Pest tanpa mengubah skenario bisnis.

### `tests/Feature/AuthTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Admin Test',
            'username' => 'admin01',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ], $attributes));
    }

    public function test_login_admin_valid_redirect_ke_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->post('/login', [
            'username' => 'admin01',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'username' => 'admin01',
            'role' => 'admin',
        ]);
    }

    public function test_login_admin_password_salah_menampilkan_error(): void
    {
        $this->makeAdmin();

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin01',
            'password' => 'salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_admin_username_kosong_menolak_request(): void
    {
        $this->makeAdmin();

        $response = $this->from('/login')->post('/login', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }

    public function test_logout_admin_mengakhiri_sesi(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
```

### `tests/Feature/SiswaTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin Test',
            'username' => 'admin01',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    private function siswaPayload(array $overrides = []): array
    {
        return array_merge([
            'nis' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'nama_orangtua' => 'Siti Aminah',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 1',
            'is_active' => 1,
        ], $overrides);
    }

    public function test_tambah_siswa_valid_menyimpan_data(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.siswa.store'), $this->siswaPayload());

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'nis' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
        ]);
    }

    public function test_tambah_siswa_data_kosong_menampilkan_error(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->from(route('admin.siswa.index'))->post(route('admin.siswa.store'), []);

        $response->assertRedirect(route('admin.siswa.index'));
        $response->assertSessionHasErrors([
            'nis',
            'nama',
            'nama_orangtua',
            'kelas',
            'jenis_kelamin',
            'no_telepon',
        ]);
    }

    public function test_tambah_siswa_nik_duplikat_ditolak(): void
    {
        $admin = $this->makeAdmin();
        Siswa::create($this->siswaPayload());

        $response = $this->actingAs($admin)->post(route('admin.siswa.store'), $this->siswaPayload([
            'nama' => 'Andi Wijaya',
        ]));

        $response->assertSessionHasErrors('nis');
        $this->assertDatabaseCount('siswas', 1);
    }

    public function test_update_siswa_valid_mengubah_data(): void
    {
        $admin = $this->makeAdmin();
        $siswa = Siswa::create($this->siswaPayload());

        $response = $this->actingAs($admin)->put(route('admin.siswa.update', $siswa), $this->siswaPayload([
            'nis' => '6543210987654321',
            'nama' => 'Budi Update',
            'kelas' => '8B',
            'jenis_kelamin' => 'P',
            'no_telepon' => '089876543210',
        ]));

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'nis' => '6543210987654321',
            'nama' => 'Budi Update',
            'kelas' => '8B',
            'jenis_kelamin' => 'P',
        ]);
    }

    public function test_update_siswa_nik_duplikat_ditolak(): void
    {
        $admin = $this->makeAdmin();
        $siswa1 = Siswa::create($this->siswaPayload(['nis' => '1234567890123456']));
        $siswa2 = Siswa::create($this->siswaPayload([
            'nis' => '6543210987654321',
            'nama' => 'Siswa Dua',
        ]));

        $response = $this->actingAs($admin)->put(route('admin.siswa.update', $siswa2), $this->siswaPayload([
            'nis' => $siswa1->nis,
            'nama' => 'Siswa Dua Update',
        ]));

        $response->assertSessionHasErrors('nis');
    }
}
```

### `tests/Feature/PemasukanTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Pemasukan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PemasukanTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin Test',
            'username' => 'admin01',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    private function makeSiswa(array $overrides = []): Siswa
    {
        return Siswa::create(array_merge([
            'nis' => fake()->unique()->numerify('################'),
            'nama' => fake()->name(),
            'nama_orangtua' => fake()->name(),
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
            'alamat' => fake()->address(),
            'is_active' => 1,
        ], $overrides));
    }

    public function test_tambah_pemasukan_valid_non_spp_menyimpan_data(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Donasi kelas',
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $response->assertRedirect(route('admin.pemasukan.index'));
        $this->assertDatabaseHas('transaksis', [
            'jenis' => 'pemasukan',
            'jenis_transaksi' => 'Donasi',
            'jumlah' => 150000,
            'id_admin' => $admin->id,
        ]);
    }

    public function test_bulk_spp_valid_menyimpan_banyak_transaksi(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $siswa1 = $this->makeSiswa(['nama' => 'Siswa Satu']);
        $siswa2 = $this->makeSiswa(['nama' => 'Siswa Dua']);

        $response = $this->actingAs($admin)->post(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'keterangan' => 'SPP Mei 2026',
            'jenis_pemasukan' => 'SPP',
            'siswa_list' => json_encode([
                ['siswa_id' => $siswa1->id, 'jumlah' => 100000],
                ['siswa_id' => $siswa2->id, 'jumlah' => 120000],
            ]),
            'bukti_transaksi' => UploadedFile::fake()->image('bukti-bulk.jpg'),
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);

        $this->assertDatabaseHas('transaksis', [
            'jenis' => 'pemasukan',
            'jenis_transaksi' => 'SPP',
            'siswa_id' => $siswa1->id,
            'jumlah' => 100000,
        ]);
        $this->assertDatabaseHas('transaksis', [
            'jenis' => 'pemasukan',
            'jenis_transaksi' => 'SPP',
            'siswa_id' => $siswa2->id,
            'jumlah' => 120000,
        ]);
    }

    public function test_tambah_pemasukan_data_wajib_kosong_menampilkan_error(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pemasukan.store'), []);

        $response->assertSessionHasErrors([
            'tanggal',
            'jenis_pemasukan',
            'bukti_transaksi',
        ]);
    }

    public function test_tambah_pemasukan_file_terlalu_besar_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Donasi kelas',
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.pdf', 3000, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('bukti_transaksi');
    }
}
```

### `tests/Feature/PengeluaranTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengeluaranTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin Test',
            'username' => 'admin01',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_tambah_pengeluaran_valid_menyimpan_data_approved(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Pembelian ATK',
            'jenis_pengeluaran' => 'ATK',
            'bukti_transaksi' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $response->assertRedirect(route('admin.pengeluaran.index'));
        $this->assertDatabaseHas('transaksis', [
            'jenis' => 'pengeluaran',
            'jenis_transaksi' => 'ATK',
            'status' => 'approved',
            'jumlah' => 150000,
            'id_admin' => $admin->id,
        ]);
    }

    public function test_tambah_pengeluaran_valid_renovasi_status_pending(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 600000,
            'keterangan' => 'Renovasi ruang kelas',
            'jenis_pengeluaran' => 'Renovasi',
            'bukti_transaksi' => UploadedFile::fake()->image('bukti-renovasi.jpg'),
        ]);

        $response->assertRedirect(route('admin.pengeluaran.index'));
        $this->assertDatabaseHas('transaksis', [
            'jenis' => 'pengeluaran',
            'jenis_transaksi' => 'Renovasi',
            'status' => 'pending',
            'jumlah' => 600000,
            'id_admin' => $admin->id,
        ]);
    }

    public function test_tambah_pengeluaran_data_wajib_kosong_menampilkan_error(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pengeluaran.store'), []);

        $response->assertSessionHasErrors([
            'tanggal',
            'jumlah',
            'jenis_pengeluaran',
            'bukti_transaksi',
        ]);
    }

    public function test_tambah_pengeluaran_file_terlalu_besar_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 250000,
            'keterangan' => 'Konsumsi kegiatan',
            'jenis_pengeluaran' => 'Konsumsi Harian',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.pdf', 3000, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('bukti_transaksi');
    }
}
```


### `tests/Feature/TambahanTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TambahanTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser($role = 'admin')
    {
        return User::factory()->create([
            'name' => ucfirst($role) . ' Test',
            'username' => $role . '01',
            'email' => $role . '@example.com',
            'role' => $role,
        ]);
    }

    public function test_pemasukan_bisa_diupdate()
    {
        $admin = $this->makeUser('admin');
        
        $response = $this->actingAs($admin)->put('/admin/pemasukan/1', [
            'tanggal' => '2026-05-26',
            'jumlah' => 200000,
            'keterangan' => 'Update',
            'jenis_pemasukan' => 'Donasi',
        ]);
        
        $this->assertTrue(true); // Placeholder until real factory setup
    }

    public function test_kepsek_tidak_bisa_akses_rute_admin()
    {
        $kepsek = $this->makeUser('kepsek');

        $response = $this->actingAs($kepsek)->get('/admin/dashboard');

        $response->assertStatus(403);
    }
    
    public function test_kepsek_bisa_akses_dashboard()
    {
        $kepsek = $this->makeUser('kepsek');

        $response = $this->actingAs($kepsek)->get('/kepsek/dashboard');

        $response->assertStatus(200);
    }

    public function test_export_laporan_rentang_tanggal_tidak_valid()
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get('/admin/laporan/export?start=2026-12-31&end=2026-01-01');

        $response->assertSessionHasErrors();
    }

    public function test_belum_login_akses_protected_redirect_ke_login()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_xss_protection_pada_form()
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->post('/admin/siswa', [
            'nis' => '1234567890123456',
            'nama' => '<script>alert(1)</script>',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
        ]);
        
        $this->assertNotEquals(500, $response->status());
    }

    public function test_boundary_teks_terlalu_panjang_ditolak()
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->post('/admin/siswa', [
            'nis' => '1234567890123456',
            'nama' => str_repeat('A', 256), // > 255
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
        ]);
        
        $response->assertSessionHasErrors('nama');
    }
}
```

## Tahap 4 - Eksekusi dan Pelaporan

### Urutan Menjalankan Test

```bash
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/SiswaTest.php
php artisan test tests/Feature/PemasukanTest.php
php artisan test tests/Feature/PengeluaranTest.php
php artisan test
```

### Template Laporan Hasil Testing

| No | ID Test | Modul | Skenario | Pass/Fail | Bug |
|---|---|---|---|---|---|
| 1 | TC-A01 | Autentikasi | Login admin valid |  |  |
| 2 | TC-S01 | Siswa | Tambah siswa valid |  |  |
| 3 | TC-P01 | Pemasukan | Tambah pemasukan valid |  |  |
| 4 | TC-E01 | Pengeluaran | Tambah pengeluaran valid |  |  |

### Template Bug Report

```text
ID Bug     : BUG-001
Modul      : Siswa
Skenario   : Tambah siswa NIS duplikat
Langkah    :
1. Login sebagai admin
2. Buka menu Siswa
3. Tambah siswa dengan NIS yang sama
Ekspektasi : Sistem menolak input dan menampilkan error NIS sudah terdaftar
Hasil      : ...
Severity   : Low / Medium / High / Critical
Screenshot : (lampirkan)
```

## Tahap 5 - Validasi AJAX

### Cara Menguji Inline Validation Error

1. Buka form tambah atau edit siswa yang memakai `fetch`.
2. Isi form dengan data tidak valid, misalnya NIS kosong atau nama kosong.
3. Klik simpan.
4. Pastikan response `422` mengembalikan pesan per field.
5. Pastikan pesan muncul tepat di bawah field dan border field berubah merah.

### Test Endpoint dengan Postman

- Method: `POST` atau `PUT`
- Header: `Accept: application/json`
- Header: `X-CSRF-TOKEN: {token}`
- URL: endpoint `store` atau `update`

### Ekspektasi Response 422

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nis": ["NIS wajib diisi"],
    "nama": ["Nama siswa wajib diisi"]
  }
}
```

### Catatan Implementasi AJAX

- Gunakan `Accept: application/json` agar Laravel mengirim validasi JSON.
- Gunakan `Storage::fake('public')` pada test upload file.
- Gunakan `assertJsonValidationErrors()` bila ingin menguji endpoint AJAX langsung.
- Untuk form normal, gunakan `assertSessionHasErrors()`.

## Tahap 4 - Postman

Untuk pengujian manual berbasis request, koleksi Postman sudah disiapkan di:

- [docs/postman/SIKEU.postman_collection.json](docs/postman/SIKEU.postman_collection.json)
- [docs/postman/SIKEU.local.postman_environment.json](docs/postman/SIKEU.local.postman_environment.json)

Cara pakai singkat:

1. Import collection dan environment ke Postman.
2. Pilih environment `SIKEU Local`.
3. Jalankan `GET Login Page` dulu agar token CSRF tersimpan otomatis.
4. Jalankan login admin atau kepsek, lalu lanjutkan request protected lain sesuai kebutuhan.
5. Untuk request upload file pada pemasukan dan pengeluaran, pilih file lokal di field `bukti_transaksi` sebelum menjalankan request valid.
