<?php

namespace Tests\Feature;

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit tests untuk BeritaBiroController
 * Requirements: 3.1, 3.4, 3.5, 3.6, 3.8
 */
class BeritaBiroControllerTest extends TestCase
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

    private function buatMenuKegiatan(): MenuKegiatan
    {
        return MenuKegiatan::create([
            'nama_menu' => 'Berita Test',
            'slug'      => 'berita-test-' . uniqid(),
            'judul'     => 'Judul Menu Berita',
            'urutan'    => 1,
        ]);
    }

    private function buatBerita(User $biro, string $status = 'pending', ?MenuKegiatan $menu = null): Kegiatan
    {
        $menu ??= $this->buatMenuKegiatan();

        return Kegiatan::create([
            'menu_kegiatan_id' => $menu->id,
            'tipe'             => 'berita',
            'judul'            => 'Berita Uji ' . uniqid(),
            'deskripsi'        => 'Deskripsi berita uji',
            'urutan'           => 0,
            'status_validasi'  => $status,
            'dibuat_oleh'      => $biro->id,
        ]);
    }

    private function dataStoreValid(MenuKegiatan $menu): array
    {
        return [
            'menu_kegiatan_id' => $menu->id,
            'judul'            => 'Berita Baru dari Biro',
            'deskripsi'        => 'Isi deskripsi berita yang cukup panjang.',
            'urutan'           => 1,
        ];
    }

    // -----------------------------------------------------------------------
    // Test: store — Requirements 3.1
    // -----------------------------------------------------------------------

    /**
     * store menyimpan berita dengan tipe='berita', status_validasi='pending',
     * dan dibuat_oleh diisi dengan id biro yang login.
     * Requirements: 3.1
     */
    public function test_store_menyimpan_berita_dengan_atribut_yang_benar(): void
    {
        $biro = $this->buatUser('biro');
        $menu = $this->buatMenuKegiatan();

        $this->actingAs($biro)
            ->post('/berita/store', $this->dataStoreValid($menu));

        $this->assertDatabaseHas('kegiatans', [
            'judul'            => 'Berita Baru dari Biro',
            'tipe'             => 'berita',
            'status_validasi'  => 'pending',
            'dibuat_oleh'      => $biro->id,
        ]);
    }

    /**
     * store mengembalikan redirect ke berita.index dengan flash success.
     * Requirements: 3.1
     */
    public function test_store_redirect_ke_berita_index_dengan_flash_success(): void
    {
        $biro = $this->buatUser('biro');
        $menu = $this->buatMenuKegiatan();

        $response = $this->actingAs($biro)
            ->post('/berita/store', $this->dataStoreValid($menu));

        $response->assertRedirect(route('berita.index'));
        $response->assertSessionHas('success');
    }

    /**
     * store menolak request dengan field wajib yang kosong.
     * Requirements: 3.7
     */
    public function test_store_gagal_jika_field_wajib_kosong(): void
    {
        $biro = $this->buatUser('biro');

        $response = $this->actingAs($biro)
            ->post('/berita/store', []);

        $response->assertSessionHasErrors(['judul', 'deskripsi']);
    }

    /**
     * store tidak mengubah status menjadi apapun selain 'pending' —
     * bahkan jika request mencoba mengirim status_validasi berbeda.
     * Requirements: 3.1
     */
    public function test_store_selalu_menetapkan_status_pending_bukan_dari_request(): void
    {
        $biro = $this->buatUser('biro');
        $menu = $this->buatMenuKegiatan();

        $data              = $this->dataStoreValid($menu);
        $data['status_validasi'] = 'approved'; // upaya inject status

        $this->actingAs($biro)->post('/berita/store', $data);

        $this->assertDatabaseHas('kegiatans', [
            'judul'           => 'Berita Baru dari Biro',
            'status_validasi' => 'pending',
        ]);
    }

    // -----------------------------------------------------------------------
    // Test: destroy berita pending/rejected — Requirements 3.4
    // -----------------------------------------------------------------------

    /**
     * Biro dapat menghapus berita miliknya sendiri yang berstatus 'pending'.
     * Requirements: 3.4
     */
    public function test_destroy_berita_pending_berhasil(): void
    {
        $biro   = $this->buatUser('biro');
        $berita = $this->buatBerita($biro, 'pending');

        $response = $this->actingAs($biro)
            ->delete("/berita/{$berita->id}");

        $response->assertRedirect(route('berita.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kegiatans', ['id' => $berita->id]);
    }

    /**
     * Biro dapat menghapus berita miliknya sendiri yang berstatus 'rejected'.
     * Requirements: 3.4
     */
    public function test_destroy_berita_rejected_berhasil(): void
    {
        $biro   = $this->buatUser('biro');
        $berita = $this->buatBerita($biro, 'rejected');

        $response = $this->actingAs($biro)
            ->delete("/berita/{$berita->id}");

        $response->assertRedirect(route('berita.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kegiatans', ['id' => $berita->id]);
    }

    // -----------------------------------------------------------------------
    // Test: destroy berita approved — Requirements 3.5
    // -----------------------------------------------------------------------

    /**
     * Biro tidak dapat menghapus berita miliknya yang berstatus 'approved'.
     * Harus kembali dengan pesan error.
     * Requirements: 3.5
     */
    public function test_destroy_berita_approved_menghasilkan_error(): void
    {
        $biro   = $this->buatUser('biro');
        $berita = $this->buatBerita($biro, 'approved');

        $response = $this->actingAs($biro)
            ->delete("/berita/{$berita->id}");

        $response->assertSessionHasErrors('error');
        // Berita masih ada di database
        $this->assertDatabaseHas('kegiatans', ['id' => $berita->id]);
    }

    /**
     * Berita yang berstatus 'approved' tidak dihapus dari database
     * meskipun request hapus dikirimkan.
     * Requirements: 3.5
     */
    public function test_destroy_berita_approved_tidak_menghapus_dari_database(): void
    {
        $biro   = $this->buatUser('biro');
        $berita = $this->buatBerita($biro, 'approved');

        $this->actingAs($biro)->delete("/berita/{$berita->id}");

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'approved',
        ]);
    }

    // -----------------------------------------------------------------------
    // Test: 403 edit/hapus berita biro lain — Requirements 3.6
    // -----------------------------------------------------------------------

    /**
     * Biro yang mencoba mengakses endpoint edit berita milik biro lain
     * harus mendapatkan HTTP 403.
     * Requirements: 3.6
     */
    public function test_edit_berita_biro_lain_menghasilkan_403(): void
    {
        $biroA  = $this->buatUser('biro');
        $biroB  = $this->buatUser('biro');
        $berita = $this->buatBerita($biroB, 'pending');

        $response = $this->actingAs($biroA)
            ->get("/berita/{$berita->id}/edit");

        $response->assertStatus(403);
    }

    /**
     * Biro yang mencoba menghapus berita milik biro lain
     * harus mendapatkan HTTP 403 tanpa perubahan data.
     * Requirements: 3.6
     */
    public function test_destroy_berita_biro_lain_menghasilkan_403(): void
    {
        $biroA  = $this->buatUser('biro');
        $biroB  = $this->buatUser('biro');
        $berita = $this->buatBerita($biroB, 'pending');

        $response = $this->actingAs($biroA)
            ->delete("/berita/{$berita->id}");

        $response->assertStatus(403);
        // Berita milik biroB tetap ada
        $this->assertDatabaseHas('kegiatans', ['id' => $berita->id]);
    }

    /**
     * Biro yang mencoba mengupdate berita milik biro lain
     * harus mendapatkan HTTP 403 tanpa perubahan data.
     * Requirements: 3.6
     */
    public function test_update_berita_biro_lain_menghasilkan_403(): void
    {
        $biroA  = $this->buatUser('biro');
        $biroB  = $this->buatUser('biro');
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biroB, 'pending', $menu);

        $judulAsli = $berita->judul;

        $response = $this->actingAs($biroA)
            ->put("/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => 'Judul Diubah oleh Biro Lain',
                'deskripsi'        => 'Deskripsi baru',
            ]);

        $response->assertStatus(403);
        // Judul berita tidak berubah
        $this->assertDatabaseHas('kegiatans', [
            'id'    => $berita->id,
            'judul' => $judulAsli,
        ]);
    }

    // -----------------------------------------------------------------------
    // Test: DB error → rollback + pesan error generik — Requirements 3.8
    // -----------------------------------------------------------------------

    /**
     * Jika terjadi error database saat store, controller harus rollback
     * dan mengembalikan pesan error generik tanpa detail teknis.
     *
     * Simulasi error: gunakan Eloquent 'creating' event untuk melempar
     * exception setelah BeginTransaction sudah dipanggil controller,
     * sehingga rollback path tereksekusi.
     * Requirements: 3.8
     */
    public function test_store_db_error_rollback_dan_pesan_generik(): void
    {
        $biro = $this->buatUser('biro');
        $menu = $this->buatMenuKegiatan();

        // Paksa exception saat model hendak disimpan
        Kegiatan::creating(function () {
            throw new \Exception('Simulated DB error');
        });

        $response = $this->actingAs($biro)
            ->post('/berita/store', $this->dataStoreValid($menu));

        // Hapus listener agar tidak mengganggu test lain
        Kegiatan::flushEventListeners();

        $response->assertSessionHasErrors('error');

        // Pesan error tidak boleh mengekspos detail teknis
        $errors   = session('errors');
        $errorMsg = $errors->first('error');
        $this->assertStringNotContainsStringIgnoringCase('Simulated DB error', $errorMsg);
        $this->assertStringNotContainsStringIgnoringCase('Exception', $errorMsg);
    }

    /**
     * Jika terjadi error database saat destroy, controller harus rollback
     * dan mengembalikan pesan error generik.
     * Requirements: 3.8
     */
    public function test_destroy_db_error_rollback_dan_pesan_generik(): void
    {
        $biro   = $this->buatUser('biro');
        $berita = $this->buatBerita($biro, 'pending');

        // Paksa exception saat model hendak dihapus
        Kegiatan::deleting(function () {
            throw new \Exception('Simulated DB error');
        });

        $response = $this->actingAs($biro)
            ->delete("/berita/{$berita->id}");

        // Hapus listener agar tidak mengganggu test lain
        Kegiatan::flushEventListeners();

        $response->assertSessionHasErrors('error');

        $errors   = session('errors');
        $errorMsg = $errors->first('error');
        $this->assertStringNotContainsStringIgnoringCase('Simulated DB error', $errorMsg);
    }

    // -----------------------------------------------------------------------
    // Test: update reset status ke pending — Requirements 3.3
    // -----------------------------------------------------------------------

    /**
     * Biro mengedit berita berstatus 'approved' → status direset ke 'pending'.
     * Requirements: 3.3
     */
    public function test_update_berita_approved_mereset_status_ke_pending(): void
    {
        $biro   = $this->buatUser('biro');
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'approved', $menu);

        $this->actingAs($biro)->put("/berita/{$berita->id}", [
            'menu_kegiatan_id' => $menu->id,
            'judul'            => 'Judul Diperbarui',
            'deskripsi'        => 'Deskripsi diperbarui',
        ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'pending',
        ]);
    }

    /**
     * Biro mengedit berita berstatus 'rejected' → status direset ke 'pending'.
     * Requirements: 3.3
     */
    public function test_update_berita_rejected_mereset_status_ke_pending(): void
    {
        $biro   = $this->buatUser('biro');
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'rejected', $menu);

        $this->actingAs($biro)->put("/berita/{$berita->id}", [
            'menu_kegiatan_id' => $menu->id,
            'judul'            => 'Judul Setelah Revisi',
            'deskripsi'        => 'Deskripsi setelah revisi',
        ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'pending',
        ]);
    }

    // -----------------------------------------------------------------------
    // Test: upload gambar saat store — Requirements 3.7
    // -----------------------------------------------------------------------

    /**
     * store dengan upload gambar valid menyimpan path gambar ke database.
     * Requirements: 3.7
     */
    public function test_store_dengan_gambar_menyimpan_path_gambar(): void
    {
        Storage::fake('public');

        $biro  = $this->buatUser('biro');
        $menu  = $this->buatMenuKegiatan();
        $data  = $this->dataStoreValid($menu);
        $data['gambar'] = UploadedFile::fake()->image('foto.jpg', 100, 100);

        $this->actingAs($biro)->post('/berita/store', $data);

        $berita = Kegiatan::where('dibuat_oleh', $biro->id)->first();
        $this->assertNotNull($berita->gambar);
        Storage::disk('public')->assertExists($berita->gambar);
    }
}
