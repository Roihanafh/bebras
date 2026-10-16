<?php

namespace Tests\Feature;

use App\Models\AnggotaBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnggotaBiroManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $biroA;
    private User $biroB;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $biroRole = Role::firstOrCreate(['name' => 'biro']);

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'username' => 'admin_' . uniqid(),
            'email'    => 'admin_' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->admin->roles()->attach($adminRole->id);

        $this->biroA = User::create([
            'name'      => 'Biro A ' . uniqid(),
            'username'  => 'biro_a_' . uniqid(),
            'email'     => 'biroa_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => '001',
        ]);
        $this->biroA->roles()->attach($biroRole->id);

        $this->biroB = User::create([
            'name'      => 'Biro B ' . uniqid(),
            'username'  => 'biro_b_' . uniqid(),
            'email'     => 'birob_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
            'kode_biro' => '002',
        ]);
        $this->biroB->roles()->attach($biroRole->id);
    }

    public function test_tabel_anggota_biro_dan_kolom_yang_dibutuhkan_tersedia(): void
    {
        $this->assertTrue(Schema::hasTable('anggota_biro'), 'Tabel anggota_biro harus ada.');
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'id'));
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'biro_id'));
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'nama'));
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'tahun_masuk'));
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'tanggal_lahir'));
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'no_anggota'));
        $this->assertTrue(Schema::hasColumn('anggota_biro', 'status_validasi'));

        // Pastikan TIDAK ADA field email
        $this->assertFalse(Schema::hasColumn('anggota_biro', 'email'), 'Tabel anggota_biro TIDAK BOLEH memiliki kolom email.');
    }

    public function test_composite_unique_biro_id_dan_no_anggota(): void
    {
        // Biro A no 001 -> Valid
        $anggotaA1 = AnggotaBiro::create([
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Budi Biro A',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);
        $this->assertDatabaseHas('anggota_biro', ['id' => $anggotaA1->id, 'no_anggota' => '001']);

        // Biro B no 001 -> Valid (Biro berbeda boleh memiliki no_anggota yang sama)
        $anggotaB1 = AnggotaBiro::create([
            'biro_id'         => $this->biroB->id,
            'nama'            => 'Andi Biro B',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-02-02',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);
        $this->assertDatabaseHas('anggota_biro', ['id' => $anggotaB1->id, 'no_anggota' => '001']);

        // Biro A no 001 lagi -> Duplicate / Gagal constraint database
        $this->expectException(QueryException::class);
        AnggotaBiro::create([
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Duplikat Biro A',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-03-03',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);
    }

    public function test_flow_penomoran_otomatis_per_biro(): void
    {
        // Belum ada anggota -> 001
        $nextA = AnggotaBiro::generateNextNoAnggota($this->biroA->id);
        $this->assertEquals('001', $nextA);

        // Tambah anggota pertama
        AnggotaBiro::create([
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Anggota 1',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        // Setelah 001 -> 002
        $nextA2 = AnggotaBiro::generateNextNoAnggota($this->biroA->id);
        $this->assertEquals('002', $nextA2);

        // Biro B yang belum memiliki anggota tetap mulai dari 001
        $nextB = AnggotaBiro::generateNextNoAnggota($this->biroB->id);
        $this->assertEquals('001', $nextB);
    }

    public function test_admin_can_access_anggota_biro_page_and_crud(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.anggota_biro.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Anggota Biro');

        // Admin Tambah Anggota Langsung
        $storeRes = $this->actingAs($this->admin)->postJson(route('admin.anggota_biro.store'), [
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Anggota Dibuat Admin',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2004-05-10',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        $storeRes->assertStatus(200);
        $this->assertDatabaseHas('anggota_biro', [
            'nama'            => 'Anggota Dibuat Admin',
            'biro_id'         => $this->biroA->id,
            'status_validasi' => 'approved',
        ]);
        $anggotaId = $storeRes->json('data.id');

        // Admin Edit Anggota
        $updateRes = $this->actingAs($this->admin)->putJson(url('/admin/anggota-biro/' . $anggotaId), [
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Nama Telah Diupdate',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2004-05-10',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('anggota_biro', ['nama' => 'Nama Telah Diupdate']);

        // Admin Hapus Anggota
        $delRes = $this->actingAs($this->admin)->deleteJson(url('/admin/anggota-biro/' . $anggotaId));
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('anggota_biro', ['id' => $anggotaId]);
    }

    public function test_admin_can_approve_and_reject_pengajuan_anggota(): void
    {
        // Anggota berstatus pending dari biro
        $anggota = AnggotaBiro::create([
            'biro_id'         => $this->biroA->id,
            'nama'            => 'Calon Anggota',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2006-03-15',
            'no_anggota'      => '001',
            'status_validasi' => 'pending',
        ]);

        // Admin Approve
        $approveRes = $this->actingAs($this->admin)->postJson(url('/admin/anggota-biro/' . $anggota->id . '/approve'));
        $approveRes->assertStatus(200);
        $this->assertDatabaseHas('anggota_biro', [
            'id'              => $anggota->id,
            'status_validasi' => 'approved',
        ]);

        // Admin Reject
        $rejectRes = $this->actingAs($this->admin)->postJson(url('/admin/anggota-biro/' . $anggota->id . '/reject'));
        $rejectRes->assertStatus(200);
        $this->assertDatabaseHas('anggota_biro', [
            'id'              => $anggota->id,
            'status_validasi' => 'rejected',
        ]);
    }
}
