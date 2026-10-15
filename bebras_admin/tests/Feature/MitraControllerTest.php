<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MitraControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createAdminUser(): User
    {
        $user = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test_' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->roles()->attach($role);
        return $user;
    }

    private function createUserRole(): User
    {
        $user = User::create([
            'name'     => 'User Test',
            'username' => 'user_test_' . uniqid(),
            'email'    => 'user_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
        ]);
        $role = Role::firstOrCreate(['name' => 'user']);
        $user->roles()->attach($role);
        return $user;
    }

    // =========================================================================
    // Task 11.1 — index, list, store
    // =========================================================================

    /**
     * Test #1: Admin dapat mengakses halaman daftar mitra.
     * Requirements: 3.1, 4.1
     */
    public function test_index_admin_dapat_akses(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/mitra');

        $response->assertStatus(200);
        $response->assertViewIs('mitra.index');
    }

    /**
     * Test #2: Pengguna tanpa autentikasi diarahkan ke halaman login.
     * Requirements: 4.2
     */
    public function test_index_tanpa_auth_redirect_ke_login(): void
    {
        $response = $this->get('/mitra');

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test #3: Pengguna terautentikasi dengan role 'user' (bukan admin) mendapat 403.
     * Requirements: 4.3
     */
    public function test_index_role_bukan_admin_forbidden(): void
    {
        $user = $this->createUserRole();

        $response = $this->actingAs($user)->get('/mitra');

        $response->assertStatus(403);
    }

    /**
     * Test #4: Request AJAX ke /mitra/list mengembalikan JSON berstruktur DataTables.
     * Requirements: 3.2
     */
    public function test_list_ajax_valid_mengembalikan_datatables(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson('/mitra/list', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Test #5: Store data valid tanpa logo → status 200, JSON success, 1 record di DB.
     * Requirements: 3.3
     */
    public function test_store_data_valid_tanpa_logo(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post('/mitra/store', [
                'nama'   => 'TOKI Indonesia',
                'urutan' => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('mitra', ['nama' => 'TOKI Indonesia']);
        $this->assertSame(1, Mitra::count());
    }

    /**
     * Test #6: Store data valid dengan file logo → path logo tersimpan, file ada di storage.
     * Requirements: 3.3
     */
    public function test_store_data_valid_dengan_file_logo(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();

        $file = UploadedFile::fake()->image('logo.jpg', 100, 100);

        $response = $this->actingAs($admin)
            ->post('/mitra/store', [
                'nama'   => 'STEI ITB',
                'logo'   => $file,
                'urutan' => 0,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $mitra = Mitra::first();
        $this->assertNotNull($mitra->logo, 'Kolom logo harus terisi');
        Storage::disk('public')->assertExists($mitra->logo);
    }

    /**
     * Test #7: Store gagal validasi — nama kosong → status 422, JSON errors.nama.
     * Requirements: 3.4
     */
    public function test_store_validasi_gagal_nama_kosong(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->postJson('/mitra/store', ['nama' => '']);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['nama']]);
    }

    /**
     * Test #8: Store gagal validasi — nama lebih dari 255 karakter → status 422.
     * Requirements: 3.4
     */
    public function test_store_validasi_gagal_nama_terlalu_panjang(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->postJson('/mitra/store', ['nama' => str_repeat('A', 256)]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['nama']]);
    }

    /**
     * Test #9: Store gagal validasi — file bukan gambar → status 422, JSON errors.logo.
     * Requirements: 3.4
     */
    public function test_store_validasi_gagal_file_bukan_gambar(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)
            ->postJson('/mitra/store', [
                'nama' => 'Mitra Valid',
                'logo' => $file,
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['logo']]);
    }

    // =========================================================================
    // Task 11.2 — edit, update, destroy
    // =========================================================================

    /**
     * Test #10: Edit dengan ID valid → status 200, JSON {status:'success', data:{id,nama,logo}}.
     * Requirements: 3.5
     */
    public function test_edit_id_valid(): void
    {
        $admin = $this->createAdminUser();
        $mitra = Mitra::create(['nama' => 'Fasilkom UI', 'logo' => null, 'urutan' => 0]);

        $response = $this->actingAs($admin)
            ->get("/mitra/{$mitra->id}/edit");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data'   => [
                'id'   => $mitra->id,
                'nama' => 'Fasilkom UI',
                'logo' => null,
            ],
        ]);
    }

    /**
     * Test #11: Edit dengan ID yang tidak ada → status 404, JSON {status:'error', message:'Data tidak ditemukan'}.
     * Requirements: 3.6
     */
    public function test_edit_id_tidak_ada(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson('/mitra/9999/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'status'  => 'error',
            'message' => 'Data tidak ditemukan',
        ]);
    }

    /**
     * Test #12: Update data valid tanpa file baru → status 200, data terupdate di DB.
     * Requirements: 3.7
     */
    public function test_update_data_valid_tanpa_file_baru(): void
    {
        $admin = $this->createAdminUser();
        $mitra = Mitra::create(['nama' => 'Nama Lama', 'logo' => null, 'urutan' => 0]);

        $response = $this->actingAs($admin)
            ->post("/mitra/{$mitra->id}/update", [
                'nama'   => 'Nama Baru',
                'urutan' => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('mitra', ['id' => $mitra->id, 'nama' => 'Nama Baru']);
    }

    /**
     * Test #13: Update dengan file logo baru → file lama dihapus, file baru tersimpan.
     * Requirements: 3.7
     */
    public function test_update_dengan_file_logo_baru(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();

        // Buat file lama di storage
        $fileLama = UploadedFile::fake()->image('lama.jpg', 50, 50);
        $pathLama = $fileLama->store('mitra', 'public');

        $mitra = Mitra::create(['nama' => 'GDP Labs', 'logo' => $pathLama, 'urutan' => 0]);
        Storage::disk('public')->assertExists($pathLama);

        $fileBaru = UploadedFile::fake()->image('baru.jpg', 80, 80);

        $response = $this->actingAs($admin)
            ->post("/mitra/{$mitra->id}/update", [
                'nama' => 'GDP Labs',
                'logo' => $fileBaru,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // File lama harus terhapus
        Storage::disk('public')->assertMissing($pathLama);

        // File baru harus tersimpan
        $mitra->refresh();
        $this->assertNotNull($mitra->logo);
        $this->assertNotSame($pathLama, $mitra->logo);
        Storage::disk('public')->assertExists($mitra->logo);
    }

    /**
     * Test #14: Update dengan ID yang tidak ada → status 404.
     * Requirements: 3.10
     */
    public function test_update_id_tidak_ada(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->postJson('/mitra/9999/update', ['nama' => 'Mitra X']);

        $response->assertStatus(404);
        $response->assertJson(['status' => 'error']);
    }

    /**
     * Test #15: Destroy ID valid dengan logo → status 200, record terhapus, file terhapus.
     * Requirements: 3.8
     */
    public function test_destroy_id_valid_dengan_logo(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();

        $file = UploadedFile::fake()->image('hapus.jpg', 60, 60);
        $path = $file->store('mitra', 'public');

        $mitra = Mitra::create(['nama' => 'Mitra Hapus', 'logo' => $path, 'urutan' => 0]);
        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($admin)
            ->deleteJson("/mitra/{$mitra->id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('mitra', ['id' => $mitra->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * Test #16: Destroy ID valid tanpa logo → status 200, record terhapus.
     * Requirements: 3.8
     */
    public function test_destroy_id_valid_tanpa_logo(): void
    {
        $admin = $this->createAdminUser();
        $mitra = Mitra::create(['nama' => 'Mitra Tanpa Logo', 'logo' => null, 'urutan' => 0]);

        $response = $this->actingAs($admin)
            ->deleteJson("/mitra/{$mitra->id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('mitra', ['id' => $mitra->id]);
    }

    /**
     * Test #17: Destroy dengan ID yang tidak ada → status 404.
     * Requirements: 3.10
     */
    public function test_destroy_id_tidak_ada(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->deleteJson('/mitra/9999');

        $response->assertStatus(404);
        $response->assertJson(['status' => 'error']);
    }
}
