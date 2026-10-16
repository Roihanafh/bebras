<?php

namespace Tests\Feature;

use App\Models\AnggotaBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AnggotaBiroRoleTest extends TestCase
{
    use DatabaseTransactions;

    private User $biroA;
    private User $biroB;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $this->biroA = User::create([
            'name'      => 'Biro A ' . uniqid(),
            'username'  => 'biro_a_' . uniqid(),
            'email'     => 'biro_a_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => '001',
        ]);
        $this->biroA->roles()->attach($biroRole->id);

        $this->biroB = User::create([
            'name'      => 'Biro B ' . uniqid(),
            'username'  => 'biro_b_' . uniqid(),
            'email'     => 'biro_b_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => '002',
        ]);
        $this->biroB->roles()->attach($biroRole->id);

        $this->regularUser = User::create([
            'name'      => 'Regular User',
            'username'  => 'regular_' . uniqid(),
            'email'     => 'regular_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
        ]);
        $this->regularUser->roles()->attach($userRole->id);
    }

    public function test_user_biro_dapat_mengakses_halaman_anggota_biro(): void
    {
        $response = $this->actingAs($this->biroA)->get(route('biro_anggota.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Anggota Biro Saya');
    }

    public function test_non_biro_tidak_bisa_mengakses_halaman_anggota_biro(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('biro_anggota.index'));
        $response->assertStatus(403);
    }

    public function test_pengajuan_anggota_dari_biro_otomatis_berstatus_pending(): void
    {
        $response = $this->actingAs($this->biroA)->postJson(route('biro_anggota.store'), [
            'nama'            => 'Siswa Baru Diajukan',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2006-08-17',
            'no_anggota'      => '001',
            // Coba kirim status approved secara sengaja, harus tetap diabaikan dan menjadi pending!
            'status_validasi' => 'approved',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('anggota_biro', [
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Siswa Baru Diajukan',
            'no_anggota'      => '001',
            'status_validasi' => 'pending', // Harus tetap pending!
        ]);
    }

    public function test_user_biro_hanya_melihat_anggota_milik_bironya_sendiri(): void
    {
        // Anggota milik Biro A
        $anggotaA = AnggotaBiro::create([
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Anggota Khusus Biro A',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'pending',
        ]);

        // Anggota milik Biro B
        $anggotaB = AnggotaBiro::create([
            'biro_id'         => $this->biroB->id,
            'nama'            => 'Anggota Khusus Biro B',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-02-02',
            'no_anggota'      => '001',
            'status_validasi' => 'pending',
        ]);

        $response = $this->actingAs($this->biroA)->getJson(route('biro_anggota.list'));
        $response->assertStatus(200);

        $response->assertSee('Anggota Khusus Biro A');
        $response->assertDontSee('Anggota Khusus Biro B');
    }

    public function test_user_biro_tidak_bisa_mengubah_anggota_milik_biro_lain(): void
    {
        $anggotaB = AnggotaBiro::create([
            'biro_id'         => $this->biroB->id,
            'nama'            => 'Anggota Asli Biro B',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-02-02',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        // Biro A mencoba mengupdate anggota milik Biro B
        $response = $this->actingAs($this->biroA)->putJson(url('/anggota-biro/' . $anggotaB->id), [
            'nama'          => 'Diretas oleh Biro A',
            'tahun_masuk'   => 2024,
            'tanggal_lahir' => '2005-02-02',
            'no_anggota'    => '001',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('anggota_biro', [
            'id'   => $anggotaB->id,
            'nama' => 'Anggota Asli Biro B',
        ]);
    }

    public function test_user_biro_tidak_bisa_menghapus_anggota_milik_biro_lain(): void
    {
        $anggotaB = AnggotaBiro::create([
            'biro_id'         => $this->biroB->id,
            'nama'            => 'Anggota Kebal Biro B',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-02-02',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        // Biro A mencoba menghapus anggota milik Biro B
        $response = $this->actingAs($this->biroA)->deleteJson(url('/anggota-biro/' . $anggotaB->id));

        $response->assertStatus(404);
        $this->assertDatabaseHas('anggota_biro', [
            'id' => $anggotaB->id,
        ]);
    }

    public function test_user_biro_tidak_bisa_mengubah_status_validasi_menjadi_approved(): void
    {
        $anggotaA = AnggotaBiro::create([
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Anggota Pending',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'pending',
        ]);

        // Biro A mengupdate data nama dan mencoba menyisipkan status_validasi = approved
        $response = $this->actingAs($this->biroA)->putJson(url('/anggota-biro/' . $anggotaA->id), [
            'nama'            => 'Anggota Diupdate',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('anggota_biro', [
            'id'              => $anggotaA->id,
            'nama'            => 'Anggota Diupdate',
            'status_validasi' => 'pending', // Harus tetap pending!
        ]);
    }
}
