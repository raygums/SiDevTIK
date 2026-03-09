<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test; // <-- Wajib diimpor agar #[Test] dikenali
use Tests\TestCase;

class AuthTest extends TestCase
{
    // Membersihkan database virtual setiap kali test jalan untuk menjaga isolasi data
    use RefreshDatabase;

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        // 1. Persiapan Data (Arrange): Buat user dengan kredensial valid
        $user = User::factory()->create([
            'email' => 'firman@test.com',
            'password' => 'password123', 
            'role' => UserRole::USER,
        ]);

        // 2. Eksekusi (Act): Lakukan request POST ke endpoint login
        $response = $this->post(route('login.store'), [
            'email' => 'firman@test.com',
            'password' => 'password123',
        ]);

        // 3. Verifikasi (Assert): Pastikan sukses redirect ke dashboard dan user terautentikasi
        $response->assertRedirect(route('dashboard')); 
        $this->assertAuthenticatedAs($user); 
    }

    #[Test]
    public function user_cannot_login_with_wrong_password()
    {
        // 1. Persiapan Data (Arrange): Buat user target
        $user = User::factory()->create([
            'email' => 'hacker@test.com',
            'password' => 'password123',
        ]);

        // 2. Eksekusi (Act): Coba login dengan kombinasi password yang salah
        $response = $this->post(route('login.store'), [
            'email' => 'hacker@test.com',
            'password' => 'salah123',
        ]);

        // 3. Verifikasi (Assert): Pastikan sistem menolak, mengembalikan error, dan status tetap Guest
        $response->assertSessionHasErrors('email'); 
        $this->assertGuest(); 
    }

    #[Test]
    public function admin_middleware_blocks_regular_user()
    {
        // 1. Persiapan Data & Eksekusi (Arrange & Act): Login paksa sebagai User Biasa
        $user = User::factory()->create(['role' => UserRole::USER]);
        $this->actingAs($user);

        // 2. Eksekusi lanjutan: Akses halaman yang dilindungi middleware Admin
        $response = $this->get(route('admin.users'));

        // 3. Verifikasi (Assert): Pastikan sistem melempar status 403 (Forbidden)
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_middleware_allows_admin_user()
    {
        // 1. Persiapan Data & Eksekusi (Arrange & Act): Login paksa sebagai Admin
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin);

        // 2. Eksekusi lanjutan: Akses halaman khusus Admin
        $response = $this->get(route('admin.users'));

        // 3. Verifikasi (Assert): Pastikan halaman berhasil dimuat dengan status 200 (OK)
        $response->assertStatus(200);
    }
}
