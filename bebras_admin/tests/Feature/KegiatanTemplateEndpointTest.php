<?php

namespace Tests\Feature;

use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature tests untuk endpoint AJAX GET /kegiatan/menu/{id}/template
 * Requirements: 5.1
 */
class KegiatanTemplateEndpointTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function buatAdminUser(): User
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

    // -----------------------------------------------------------------------
    // Tests
    // -----------------------------------------------------------------------

    /**
     * GET /kegiatan/menu/{id}/template pada menu root dengan template 'workshop'
     * harus mengembalikan JSON {"template": "workshop"}.
     * Requirements: 5.1
     */
    public function test_endpoint_mengembalikan_template_menu_root(): void
    {
        $admin = $this->buatAdminUser();

        $menuRoot = MenuKegiatan::create([
            'nama_menu' => 'Workshop',
            'slug'      => 'workshop',
            'urutan'    => 1,
            'template'  => 'workshop',
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('kegiatan.menu.template', $menuRoot->id));

        $response->assertOk()
            ->assertJson(['template' => 'workshop']);
    }

    /**
     * GET /kegiatan/menu/{id}/template pada menu child dengan parent ber-template
     * 'bebras_challenge' harus mengembalikan JSON {"template": "bebras_challenge"}.
     * Requirements: 5.1
     */
    public function test_endpoint_mengembalikan_template_dari_parent_untuk_menu_child(): void
    {
        $admin = $this->buatAdminUser();

        $parent = MenuKegiatan::create([
            'nama_menu' => 'Bebras Challenge',
            'slug'      => 'bebras-challenge',
            'urutan'    => 1,
            'template'  => 'bebras_challenge',
        ]);

        $child = MenuKegiatan::create([
            'parent_id' => $parent->id,
            'nama_menu' => 'Bebras Challenge 2024',
            'slug'      => 'bebras-challenge-2024',
            'urutan'    => 1,
            'template'  => null,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('kegiatan.menu.template', $child->id));

        $response->assertOk()
            ->assertJson(['template' => 'bebras_challenge']);
    }

    /**
     * GET /kegiatan/menu/{id}/template dengan ID yang tidak ada
     * harus mengembalikan HTTP 404.
     * Requirements: 5.1
     */
    public function test_endpoint_mengembalikan_404_untuk_id_tidak_ada(): void
    {
        $admin = $this->buatAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('kegiatan.menu.template', 9999));

        $response->assertNotFound();
    }

    /**
     * Akses tanpa autentikasi ke endpoint template
     * harus redirect ke halaman login.
     * Requirements: 5.1
     */
    public function test_akses_tanpa_auth_diredirect_ke_login(): void
    {
        $response = $this->get(route('kegiatan.menu.template', 999));

        $response->assertRedirect(route('login'));
    }
}
