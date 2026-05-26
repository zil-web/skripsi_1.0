<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengeluaranTest extends TestCase
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

    public function test_tambah_pengeluaran_valid_menyimpan_data_approved(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Pembelian ATK',
            'jenis_pengeluaran' => 'ATK',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 600000,
            'keterangan' => 'Renovasi ruang kelas',
            'jenis_pengeluaran' => 'Renovasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti-renovasi.jpg', 100, 'image/jpeg'),
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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), []);

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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 250000,
            'keterangan' => 'Konsumsi kegiatan',
            'jenis_pengeluaran' => 'Konsumsi Harian',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.pdf', 3000, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('bukti_transaksi');
    }

    public function test_pengeluaran_keterangan_terlalu_panjang_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => str_repeat('a', 501),
            'jenis_pengeluaran' => 'ATK',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('keterangan');
    }

    public function test_pengeluaran_jumlah_nol_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => 0,
            'keterangan' => 'Pembelian ATK',
            'jenis_pengeluaran' => 'ATK',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('jumlah');
    }

    public function test_pengeluaran_jumlah_negatif_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), [
            'tanggal' => '2026-05-26',
            'jumlah' => -1,
            'keterangan' => 'Pembelian ATK',
            'jenis_pengeluaran' => 'ATK',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('jumlah');
    }
}