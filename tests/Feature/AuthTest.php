<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // Membersihkan database virtual setiap kali test jalan
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        // 1. Buat user
        $user = User::factory()->create([
            'email' => 'firman@test.com',
            'password' => 'password123', 
            'role' => UserRole::USER,
        ]);

        // 2. Coba login
        $response = $this->post(route('login.store'), [
            'email' => 'firman@test.com',
            'password' => 'password123',
        ]);

        // 3. Pastikan sukses redirect ke dashboard
        $response->assertRedirect(route('dashboard')); 
        $this->assertAuthenticatedAs($user); 
    }

    /** @test */
    public function user_cannot_login_with_wrong_password()
    {
        // 1. Buat user
        $user = User::factory()->create([
            'email' => 'hacker@test.com',
            'password' => 'password123',
        ]);

        // 2. Login dengan password salah
        $response = $this->post(route('login.store'), [
            'email' => 'hacker@test.com',
            'password' => 'salah123',
        ]);

        // 3. Pastikan gagal (ada error di session)
        $response->assertSessionHasErrors('email'); 
        $this->assertGuest(); 
    }

    /** @test */
    public function admin_middleware_blocks_regular_user()
    {
        // 1. Login sebagai User Biasa
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);

        // 2. Akses halaman Admin
        $response = $this->get(route('admin.users'));

        // 3. Harap ditolak (403 Forbidden)
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_middleware_allows_admin_user()
    {
        // 1. Login sebagai Admin
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // 2. Akses halaman Admin
        $response = $this->get(route('admin.users'));

        // 3. Harap sukses (200 OK)
        $response->assertStatus(200);
    }
}