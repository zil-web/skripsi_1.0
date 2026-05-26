<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function postWithCsrf(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        $token = csrf_token();

        return $this->withSession(['_token' => $token])->post($uri, array_merge([
            '_token' => $token,
        ], $data));
    }

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

        $response = $this->postWithCsrf('/login', [
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

        $response = $this->from('/login')->postWithCsrf('/login', [
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

        $response = $this->from('/login')->postWithCsrf('/login', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
    }

    public function test_login_admin_password_kosong_menampilkan_error(): void
    {
        $this->makeAdmin();

        $response = $this->from('/login')->postWithCsrf('/login', [
            'username' => 'admin01',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('password');
    }

    public function test_login_karakter_spesial_ditolak(): void
    {
        $this->makeAdmin();

        $response = $this->from('/login')->postWithCsrf('/login', [
            'username' => '<script>alert(1)</script>',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $this->assertNotEquals(url('/admin/dashboard'), $response->headers->get('Location'));
    }

    public function test_login_non_admin_ditolak(): void
    {
        $user = User::factory()->create([
            'name' => 'User Biasa',
            'username' => 'user01',
            'email' => 'user@example.com',
            'role' => 'kepsek',
        ]);

        $response = $this->from('/login')->postWithCsrf('/login', [
            'username' => 'user01',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $this->assertNotEquals(url('/admin/dashboard'), $response->headers->get('Location'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'user01',
            'role' => 'kepsek',
        ]);
    }

    public function test_login_session_aktif_dashboard_tampil(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_akses_login_saat_sudah_login_dialihkan(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect();
        $this->assertNotEquals(url('/login'), $response->headers->get('Location'));
    }

    public function test_logout_admin_mengakhiri_sesi(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $response = $this->postWithCsrf('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}