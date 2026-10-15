<?php

namespace Tests\Feature;

use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BiroRoleKoordinatorTest extends TestCase
{
    use DatabaseTransactions;

    private User $biro1;
    private User $biro2;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $this->biro1 = User::create([
            'name'      => 'Biro ITB',
            'username'  => 'biro_itb_' . uniqid(),
            'email'     => 'itb_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => '001',
        ]);
        $this->biro1->roles()->attach($biroRole->id);

        $this->biro2 = User::create([
            'name'      => 'Biro UI',
            'username'  => 'biro_ui_' . uniqid(),
            'email'     => 'ui_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => '002',
        ]);
        $this->biro2->roles()->attach($biroRole->id);

        $this->regularUser = User::create([
            'name'     => 'Regular User',
            'username' => 'user_' . uniqid(),
            'email'    => 'user_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->regularUser->roles()->attach($userRole->id);
    }

    public function test_user_biro_dapat_mengakses_halaman_koordinator_saya(): void
    {
        $response = $this->actingAs($this->biro1)->get(route('biro_koordinator.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Koordinator Biro');
        $response->assertSee('Biro ITB');
    }

    public function test_non_biro_tidak_bisa_mengakses_halaman_koordinator_biro(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('biro_koordinator.index'));
        $response->assertStatus(403);
    }

    public function test_user_biro_hanya_melihat_koordinator_miliknya_sendiri(): void
    {
        $koor1 = $this->biro1->koordinatorBiro()->create([
            'nama'  => 'Koordinator ITB',
            'email' => 'koor@itb.ac.id',
        ]);

        $koor2 = $this->biro2->koordinatorBiro()->create([
            'nama'  => 'Koordinator UI',
            'email' => 'koor@ui.ac.id',
        ]);

        $response = $this->actingAs($this->biro1)->getJson(route('biro_koordinator.list'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $json = $response->json();
        $names = collect($json['data'])->pluck('nama')->all();

        $this->assertContains('Koordinator ITB', $names);
        $this->assertNotContains('Koordinator UI', $names, 'Biro 1 tidak boleh melihat koordinator Biro 2');
    }

    public function test_user_biro_dapat_menambah_koordinator_dan_otomatis_terhubung_ke_dirinya(): void
    {
        $response = $this->actingAs($this->biro1)->postJson(route('biro_koordinator.store'), [
            'nama'  => 'Koordinator Baru ITB',
            'email' => 'baru@itb.ac.id',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('koordinator_biro', [
            'user_id' => $this->biro1->id,
            'nama'    => 'Koordinator Baru ITB',
            'email'   => 'baru@itb.ac.id',
        ]);
    }

    public function test_user_biro_dapat_mengubah_koordinator_miliknya(): void
    {
        $koor = $this->biro1->koordinatorBiro()->create([
            'nama'  => 'Nama Lama',
            'email' => 'lama@itb.ac.id',
        ]);

        $response = $this->actingAs($this->biro1)->putJson(route('biro_koordinator.update', $koor->id), [
            'nama'  => 'Nama Update',
            'email' => 'update@itb.ac.id',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('koordinator_biro', [
            'id'    => $koor->id,
            'nama'  => 'Nama Update',
            'email' => 'update@itb.ac.id',
        ]);
    }

    public function test_user_biro_TIDAK_bisa_mengubah_koordinator_milik_biro_lain(): void
    {
        $koorBiro2 = $this->biro2->koordinatorBiro()->create([
            'nama'  => 'Koordinator Milik Biro 2',
            'email' => 'koor@ui.ac.id',
        ]);

        // Biro 1 mencoba mengubah koordinator Biro 2
        $response = $this->actingAs($this->biro1)->putJson(route('biro_koordinator.update', $koorBiro2->id), [
            'nama'  => 'Percobaan Hack Nama',
            'email' => 'hacked@ui.ac.id',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('koordinator_biro', [
            'id'   => $koorBiro2->id,
            'nama' => 'Percobaan Hack Nama',
        ]);
    }

    public function test_user_biro_dapat_menghapus_koordinator_miliknya(): void
    {
        $koor = $this->biro1->koordinatorBiro()->create([
            'nama'  => 'Koordinator Hapus',
            'email' => 'hapus@itb.ac.id',
        ]);

        $response = $this->actingAs($this->biro1)->deleteJson(route('biro_koordinator.destroy', $koor->id));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('koordinator_biro', [
            'id' => $koor->id,
        ]);
    }

    public function test_user_biro_TIDAK_bisa_menghapus_koordinator_milik_biro_lain(): void
    {
        $koorBiro2 = $this->biro2->koordinatorBiro()->create([
            'nama'  => 'Koordinator Biro 2 Tetap Aman',
            'email' => 'aman@ui.ac.id',
        ]);

        // Biro 1 mencoba menghapus koordinator Biro 2
        $response = $this->actingAs($this->biro1)->deleteJson(route('biro_koordinator.destroy', $koorBiro2->id));
        $response->assertStatus(404);
        $this->assertDatabaseHas('koordinator_biro', [
            'id' => $koorBiro2->id,
        ]);
    }
}
