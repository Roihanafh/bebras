<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function buatUser(string $namaRole): User
    {
        $role = Role::firstOrCreate(['name' => $namaRole]);

        $user = User::create([
            'name'     => ucfirst($namaRole) . ' Test',
            'username' => $namaRole . '_test_' . uniqid(),
            'email'    => $namaRole . '_test_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    // -----------------------------------------------------------------------
    // Test: login biro → redirect ke berita.index
    // Requirements: 2.1
    // -----------------------------------------------------------------------
    public function test_biro_login_redirect_ke_berita_index(): void
    {
        $biro = $this->buatUser('biro');

        $response = $this->post('/login', [
            'username' => $biro->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/berita');
    }

    /**
     * Setelah biro berhasil login, sesi harus ter-autentikasi sebagai user tersebut.
     * Requirements: 2.1
     */
    public function test_biro_login_sesi_terotentikasi(): void
    {
        $biro = $this->buatUser('biro');

        $this->post('/login', [
            'username' => $biro->username,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($biro);
    }

    // -----------------------------------------------------------------------
    // Test: role tidak dikenali → logout + redirect ke login dengan error
    // Requirements: 2.5
    // -----------------------------------------------------------------------

    /**
     * User tanpa role apapun yang berhasil login harus di-logout dan
     * diarahkan kembali ke halaman login dengan pesan error.
     * Requirements: 2.5
     */
    public function test_user_tanpa_role_redirect_ke_login_dengan_error(): void
    {
        // Buat user tanpa role sama sekali
        $userTanpaRole = User::create([
            'name'     => 'User Tanpa Role',
            'username' => 'tanpa_role_' . uniqid(),
            'email'    => 'tanpa_role_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $userTanpaRole->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    /**
     * User tanpa role harus di-logout (tidak terotentikasi) setelah percobaan login.
     * Requirements: 2.5
     */
    public function test_user_tanpa_role_tidak_terotentikasi_setelah_login(): void
    {
        $userTanpaRole = User::create([
            'name'     => 'User Tanpa Role',
            'username' => 'tanpa_role2_' . uniqid(),
            'email'    => 'tanpa_role2_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'username' => $userTanpaRole->username,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /**
     * User dengan role yang tidak dikenali sistem (bukan admin/biro/user) harus
     * di-logout dan diarahkan ke login dengan pesan error.
     * Requirements: 2.5
     */
    public function test_user_dengan_role_tidak_dikenali_redirect_ke_login(): void
    {
        $userRoleAneh = $this->buatUser('peran_tidak_dikenal');

        $response = $this->post('/login', [
            'username' => $userRoleAneh->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // Test: kredensial salah → kembali dengan error (sanity check)
    // -----------------------------------------------------------------------

    /**
     * Login dengan password salah harus mengembalikan error validasi.
     */
    public function test_login_password_salah_mengembalikan_error(): void
    {
        $biro = $this->buatUser('biro');

        $response = $this->post('/login', [
            'username' => $biro->username,
            'password' => 'password_salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
