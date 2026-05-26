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

    private function postWithCsrf(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        $token = csrf_token();

        return $this->withSession(['_token' => $token])->post($uri, array_merge([
            '_token' => $token,
        ], $data));
    }

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
            'nik' => fake()->unique()->numerify('################'),
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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Donasi kelas',
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'keterangan' => 'SPP Mei 2026',
            'jenis_pemasukan' => 'SPP',
            'siswa_list' => json_encode([
                ['siswa_id' => $siswa1->id, 'jumlah' => 100000],
                ['siswa_id' => $siswa2->id, 'jumlah' => 120000],
            ]),
            'bukti_transaksi' => UploadedFile::fake()->create('bukti-bulk.jpg', 100, 'image/jpeg'),
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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), []);

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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Donasi kelas',
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.pdf', 3000, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('bukti_transaksi');
    }

    public function test_spp_tanpa_siswa_list_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 100000,
            'keterangan' => 'SPP tanpa siswa_list',
            'jenis_pemasukan' => 'SPP',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_pemasukan_jumlah_nol_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 0,
            'keterangan' => 'Donasi kelas',
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('jumlah');
    }

    public function test_keterangan_terlalu_panjang_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => str_repeat('a', 501),
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('keterangan');
    }
}