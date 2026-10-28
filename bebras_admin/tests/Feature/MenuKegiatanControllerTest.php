<?php

namespace Tests\Feature;

use App\Models\MenuKegiatan;
use App\Models\PengumumanHasilFile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature tests untuk MenuKegiatanController — logika penyimpanan kolom template.
 * Requirements: 4.5, 4.6, 4.7, 4.8, 4.9
 */
class MenuKegiatanControllerTest extends TestCase
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

    private function dataMenuValid(array $override = []): array
    {
        return array_merge([
            'nama_menu' => 'Menu Test',
            'slug'      => 'menu-test-' . uniqid(),
            'judul'     => 'Judul Menu Test',
            'urutan'    => 1,
            'parent_id' => '',
        ], $override);
    }

    // -----------------------------------------------------------------------
    // Test: store — Requirements 4.5, 4.6, 4.9
    // -----------------------------------------------------------------------

    /**
     * store() dengan parent_id kosong dan template valid menyimpan template ke DB.
     * Requirements: 4.5
     */
    public function test_store_menu_root_menyimpan_template(): void
    {
        $admin = $this->buatAdmin();
        $slug  = 'menu-root-' . uniqid();

        $this->actingAs($admin)
            ->post(route('menu_kegiatan.store'), $this->dataMenuValid([
                'slug'      => $slug,
                'parent_id' => '',
                'template'  => 'workshop',
            ]));

        $this->assertDatabaseHas('menu_kegiatan', [
            'slug'     => $slug,
            'template' => 'workshop',
        ]);
    }

    /**
     * store() dengan parent_id terisi harus menyimpan template = NULL,
     * mengabaikan nilai template yang dikirim dari request.
     * Requirements: 4.6
     */
    public function test_store_menu_child_template_dipaksa_null(): void
    {
        $admin  = $this->buatAdmin();
        $parent = MenuKegiatan::create([
            'nama_menu' => 'Parent Menu',
            'slug'      => 'parent-menu-' . uniqid(),
            'urutan'    => 1,
            'template'  => 'workshop',
        ]);
        $slug = 'menu-child-' . uniqid();

        $this->actingAs($admin)
            ->post(route('menu_kegiatan.store'), $this->dataMenuValid([
                'slug'      => $slug,
                'parent_id' => $parent->id,
                'template'  => 'workshop',
            ]));

        $this->assertDatabaseHas('menu_kegiatan', [
            'slug'      => $slug,
            'parent_id' => $parent->id,
            'template'  => null,
        ]);
    }

    /**
     * store() dengan nilai template tidak valid harus mengembalikan error validasi
     * pada field template (redirect kembali dengan session errors).
     * Requirements: 4.9
     */
    public function test_store_template_tidak_valid_mengembalikan_error_validasi(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->post(route('menu_kegiatan.store'), $this->dataMenuValid([
                'slug'     => 'menu-invalid-' . uniqid(),
                'template' => 'nilai_tidak_valid',
            ]));

        // Laravel mengembalikan redirect (302) dengan session errors untuk request form biasa
        $response->assertSessionHasErrors('template');
        $this->assertDatabaseMissing('menu_kegiatan', ['template' => 'nilai_tidak_valid']);
    }

    // -----------------------------------------------------------------------
    // Test: update — Requirements 4.7, 4.8
    // -----------------------------------------------------------------------

    /**
     * update() saat parent_id dikosongkan dengan template valid harus memperbarui
     * kolom template di DB.
     * Requirements: 4.7
     */
    public function test_update_menu_root_memperbarui_template(): void
    {
        $admin = $this->buatAdmin();
        $menu  = MenuKegiatan::create([
            'nama_menu' => 'Menu Update',
            'slug'      => 'menu-update-' . uniqid(),
            'urutan'    => 1,
            'template'  => null,
        ]);

        $this->actingAs($admin)
            ->put(route('menu_kegiatan.update', $menu->id), [
                'nama_menu' => $menu->nama_menu,
                'slug'      => $menu->slug,
                'urutan'    => $menu->urutan,
                'parent_id' => '',
                'template'  => 'pengumuman_hasil',
            ]);

        $this->assertDatabaseHas('menu_kegiatan', [
            'id'       => $menu->id,
            'template' => 'pengumuman_hasil',
        ]);
    }

    /**
     * update() saat parent_id terisi harus memaksa template = NULL di DB,
     * mengabaikan nilai template dari request.
     * Requirements: 4.8
     */
    public function test_update_menu_child_template_dipaksa_null(): void
    {
        $admin  = $this->buatAdmin();
        $parent = MenuKegiatan::create([
            'nama_menu' => 'Parent Update',
            'slug'      => 'parent-update-' . uniqid(),
            'urutan'    => 1,
            'template'  => 'bebras_challenge',
        ]);
        $child = MenuKegiatan::create([
            'parent_id' => null,
            'nama_menu' => 'Child Update',
            'slug'      => 'child-update-' . uniqid(),
            'urutan'    => 2,
            'template'  => 'workshop',
        ]);

        $this->actingAs($admin)
            ->put(route('menu_kegiatan.update', $child->id), [
                'nama_menu' => $child->nama_menu,
                'slug'      => $child->slug,
                'urutan'    => $child->urutan,
                'parent_id' => $parent->id,
                'template'  => 'workshop', // dikirim, tapi harus diabaikan
            ]);

        $this->assertDatabaseHas('menu_kegiatan', [
            'id'       => $child->id,
            'template' => null,
        ]);
    }

    /**
     * store() dengan template pengumuman_hasil mengunggah CSV untuk 4 kategori dan mengabaikan gambar.
     */
    public function test_store_pengumuman_hasil_mengunggah_csv_dan_mengabaikan_gambar(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();
        $slug  = 'pengumuman-test-' . uniqid();

        $fileSikecil    = UploadedFile::fake()->create('Hasil-SiKecil.csv', 100, 'text/csv');
        $fileSiaga      = UploadedFile::fake()->create('Hasil-Siaga.xlsx', 200, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filePenggalang = UploadedFile::fake()->create('Hasil-Penggalang.csv', 150, 'text/csv');
        $filePenegak    = UploadedFile::fake()->create('Hasil-Penegak.csv', 120, 'text/csv');
        $gambarDummy    = UploadedFile::fake()->image('banner.jpg');

        $response = $this->actingAs($admin)->post(route('menu_kegiatan.store'), [
            'nama_menu'      => 'Pengumuman 2026',
            'slug'           => $slug,
            'urutan'         => 1,
            'template'       => 'pengumuman_hasil',
            'gambar'         => $gambarDummy, // seharusnya diabaikan/diset null
            'csv_sikecil'    => $fileSikecil,
            'csv_siaga'      => $fileSiaga,
            'csv_penggalang' => $filePenggalang,
            'csv_penegak'    => $filePenegak,
        ]);

        $response->assertRedirect(route('menu_kegiatan.index'));

        $menu = MenuKegiatan::where('slug', $slug)->firstOrFail();
        $this->assertNull($menu->gambar);

        $this->assertDatabaseHas('pengumuman_hasil_files', [
            'menu_kegiatan_id' => $menu->id,
            'kategori'         => 'sikecil',
            'original_name'    => 'Hasil-SiKecil.csv',
        ]);
        $this->assertDatabaseHas('pengumuman_hasil_files', [
            'menu_kegiatan_id' => $menu->id,
            'kategori'         => 'siaga',
            'original_name'    => 'Hasil-Siaga.xlsx',
        ]);
        $this->assertDatabaseHas('pengumuman_hasil_files', [
            'menu_kegiatan_id' => $menu->id,
            'kategori'         => 'penggalang',
            'original_name'    => 'Hasil-Penggalang.csv',
        ]);
        $this->assertDatabaseHas('pengumuman_hasil_files', [
            'menu_kegiatan_id' => $menu->id,
            'kategori'         => 'penegak',
            'original_name'    => 'Hasil-Penegak.csv',
        ]);

        $files = PengumumanHasilFile::where('menu_kegiatan_id', $menu->id)->get();
        $this->assertCount(4, $files);
        foreach ($files as $f) {
            Storage::disk('public')->assertExists($f->file_path);
        }
    }

    /**
     * update() dengan template pengumuman_hasil memperbarui file CSV dan menghapus file lama.
     */
    public function test_update_pengumuman_hasil_memperbarui_file_csv(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();

        $menu = MenuKegiatan::create([
            'nama_menu' => 'Pengumuman Edit Test',
            'slug'      => 'pengumuman-edit-' . uniqid(),
            'urutan'    => 1,
            'template'  => 'pengumuman_hasil',
        ]);

        $oldPath = UploadedFile::fake()->create('Old-SiKecil.csv', 50, 'text/csv')->store('pengumuman_hasil', 'public');
        PengumumanHasilFile::create([
            'menu_kegiatan_id' => $menu->id,
            'kategori'         => 'sikecil',
            'file_path'        => $oldPath,
            'original_name'    => 'Old-SiKecil.csv',
            'file_size'        => 50000,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->create('New-SiKecil.csv', 80, 'text/csv');

        $this->actingAs($admin)->put(route('menu_kegiatan.update', $menu->id), [
            'nama_menu'   => $menu->nama_menu,
            'slug'        => $menu->slug,
            'urutan'      => $menu->urutan,
            'template'    => 'pengumuman_hasil',
            'csv_sikecil' => $newFile,
        ]);

        Storage::disk('public')->assertMissing($oldPath);

        $updatedRecord = PengumumanHasilFile::where('menu_kegiatan_id', $menu->id)
            ->where('kategori', 'sikecil')
            ->firstOrFail();

        $this->assertSame('New-SiKecil.csv', $updatedRecord->original_name);
        Storage::disk('public')->assertExists($updatedRecord->file_path);
    }

    /**
     * destroy() menu_kegiatan menghapus semua file CSV terkait di storage.
     */
    public function test_destroy_pengumuman_hasil_menghapus_file_di_storage(): void
    {
        Storage::fake('public');
        $admin = $this->buatAdmin();

        $menu = MenuKegiatan::create([
            'nama_menu' => 'Pengumuman Hapus Test',
            'slug'      => 'pengumuman-hapus-' . uniqid(),
            'urutan'    => 1,
            'template'  => 'pengumuman_hasil',
        ]);

        $path = UploadedFile::fake()->create('Siaga.xlsx', 100)->store('pengumuman_hasil', 'public');
        PengumumanHasilFile::create([
            'menu_kegiatan_id' => $menu->id,
            'kategori'         => 'siaga',
            'file_path'        => $path,
            'original_name'    => 'Siaga.xlsx',
            'file_size'        => 100000,
        ]);

        Storage::disk('public')->assertExists($path);

        $this->actingAs($admin)->delete(route('menu_kegiatan.destroy', $menu->id));

        $this->assertDatabaseMissing('menu_kegiatan', ['id' => $menu->id]);
        $this->assertDatabaseMissing('pengumuman_hasil_files', ['menu_kegiatan_id' => $menu->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * store() untuk menu child yang parent-nya memiliki template pengumuman_hasil
     * harus mengunggah file CSV dan mengabaikan gambar.
     */
    public function test_store_menu_child_mengadopsi_template_pengumuman_hasil_dari_parent(): void
    {
        Storage::fake('public');
        $admin  = $this->buatAdmin();
        $parent = MenuKegiatan::create([
            'nama_menu' => 'Pengumuman Hasil Induk',
            'slug'      => 'pengumuman-hasil-induk-' . uniqid(),
            'urutan'    => 1,
            'template'  => 'pengumuman_hasil',
        ]);

        $slugChild = 'pengumuman-child-' . uniqid();
        $fileSikecil = UploadedFile::fake()->create('Hasil-Child-SiKecil.csv', 100, 'text/csv');

        $response = $this->actingAs($admin)->post(route('menu_kegiatan.store'), [
            'parent_id'   => $parent->id,
            'nama_menu'   => '2026',
            'slug'        => $slugChild,
            'urutan'      => 1,
            'csv_sikecil' => $fileSikecil,
        ]);

        $response->assertRedirect(route('menu_kegiatan.index'));

        $child = MenuKegiatan::where('slug', $slugChild)->firstOrFail();
        $this->assertNull($child->template); // template child tetap null di DB
        $this->assertSame('pengumuman_hasil', $child->resolveTemplate()); // resolved dari parent

        $this->assertDatabaseHas('pengumuman_hasil_files', [
            'menu_kegiatan_id' => $child->id,
            'kategori'         => 'sikecil',
            'original_name'    => 'Hasil-Child-SiKecil.csv',
        ]);
    }
}
