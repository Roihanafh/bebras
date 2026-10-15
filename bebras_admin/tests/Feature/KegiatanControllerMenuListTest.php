<?php

namespace Tests\Feature;

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KegiatanControllerMenuListTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function buatAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        $admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test_' . uniqid(),
            'email'    => 'admin_test_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->roles()->attach($role->id);

        return $admin;
    }

    private function buatMenuRoot(array $atribut = []): MenuKegiatan
    {
        return MenuKegiatan::create(array_merge([
            'parent_id' => null,
            'nama_menu' => 'Menu Root ' . uniqid(),
            'slug'      => 'menu-root-' . uniqid(),
            'urutan'    => 0,
        ], $atribut));
    }

    private function buatMenuChild(MenuKegiatan $parent, array $atribut = []): MenuKegiatan
    {
        return MenuKegiatan::create(array_merge([
            'parent_id' => $parent->id,
            'nama_menu' => 'Menu Child ' . uniqid(),
            'slug'      => 'menu-child-' . uniqid(),
            'urutan'    => 0,
        ], $atribut));
    }

    // -----------------------------------------------------------------------
    // Test: create() — menuList hanya berisi menu leaf
    // Requirements: 3.1
    // -----------------------------------------------------------------------

    /**
     * Halaman create kegiatan harus mengirim variabel $menuList ke view yang
     * hanya berisi menu tanpa children (menu leaf). Menu yang sudah memiliki
     * sub-menu tidak boleh muncul dalam dropdown.
     *
     * Requirements: 3.1
     */
    public function test_create_menu_list_hanya_berisi_menu_leaf(): void
    {
        $admin = $this->buatAdmin();

        // Menu root tanpa children (leaf) — harus muncul
        $menuLeaf = $this->buatMenuRoot(['nama_menu' => 'Menu Leaf', 'slug' => 'menu-leaf']);

        // Menu root yang punya child (non-leaf) — tidak boleh muncul
        $menuParent = $this->buatMenuRoot(['nama_menu' => 'Menu Parent', 'slug' => 'menu-parent']);
        $this->buatMenuChild($menuParent);

        $response = $this->actingAs($admin)
            ->get(route('kegiatan.create'));

        $response->assertStatus(200);
        $response->assertViewHas('menuList');

        $menuList = $response->viewData('menuList');

        // Menu leaf harus ada dalam list
        $this->assertTrue(
            $menuList->contains('id', $menuLeaf->id),
            'Menu leaf harus muncul dalam $menuList'
        );

        // Menu yang punya children tidak boleh ada dalam list
        $this->assertFalse(
            $menuList->contains('id', $menuParent->id),
            'Menu yang memiliki children tidak boleh muncul dalam $menuList'
        );
    }

    /**
     * Ketika tidak ada menu yang terdaftar, $menuList tetap dikirim ke view
     * sebagai koleksi kosong.
     *
     * Requirements: 3.1
     */
    public function test_create_menu_list_kosong_jika_tidak_ada_menu(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->actingAs($admin)
            ->get(route('kegiatan.create'));

        $response->assertStatus(200);
        $response->assertViewHas('menuList');

        $menuList = $response->viewData('menuList');

        $this->assertTrue($menuList->isEmpty(), '$menuList harus kosong jika tidak ada menu');
    }

    // -----------------------------------------------------------------------
    // Test: edit() — menu non-leaf tetap muncul dengan flag $isMenuNotLeaf
    // Requirements: 3.4
    // -----------------------------------------------------------------------

    /**
     * Halaman edit kegiatan yang menu-nya sudah tidak lagi menjadi leaf (karena
     * telah ditambahkan sub-menu setelah kegiatan dibuat) harus:
     * 1. Tetap menampilkan menu tersebut sebagai opsi terpilih dalam $menuList
     * 2. Mengirim flag $isMenuNotLeaf = true ke view
     *
     * Requirements: 3.4
     */
    public function test_edit_menu_non_leaf_tetap_muncul_dengan_flag_is_menu_not_leaf(): void
    {
        $admin = $this->buatAdmin();

        // Buat menu root yang pada awalnya merupakan leaf
        $menu = $this->buatMenuRoot(['nama_menu' => 'Menu Kegiatan', 'slug' => 'menu-kegiatan-test']);

        // Buat kegiatan yang menggunakan menu tersebut
        $kegiatan = Kegiatan::create([
            'menu_kegiatan_id' => $menu->id,
            'tipe'             => 'kegiatan_menu',
            'judul'            => 'Kegiatan Test',
            'deskripsi'        => 'Deskripsi test',
            'urutan'           => 0,
            'status_validasi'  => 'approved',
        ]);

        // Setelah kegiatan dibuat, tambahkan child ke menu (membuatnya non-leaf)
        $this->buatMenuChild($menu);

        $response = $this->actingAs($admin)
            ->get(route('kegiatan.edit', $kegiatan->id));

        $response->assertStatus(200);
        $response->assertViewHas('menuList');
        $response->assertViewHas('isMenuNotLeaf', true);

        $menuList = $response->viewData('menuList');

        // Menu yang sudah non-leaf harus tetap ada dalam $menuList agar data tidak hilang
        $this->assertTrue(
            $menuList->contains('id', $menu->id),
            'Menu non-leaf harus tetap muncul dalam $menuList pada mode edit'
        );
    }

    /**
     * Halaman edit kegiatan yang menu-nya masih leaf tidak mengirim flag
     * $isMenuNotLeaf = true.
     *
     * Requirements: 3.4
     */
    public function test_edit_menu_leaf_tidak_punya_flag_is_menu_not_leaf(): void
    {
        $admin = $this->buatAdmin();

        $menu = $this->buatMenuRoot(['nama_menu' => 'Menu Leaf Edit', 'slug' => 'menu-leaf-edit']);

        $kegiatan = Kegiatan::create([
            'menu_kegiatan_id' => $menu->id,
            'tipe'             => 'kegiatan_menu',
            'judul'            => 'Kegiatan Leaf Test',
            'deskripsi'        => 'Deskripsi test',
            'urutan'           => 0,
            'status_validasi'  => 'approved',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('kegiatan.edit', $kegiatan->id));

        $response->assertStatus(200);

        // Tidak boleh ada flag isMenuNotLeaf = true
        $isMenuNotLeaf = $response->viewData('isMenuNotLeaf');
        $this->assertFalse(
            (bool) $isMenuNotLeaf,
            '$isMenuNotLeaf harus false/null jika menu masih leaf'
        );
    }
}
