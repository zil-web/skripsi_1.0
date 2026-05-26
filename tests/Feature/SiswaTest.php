<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaTest extends TestCase
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

    public function test_tambah_siswa_valid_menyimpan_data(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload());

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'no_telepon' => '081234567890',
        ]);
    }

    public function test_tambah_siswa_data_kosong_menampilkan_error(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->from(route('admin.siswa.index'))->postWithCsrf(route('admin.siswa.store'), []);

        $response->assertRedirect(route('admin.siswa.index'));
        $response->assertSessionHasErrors([
            'nik',
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

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload([
            'nama' => 'Andi Wijaya',
        ]));

        $response->assertSessionHasErrors('nik');
        $this->assertDatabaseCount('siswas', 1);
    }

    public function test_tambah_siswa_nik_kurang_16_digit(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload([
            'nik' => '123456789012345',
        ]));

        $response->assertSessionHasErrors('nik');
    }

    public function test_tambah_siswa_nama_berisi_angka(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), $this->siswaPayload([
            'nama' => 'Budi123',
        ]));

        $response->assertSessionHasErrors('nama');
    }

    public function test_tambah_siswa_alamat_karakter_spesial_tersimpan(): void
    {
        $admin = $this->makeAdmin();
        $payload = $this->siswaPayload([
            'alamat' => 'Jl. Merdeka No.1 <RT/RW> 002/003 "Blok-A"',
        ]);

        $this->actingAs($admin);
        $response = $this->postWithCsrf(route('admin.siswa.store'), $payload);

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'nik' => '1234567890123456',
            'alamat' => 'Jl. Merdeka No.1 <RT/RW> 002/003 "Blok-A"',
        ]);
    }

    public function test_hapus_siswa_soft_delete(): void
    {
        $admin = $this->makeAdmin();
        $siswa = Siswa::create($this->siswaPayload());

        $this->actingAs($admin);
        $response = $this->delete(route('admin.siswa.destroy', $siswa));

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertSoftDeleted('siswas', [
            'id' => $siswa->id,
        ]);
    }

    public function test_update_siswa_valid_mengubah_data(): void
    {
        $admin = $this->makeAdmin();
        $siswa = Siswa::create($this->siswaPayload());

        $this->actingAs($admin);
        $response = $this->putWithCsrf(route('admin.siswa.update', $siswa), $this->siswaPayload([
            'nik' => '6543210987654321',
            'nama' => 'Budi Update',
            'kelas' => '8B',
            'jenis_kelamin' => 'P',
            'no_telepon' => '089876543210',
        ]));

        $response->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'nik' => '6543210987654321',
            'nama' => 'Budi Update',
            'kelas' => '8B',
            'jenis_kelamin' => 'P',
        ]);
    }

    public function test_update_siswa_nik_duplikat_ditolak(): void
    {
        $admin = $this->makeAdmin();
        $siswa1 = Siswa::create($this->siswaPayload(['nik' => '1234567890123456']));
        $siswa2 = Siswa::create($this->siswaPayload([
            'nik' => '6543210987654321',
            'nama' => 'Siswa Dua',
        ]));

        $this->actingAs($admin);
        $response = $this->putWithCsrf(route('admin.siswa.update', $siswa2), $this->siswaPayload([
            'nik' => $siswa1->nik,
            'nama' => 'Siswa Dua Update',
        ]));

        $response->assertSessionHasErrors('nik');
    }
}