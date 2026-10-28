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
                'nama'     => 'Budi Santoso',
                'email'    => 'budi@itb.ac.id',
                'username' => 'budi_admin_test',
                'password' => 'secret123',
                'telepon'  => '0899123456',
            ]
        );
        $storeRes->assertStatus(200);
        $storeRes->assertJson(['status' => 'success']);

        $koordinator = KoordinatorBiro::where('email', 'budi@itb.ac.id')->first();
        $this->assertNotNull($koordinator);
        $this->assertEquals('Budi Santoso', $koordinator->nama);
        $this->assertEquals('budi_admin_test', $koordinator->username);
        $this->assertEquals('secret123', $koordinator->password);

        // Pastikan akun user login terbuat sesuai seed
        $this->assertDatabaseHas('users', [
            'id'        => $koordinator->user_id,
            'email'     => 'budi@itb.ac.id',
            'username'  => 'budi_admin_test',
            'kode_biro' => $this->biroUser->kode_biro,
        ]);
        $this->assertTrue($koordinator->user->hasRole('biro'));

        // 2. Update Koordinator
        $updateRes = $this->actingAs($this->adminUser)->putJson(
            route('biro.koordinator.update', $koordinator->id),
            [
                'nama'     => 'Budi Santoso, M.Kom.',
                'email'    => 'budi_updated@itb.ac.id',
                'username' => 'budi_updated',
                'password' => 'newpassword456',
            ]
        );
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('koordinator_biro', [
            'id'       => $koordinator->id,
            'nama'     => 'Budi Santoso, M.Kom.',
            'email'    => 'budi_updated@itb.ac.id',
            'username' => 'budi_updated',
            'password' => 'newpassword456',
        ]);
        $this->assertDatabaseHas('users', [
            'id'       => $koordinator->user_id,
            'email'    => 'budi_updated@itb.ac.id',
            'username' => 'budi_updated',
        ]);

        $koordUserId = $koordinator->user_id;

        // 3. Delete Koordinator
        $delRes = $this->actingAs($this->adminUser)->deleteJson(
            route('biro.koordinator.destroy', $koordinator->id)
        );
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('koordinator_biro', [
            'id' => $koordinator->id,
        ]);
        // Akun user koordinator juga dihapus karena biro masih memiliki biroUser utama
        $this->assertDatabaseMissing('users', [
            'id' => $koordUserId,
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

    public function test_biro_dengan_kode_biro_sama_tidak_duplikat_di_admin_dan_menghitung_semua_koordinator(): void
    {
        $kodeBiro = '777';
        $biroRole = Role::firstOrCreate(['name' => 'biro']);

        // Akun 1
        $user1 = User::create([
            'name'      => 'Biro Uji Duplikat',
            'username'  => 'biro_dup_1_' . uniqid(),
            'email'     => 'dup1_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => $kodeBiro,
        ]);
        $user1->roles()->attach($biroRole->id);
        $user1->koordinatorBiro()->create([
            'nama'  => 'Koordinator 1',
            'email' => 'k1@example.com',
        ]);

        // Akun 2 dengan kode_biro yang sama
        $user2 = User::create([
            'name'      => 'Biro Uji Duplikat',
            'username'  => 'biro_dup_2_' . uniqid(),
            'email'     => 'dup2_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => $kodeBiro,
        ]);
        $user2->roles()->attach($biroRole->id);
        $user2->koordinatorBiro()->create([
            'nama'  => 'Koordinator 2',
            'email' => 'k2@example.com',
        ]);

        $res = $this->actingAs($this->adminUser)->getJson(route('biro.list'), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);
        $res->assertStatus(200);

        $json = $res->json();
        $matchingRows = array_filter($json['data'], fn($row) => ($row['kode_biro'] ?? '') === $kodeBiro);

        // Hanya boleh ada 1 baris untuk kode biro 777 (tidak duplikat!)
        $this->assertCount(1, $matchingRows);

        $row = reset($matchingRows);
        // Menghitung seluruh koordinator dari kedua akun (2 Koordinator)
        $this->assertStringContainsString('2 Koordinator', $row['jumlah_koordinator']);

        // Modal kelola koordinator juga harus mengembalikan kedua koordinator
        $koordRes = $this->actingAs($this->adminUser)->getJson(route('biro.koordinator.get', $row['id']));
        $koordRes->assertStatus(200);
        $koordList = $koordRes->json('koordinator');
        $this->assertCount(2, $koordList);
    }
}
