<?php

namespace Tests\Feature;

use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\BiroSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BiroAdminTest extends TestCase
{
    use DatabaseTransactions;

    private User $adminUser;
    private User $biroUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $biroRole  = Role::firstOrCreate(['name' => 'biro']);

        $this->adminUser = User::create([
            'name'      => 'Admin Test',
            'username'  => 'admin_test_' . uniqid(),
            'email'     => 'admin_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
        ]);
        $this->adminUser->roles()->attach($adminRole->id);

        $this->biroUser = User::create([
            'name'      => 'Biro Test ITB',
            'username'  => 'biro_itb_' . uniqid(),
            'email'     => 'biro_itb_' . uniqid() . '@itb.ac.id',
            'password'  => Hash::make('password'),
            'kode_biro' => '999',
        ]);
        $this->biroUser->roles()->attach($biroRole->id);
    }

    public function test_guest_cannot_access_manajemen_biro(): void
    {
        $response = $this->get(route('biro.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_manajemen_biro_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('biro.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Biro Bebras');
    }

    public function test_admin_can_fetch_biro_datatables_list(): void
    {
        $this->biroUser->koordinatorBiro()->create([
            'nama'  => 'Koordinator Test',
            'email' => 'coord@itb.ac.id',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('biro.list'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $response->assertSee('Biro Test ITB');
    }

    public function test_admin_can_update_kode_biro(): void
    {
        $response = $this->actingAs($this->adminUser)->putJson(
            route('biro.updateKode', $this->biroUser->id),
            ['kode_biro' => '888']
        );

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('users', [
            'id'        => $this->biroUser->id,
            'kode_biro' => '888',
        ]);
    }

    public function test_admin_can_add_edit_and_delete_koordinator_biro(): void
    {
        // 1. Add Koordinator
        $storeRes = $this->actingAs($this->adminUser)->postJson(
            route('biro.koordinator.store', $this->biroUser->id),
            [
                'nama'  => 'Budi Santoso',
                'email' => 'budi@itb.ac.id',
            ]
        );
        $storeRes->assertStatus(200);
        $storeRes->assertJson(['status' => 'success']);

        $koordinator = KoordinatorBiro::where('email', 'budi@itb.ac.id')->first();
        $this->assertNotNull($koordinator);
        $this->assertEquals('Budi Santoso', $koordinator->nama);

        // 2. Update Koordinator
        $updateRes = $this->actingAs($this->adminUser)->putJson(
            route('biro.koordinator.update', $koordinator->id),
            [
                'nama'  => 'Budi Santoso, M.Kom.',
                'email' => 'budi_updated@itb.ac.id',
            ]
        );
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('koordinator_biro', [
            'id'    => $koordinator->id,
            'nama'  => 'Budi Santoso, M.Kom.',
            'email' => 'budi_updated@itb.ac.id',
        ]);

        // 3. Delete Koordinator
        $delRes = $this->actingAs($this->adminUser)->deleteJson(
            route('biro.koordinator.destroy', $koordinator->id)
        );
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('koordinator_biro', [
            'id' => $koordinator->id,
        ]);
    }

    public function test_login_supports_both_username_and_email(): void
    {
        // Test login via username
        $loginUsername = $this->post(route('login.post'), [
            'username' => $this->adminUser->username,
            'password' => 'password',
        ]);
        $loginUsername->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);

        $this->post(route('logout'));
        $this->assertGuest();

        // Test login via email
        $loginEmail = $this->post(route('login.post'), [
            'username' => $this->adminUser->email,
            'password' => 'password',
        ]);
        $loginEmail->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }
}
