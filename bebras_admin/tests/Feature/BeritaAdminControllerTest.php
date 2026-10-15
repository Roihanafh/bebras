<?php

namespace Tests\Feature;

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit tests untuk BeritaAdminController
 * Requirements: 4.3, 4.4, 4.5, 4.7, 4.8, 4.9
 */
class BeritaAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function buatAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        $user = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test_' . uniqid(),
            'email'    => 'admin_test_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    private function buatBiro(): User
    {
        $role = Role::firstOrCreate(['name' => 'biro']);

        $user = User::create([
            'name'     => 'Biro Test',
            'username' => 'biro_test_' . uniqid(),
            'email'    => 'biro_test_' . uniqid() . '@example.com',
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

    // -----------------------------------------------------------------------
    // Test: approve — Requirements 4.3
    // -----------------------------------------------------------------------

    /**
     * approve mengubah status_validasi menjadi 'approved'.
     * Requirements: 4.3
     */
    public function test_approve_mengubah_status_ke_approved(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/approve");

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'approved',
        ]);
    }

    /**
     * approve mengisi divalidasi_oleh dengan id admin yang melakukan aksi.
     * Requirements: 4.3
     */
    public function test_approve_mengisi_divalidasi_oleh_dengan_id_admin(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/approve");

        $this->assertDatabaseHas('kegiatans', [
            'id'             => $berita->id,
            'divalidasi_oleh' => $admin->id,
        ]);
    }

    /**
     * approve mengisi divalidasi_pada dengan timestamp saat aksi dilakukan.
     * Requirements: 4.3
     */
    public function test_approve_mengisi_divalidasi_pada_dengan_timestamp(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $sebelum = now()->subSecond();

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/approve");

        $beritaSetelah = Kegiatan::find($berita->id);

        $this->assertNotNull($beritaSetelah->divalidasi_pada);
        $divalidasiPada = Carbon::parse($beritaSetelah->divalidasi_pada);
        $this->assertTrue(
            $divalidasiPada->gte($sebelum),
            'divalidasi_pada harus berisi timestamp yang tidak lebih awal dari sebelum aksi'
        );
    }

    /**
     * approve mengembalikan redirect ke admin.berita.index dengan flash success.
     * Requirements: 4.3
     */
    public function test_approve_redirect_ke_admin_berita_index_dengan_flash_success(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $response = $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/approve");

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');
    }

    /**
     * approve dapat dilakukan pada berita yang sudah berstatus 'rejected'.
     * (Aksi dilanjutkan setelah konfirmasi, sesuai requirement 4.6)
     * Requirements: 4.3, 4.6
     */
    public function test_approve_berita_rejected_mengubah_status_ke_approved(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'rejected');

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/approve");

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'approved',
            'divalidasi_oleh' => $admin->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // Test: reject — Requirements 4.4, 4.5
    // -----------------------------------------------------------------------

    /**
     * reject mengubah status_validasi menjadi 'rejected'.
     * Requirements: 4.4
     */
    public function test_reject_mengubah_status_ke_rejected(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", [
                'catatan_validasi' => 'Konten perlu diperbaiki.',
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'rejected',
        ]);
    }

    /**
     * reject mengisi divalidasi_oleh dengan id admin yang melakukan aksi.
     * Requirements: 4.4
     */
    public function test_reject_mengisi_divalidasi_oleh_dengan_id_admin(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", [
                'catatan_validasi' => 'Perlu perbaikan.',
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'divalidasi_oleh' => $admin->id,
        ]);
    }

    /**
     * reject mengisi divalidasi_pada dengan timestamp saat aksi dilakukan.
     * Requirements: 4.4
     */
    public function test_reject_mengisi_divalidasi_pada_dengan_timestamp(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $sebelum = now()->subSecond();

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", [
                'catatan_validasi' => 'Perlu perbaikan.',
            ]);

        $beritaSetelah = Kegiatan::find($berita->id);

        $this->assertNotNull($beritaSetelah->divalidasi_pada);
        $divalidasiPada = Carbon::parse($beritaSetelah->divalidasi_pada);
        $this->assertTrue(
            $divalidasiPada->gte($sebelum),
            'divalidasi_pada harus berisi timestamp yang tidak lebih awal dari sebelum aksi'
        );
    }

    /**
     * reject menyimpan catatan_validasi yang diisikan admin.
     * Requirements: 4.5
     */
    public function test_reject_menyimpan_catatan_validasi(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $catatan = 'Judul berita terlalu panjang dan tidak sesuai panduan.';

        $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", [
                'catatan_validasi' => $catatan,
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'               => $berita->id,
            'catatan_validasi' => $catatan,
        ]);
    }

    /**
     * reject dapat dijalankan tanpa catatan_validasi (field opsional, boleh null).
     * Requirements: 4.5
     */
    public function test_reject_tanpa_catatan_validasi_berhasil(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $response = $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", []);

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'rejected',
        ]);
    }

    /**
     * reject mengembalikan redirect ke admin.berita.index dengan flash success.
     * Requirements: 4.4
     */
    public function test_reject_redirect_ke_admin_berita_index_dengan_flash_success(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $response = $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", [
                'catatan_validasi' => 'Konten tidak sesuai.',
            ]);

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');
    }

    // -----------------------------------------------------------------------
    // Test: update konten tidak mengubah status_validasi — Requirements 4.7
    // -----------------------------------------------------------------------

    /**
     * Admin mengedit konten berita berstatus 'pending' → status tetap 'pending'.
     * Requirements: 4.7
     */
    public function test_update_tidak_mengubah_status_validasi_pending(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'pending', $menu);

        $this->actingAs($admin)
            ->put("/admin/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => 'Judul Diperbarui Admin',
                'deskripsi'        => 'Deskripsi diperbarui oleh admin.',
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'pending',
        ]);
    }

    /**
     * Admin mengedit konten berita berstatus 'approved' → status tetap 'approved'.
     * Requirements: 4.7
     */
    public function test_update_tidak_mengubah_status_validasi_approved(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'approved', $menu);

        $this->actingAs($admin)
            ->put("/admin/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => 'Judul Diperbarui Admin',
                'deskripsi'        => 'Deskripsi diperbarui oleh admin.',
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'approved',
        ]);
    }

    /**
     * Admin mengedit konten berita berstatus 'rejected' → status tetap 'rejected'.
     * Requirements: 4.7
     */
    public function test_update_tidak_mengubah_status_validasi_rejected(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'rejected', $menu);

        $this->actingAs($admin)
            ->put("/admin/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => 'Judul Diperbarui Admin',
                'deskripsi'        => 'Deskripsi diperbarui oleh admin.',
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'rejected',
        ]);
    }

    /**
     * update mengubah field konten (judul, deskripsi) dengan benar.
     * Requirements: 4.7
     */
    public function test_update_mengubah_field_konten_dengan_benar(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'pending', $menu);

        $judulBaru     = 'Judul Baru Dari Admin';
        $deskripsiBaru = 'Deskripsi baru yang sudah diperbarui admin.';

        $this->actingAs($admin)
            ->put("/admin/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => $judulBaru,
                'deskripsi'        => $deskripsiBaru,
            ]);

        $this->assertDatabaseHas('kegiatans', [
            'id'        => $berita->id,
            'judul'     => $judulBaru,
            'deskripsi' => $deskripsiBaru,
        ]);
    }

    /**
     * update mengembalikan redirect ke admin.berita.index dengan flash success.
     * Requirements: 4.7
     */
    public function test_update_redirect_ke_admin_berita_index_dengan_flash_success(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'pending', $menu);

        $response = $this->actingAs($admin)
            ->put("/admin/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => 'Judul Baru',
                'deskripsi'        => 'Deskripsi baru.',
            ]);

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');
    }

    // -----------------------------------------------------------------------
    // Test: destroy berhasil untuk semua status — Requirements 4.8
    // -----------------------------------------------------------------------

    /**
     * Admin dapat menghapus berita berstatus 'pending'.
     * Requirements: 4.8
     */
    public function test_destroy_berita_pending_berhasil(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        $response = $this->actingAs($admin)
            ->delete("/admin/berita/{$berita->id}");

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kegiatans', ['id' => $berita->id]);
    }

    /**
     * Admin dapat menghapus berita berstatus 'approved'.
     * Requirements: 4.8
     */
    public function test_destroy_berita_approved_berhasil(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'approved');

        $response = $this->actingAs($admin)
            ->delete("/admin/berita/{$berita->id}");

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kegiatans', ['id' => $berita->id]);
    }

    /**
     * Admin dapat menghapus berita berstatus 'rejected'.
     * Requirements: 4.8
     */
    public function test_destroy_berita_rejected_berhasil(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'rejected');

        $response = $this->actingAs($admin)
            ->delete("/admin/berita/{$berita->id}");

        $response->assertRedirect(route('admin.berita.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kegiatans', ['id' => $berita->id]);
    }

    /**
     * destroy menghapus file gambar dari storage jika ada.
     * Requirements: 4.8
     */
    public function test_destroy_menghapus_file_gambar_dari_storage(): void
    {
        Storage::fake('public');

        $admin = $this->buatAdmin();
        $biro  = $this->buatBiro();
        $menu  = $this->buatMenuKegiatan();

        // Buat file gambar palsu
        $file = UploadedFile::fake()->image('foto.jpg');
        $path = $file->store('berita', 'public');

        $berita = Kegiatan::create([
            'menu_kegiatan_id' => $menu->id,
            'tipe'             => 'berita',
            'judul'            => 'Berita Dengan Gambar',
            'deskripsi'        => 'Deskripsi',
            'urutan'           => 0,
            'status_validasi'  => 'approved',
            'dibuat_oleh'      => $biro->id,
            'gambar'           => $path,
        ]);

        $this->actingAs($admin)->delete("/admin/berita/{$berita->id}");

        Storage::disk('public')->assertMissing($path);
    }

    // -----------------------------------------------------------------------
    // Test: DB error → rollback + pesan error generik — Requirements 4.9
    // -----------------------------------------------------------------------

    /**
     * approve — DB error menyebabkan rollback dan pesan error generik.
     * Requirements: 4.9
     */
    public function test_approve_db_error_rollback_dan_pesan_generik(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        // Paksa exception saat model di-save
        Kegiatan::saving(function () {
            throw new \Exception('Simulated DB error approve');
        });

        $response = $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/approve");

        Kegiatan::flushEventListeners();

        $response->assertSessionHasErrors('error');

        $errors   = session('errors');
        $errorMsg = $errors->first('error');
        $this->assertStringNotContainsStringIgnoringCase('Simulated DB error', $errorMsg);
        $this->assertStringNotContainsStringIgnoringCase('Exception', $errorMsg);

        // Status tidak berubah
        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'pending',
        ]);
    }

    /**
     * reject — DB error menyebabkan rollback dan pesan error generik.
     * Requirements: 4.9
     */
    public function test_reject_db_error_rollback_dan_pesan_generik(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        Kegiatan::saving(function () {
            throw new \Exception('Simulated DB error reject');
        });

        $response = $this->actingAs($admin)
            ->post("/admin/berita/{$berita->id}/reject", [
                'catatan_validasi' => 'Catatan.',
            ]);

        Kegiatan::flushEventListeners();

        $response->assertSessionHasErrors('error');

        $errors   = session('errors');
        $errorMsg = $errors->first('error');
        $this->assertStringNotContainsStringIgnoringCase('Simulated DB error', $errorMsg);

        // Status tidak berubah
        $this->assertDatabaseHas('kegiatans', [
            'id'              => $berita->id,
            'status_validasi' => 'pending',
        ]);
    }

    /**
     * update — DB error menyebabkan rollback dan pesan error generik.
     * Requirements: 4.9
     */
    public function test_update_db_error_rollback_dan_pesan_generik(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $menu   = $this->buatMenuKegiatan();
        $berita = $this->buatBerita($biro, 'pending', $menu);

        $judulAsli = $berita->judul;

        Kegiatan::saving(function () {
            throw new \Exception('Simulated DB error update');
        });

        $response = $this->actingAs($admin)
            ->put("/admin/berita/{$berita->id}", [
                'menu_kegiatan_id' => $menu->id,
                'judul'            => 'Judul Yang Tidak Tersimpan',
                'deskripsi'        => 'Deskripsi baru.',
            ]);

        Kegiatan::flushEventListeners();

        $response->assertSessionHasErrors('error');

        $errors   = session('errors');
        $errorMsg = $errors->first('error');
        $this->assertStringNotContainsStringIgnoringCase('Simulated DB error', $errorMsg);

        // Judul tidak berubah
        $this->assertDatabaseHas('kegiatans', [
            'id'    => $berita->id,
            'judul' => $judulAsli,
        ]);
    }

    /**
     * destroy — DB error menyebabkan rollback dan pesan error generik.
     * Requirements: 4.9
     */
    public function test_destroy_db_error_rollback_dan_pesan_generik(): void
    {
        $admin  = $this->buatAdmin();
        $biro   = $this->buatBiro();
        $berita = $this->buatBerita($biro, 'pending');

        Kegiatan::deleting(function () {
            throw new \Exception('Simulated DB error destroy');
        });

        $response = $this->actingAs($admin)
            ->delete("/admin/berita/{$berita->id}");

        Kegiatan::flushEventListeners();

        $response->assertSessionHasErrors('error');

        $errors   = session('errors');
        $errorMsg = $errors->first('error');
        $this->assertStringNotContainsStringIgnoringCase('Simulated DB error', $errorMsg);

        // Berita masih ada di database
        $this->assertDatabaseHas('kegiatans', ['id' => $berita->id]);
    }
}
