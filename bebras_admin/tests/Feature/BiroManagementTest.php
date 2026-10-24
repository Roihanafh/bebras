<?php

namespace Tests\Feature;

use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BiroManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $biroUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $biroRole  = Role::firstOrCreate(['name' => 'biro']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_test_' . uniqid(),
            'email'    => 'admin_test_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->admin->roles()->attach($adminRole->id);

        $this->biroUser = User::create([
            'name'     => 'Institut Teknologi Bandung',
            'username' => 'biro_itb_' . uniqid(),
            'email'    => 'biro_itb_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->biroUser->roles()->attach($biroRole->id);

        $this->regularUser = User::create([
            'name'     => 'Regular User',
            'username' => 'user_' . uniqid(),
            'email'    => 'user_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->regularUser->roles()->attach($userRole->id);
    }

    public function test_kolom_kode_biro_dan_tabel_koordinator_tersedia(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'kode_biro'), 'Kolom kode_biro harus ada di tabel users');
        $this->assertTrue(Schema::hasTable('koordinator_biro'), 'Tabel koordinator_biro harus ada');
        $this->assertTrue(Schema::hasColumn('koordinator_biro', 'user_id'));
        $this->assertTrue(Schema::hasColumn('koordinator_biro', 'nama'));
        $this->assertTrue(Schema::hasColumn('koordinator_biro', 'email'));
    }

    public function test_admin_dapat_mengakses_halaman_biro(): void
    {
        $response = $this->actingAs($this->admin)->get(route('biro.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Biro Bebras');
    }

    public function test_daftar_biro_hanya_menampilkan_user_role_biro(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('biro.list'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $json = $response->json();
        $names = collect($json['data'])->pluck('name')->all();

        $this->assertContains($this->biroUser->name, $names, 'User dengan role biro harus muncul');
        $this->assertNotContains($this->regularUser->name, $names, 'User tanpa role biro tidak boleh muncul');
    }

    public function test_admin_dapat_mengubah_kode_biro(): void
    {
        $response = $this->actingAs($this->admin)->putJson(route('biro.updateKode', $this->biroUser->id), [
            'kode_biro' => '001',
        ]);

        $response->assertStatus(200);
        $this->biroUser->refresh();
        $this->assertEquals('001', $this->biroUser->kode_biro);
    }

    public function test_admin_dapat_crud_koordinator(): void
    {
        // 1. Create Koordinator 1
        $storeResponse = $this->actingAs($this->admin)->postJson(route('biro.koordinator.store', $this->biroUser->id), [
            'nama'  => 'Agung Dewandaru',
            'email' => 'agung@itb.ac.id',
        ]);
        $storeResponse->assertStatus(200);
        $koorId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('koordinator_biro', [
            'id'    => $koorId,
            'nama'  => 'Agung Dewandaru',
            'email' => 'agung@itb.ac.id',
        ]);

        $this->biroUser->refresh();
        $koorModel = KoordinatorBiro::with('user')->find($koorId);
        $this->assertNotNull($koorModel->user);
        $this->assertEquals($this->biroUser->kode_biro, $koorModel->user->kode_biro);
        $this->assertTrue($koorModel->user->hasRole('biro'));

        // 2. Read Koordinator
        $getResponse = $this->actingAs($this->admin)->getJson(route('biro.koordinator.get', $this->biroUser->id));
        $getResponse->assertStatus(200);
        $koordinators = $getResponse->json('koordinator');
        $this->assertCount(1, $koordinators);

        // 3. Update Koordinator
        $updateResponse = $this->actingAs($this->admin)->putJson(route('biro.koordinator.update', $koorId), [
            'nama'  => 'Agung Dewandaru, Ph.D.',
            'email' => 'agung.phd@itb.ac.id',
        ]);
        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('koordinator_biro', [
            'id'    => $koorId,
            'nama'  => 'Agung Dewandaru, Ph.D.',
            'email' => 'agung.phd@itb.ac.id',
        ]);

        // 4. Delete Koordinator
        $deleteResponse = $this->actingAs($this->admin)->deleteJson(route('biro.koordinator.destroy', $koorId));
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('koordinator_biro', ['id' => $koorId]);
    }

    public function test_satu_biro_dapat_memiliki_banyak_koordinator(): void
    {
        $k1 = $this->biroUser->koordinatorBiro()->create([
            'nama'  => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);
        $k2 = $this->biroUser->koordinatorBiro()->create([
            'nama'  => 'Sinta Maharani',
            'email' => 'sinta@example.com',
        ]);

        $this->biroUser->refresh();
        $this->assertCount(2, $this->biroUser->koordinatorBiro);
        $this->assertEquals($this->biroUser->id, $k1->user->id);
        $this->assertEquals($this->biroUser->id, $k2->user->id);
    }

    public function test_manajemen_akun_tetap_berfungsi(): void
    {
        $roleBiro = Role::where('name', 'biro')->first();

        $response = $this->actingAs($this->admin)->postJson(route('store.akun'), [
            'name'      => 'Biro Baru',
            'username'  => 'biro_baru_' . uniqid(),
            'email'     => 'biro_baru_' . uniqid() . '@example.com',
            'password'  => 'password123',
            'roles'     => [$roleBiro->id],
            'kode_biro' => '005',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name'      => 'Biro Baru',
            'kode_biro' => '005',
        ]);
    }
}
