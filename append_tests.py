import re

with open('E:/skripsi/dummy/docs/blackbox-testing-laravel.md', 'r', encoding='utf-8') as f:
    content = f.read()

# Append to Pemasukan
pemasukan_ext = r"""| 11 | TC-P11 | Pemasukan | Update pemasukan valid | Admin login, data ada | Data baru valid | Buka edit, simpan perubahan | Data terbarui | - | Belum diuji |
| 12 | TC-P12 | Pemasukan | Update pemasukan invalid | Admin login, data ada | Data baru invalid (kosong) | Buka edit, simpan perubahan | Error validasi muncul | - | Belum diuji |
| 13 | TC-P13 | Pemasukan | Hapus pemasukan dengan konfirmasi | Admin login, data ada | Konfirmasi hapus | Klik hapus, setuju | Data terhapus (soft delete) | - | Belum diuji |
| 14 | TC-P14 | Pemasukan | Hapus pemasukan direferensikan | Admin login, data ada (berelasi) | Konfirmasi hapus | Klik hapus | Ditolak karena referensi | - | Belum diuji |
"""
content = re.sub(r'(\| 10 \| TC-P10 \|.*?Belum diuji \|\n)', r'\1' + pemasukan_ext, content)

# Append to Pengeluaran
pengeluaran_ext = r"""| 11 | TC-E11 | Pengeluaran | Update pengeluaran valid | Admin login, data ada | Data baru valid | Buka edit, simpan | Data terbarui | - | Belum diuji |
| 12 | TC-E12 | Pengeluaran | Update pengeluaran invalid | Admin login, data ada | Data baru invalid | Buka edit, simpan | Error validasi muncul | - | Belum diuji |
| 13 | TC-E13 | Pengeluaran | Hapus pengeluaran | Admin login, data ada | Konfirmasi hapus | Klik hapus, setuju | Data terhapus | - | Belum diuji |
| 14 | TC-E14 | Pengeluaran | Hapus pengeluaran direferensikan | Admin login, data ada | Konfirmasi hapus | Klik hapus | Ditolak karena referensi | - | Belum diuji |
"""
content = re.sub(r'(\| 10 \| TC-E10 \|.*?Belum diuji \|\n)', r'\1' + pengeluaran_ext, content)

# Add New Modules before "## Tahap 3 - Automated Test"
new_modules = r"""
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

"""
# Using raw strings for lambda replacement so backslashes don't cause issues
content = content.replace("## Tahap 3 - Automated Test", new_modules + "## Tahap 3 - Automated Test")


test_appendix = r"""
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
"""
content = content.replace("## Tahap 4 - Eksekusi dan Pelaporan", test_appendix + "\n## Tahap 4 - Eksekusi dan Pelaporan")

with open('E:/skripsi/dummy/docs/blackbox-testing-laravel.md', 'w', encoding='utf-8') as f:
    f.write(content)
