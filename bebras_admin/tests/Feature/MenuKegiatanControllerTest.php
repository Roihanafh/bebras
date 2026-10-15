<?php

namespace Tests\Feature;

use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
}
