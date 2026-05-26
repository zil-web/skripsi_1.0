<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function postWithCsrf(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        $token = csrf_token();

        return $this->withSession(['_token' => $token])->post($uri, array_merge([
            '_token' => $token,
        ], $data));
    }

    private function putWithCsrf(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        $token = csrf_token();

        return $this->withSession(['_token' => $token])->put($uri, array_merge([
            '_token' => $token,
        ], $data));
    }

    private function deleteWithCsrf(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        $token = csrf_token();

        return $this->withSession(['_token' => $token])->delete($uri, array_merge([
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

    private function siswaPayload(array $overrides = []): array
    {
        return array_merge([
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'nama_orangtua' => 'Siti Aminah',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 1',
            'is_active' => 1,
        ], $overrides);
    }

    private function pemasukanPayload(array $overrides = []): array
    {
        return array_merge([
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Donasi kelas',
            'jenis_pemasukan' => 'Donasi',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ], $overrides);
    }

    private function pengeluaranPayload(array $overrides = []): array
    {
        return array_merge([
            'tanggal' => '2026-05-26',
            'jumlah' => 150000,
            'keterangan' => 'Pembelian ATK',
            'jenis_pengeluaran' => 'ATK',
            'bukti_transaksi' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ], $overrides);
    }

    public function test_log_terbentuk_saat_tambah_siswa(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload());

        $response->assertRedirect(route('admin.siswa.index'));

        $log = AuditLog::latest('id')->first();
        $this->assertNotNull($log);
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
        ]);
        $this->assertStringContainsString('menambahkan data siswa', $log->aktivitas);
        $this->assertStringContainsString($admin->name, $log->aktivitas);
    }

    public function test_log_terbentuk_saat_update_siswa(): void
    {
        $admin = $this->makeAdmin();
        $siswa = Siswa::create($this->siswaPayload());

        $this->actingAs($admin);
        $response = $this->putWithCsrf(route('admin.siswa.update', $siswa), $this->siswaPayload([
            'nama' => 'Budi Update',
            'nik' => '6543210987654321',
        ]));

        $response->assertRedirect(route('admin.siswa.index'));

        $log = AuditLog::latest('id')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('mengubah data siswa', $log->aktivitas);
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
        ]);
    }

    public function test_log_terbentuk_saat_hapus_siswa(): void
    {
        $admin = $this->makeAdmin();
        $siswa = Siswa::create($this->siswaPayload());

        $this->actingAs($admin);
        $response = $this->deleteWithCsrf(route('admin.siswa.destroy', $siswa));

        $response->assertRedirect(route('admin.siswa.index'));

        $log = AuditLog::latest('id')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('menghapus data siswa', $log->aktivitas);
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
        ]);
    }

    public function test_log_terbentuk_saat_tambah_pemasukan(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pemasukan.store'), $this->pemasukanPayload());

        $response->assertRedirect(route('admin.pemasukan.index'));

        $pemasukan = Pemasukan::latest('id')->first();
        $log = AuditLog::latest('id')->first();

        $this->assertNotNull($pemasukan);
        $this->assertNotNull($log);
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
            'id_transaksi' => $pemasukan->id,
        ]);
        $this->assertStringContainsString('menambahkan transaksi pemasukan', $log->aktivitas);
    }

    public function test_log_terbentuk_saat_tambah_pengeluaran(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.pengeluaran.store'), $this->pengeluaranPayload());

        $response->assertRedirect(route('admin.pengeluaran.index'));

        $pengeluaran = Pengeluaran::latest('id')->first();
        $log = AuditLog::latest('id')->first();

        $this->assertNotNull($pengeluaran);
        $this->assertNotNull($log);
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
            'id_transaksi' => $pengeluaran->id,
        ]);
        $this->assertStringContainsString('menambahkan pengeluaran', $log->aktivitas);
    }

    public function test_log_memuat_nama_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload());

        $log = AuditLog::latest('id')->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString($admin->name, $log->aktivitas);
    }

    public function test_log_timestamp_terisi(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload());

        $log = AuditLog::latest('id')->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->created_at);
        $this->assertNotNull($log->tanggal);
    }

    public function test_tidak_ada_log_saat_validasi_gagal(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), []);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_log_menyimpan_id_transaksi(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $this->postWithCsrf(route('admin.pemasukan.store'), $this->pemasukanPayload());

        $pemasukan = Pemasukan::latest('id')->first();

        $this->assertNotNull($pemasukan);
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
            'id_transaksi' => $pemasukan->id,
        ]);
    }

    public function test_log_tetap_ada_setelah_soft_delete(): void
    {
        $admin = $this->makeAdmin();
        $siswa = Siswa::create($this->siswaPayload());

        $this->actingAs($admin);
        $response = $this->deleteWithCsrf(route('admin.siswa.destroy', $siswa));

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseHas('audit_logs', [
            'id_admin' => $admin->id,
        ]);
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);
    }
}