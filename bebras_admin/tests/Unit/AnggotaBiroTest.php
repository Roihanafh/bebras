<?php

namespace Tests\Unit;

use App\Models\AnggotaBiro;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnggotaBiroTest extends TestCase
{
    use DatabaseTransactions;

    public function test_anggota_biro_table_name(): void
    {
        $anggota = new AnggotaBiro();
        $this->assertEquals('anggota_biro', $anggota->getTable());
    }

    public function test_anggota_biro_fillable_attributes(): void
    {
        $anggota = new AnggotaBiro();
        $expected = [
            'biro_id',
            'nama',
            'tahun_masuk',
            'tanggal_lahir',
            'no_anggota',
            'status_validasi',
        ];

        $this->assertEquals($expected, $anggota->getFillable());
    }

    public function test_anggota_biro_casts_attributes(): void
    {
        $anggota = new AnggotaBiro();
        $casts = $anggota->getCasts();

        $this->assertEquals('integer', $casts['tahun_masuk']);
        $this->assertEquals('date', $casts['tanggal_lahir']);
    }

    public function test_anggota_biro_relations_definition(): void
    {
        $anggota = new AnggotaBiro();

        $biroRel = $anggota->biro();
        $this->assertInstanceOf(BelongsTo::class, $biroRel);
        $this->assertInstanceOf(User::class, $biroRel->getRelated());
        $this->assertEquals('biro_id', $biroRel->getForeignKeyName());

        $userRel = $anggota->user();
        $this->assertInstanceOf(BelongsTo::class, $userRel);
        $this->assertInstanceOf(User::class, $userRel->getRelated());
        $this->assertEquals('biro_id', $userRel->getForeignKeyName());
    }

    public function test_anggota_biro_scopes_filter_by_status(): void
    {
        $approvedQuery = AnggotaBiro::query()->approved();
        $this->assertStringContainsString('status_validasi', $approvedQuery->toSql());
        $this->assertContains('approved', $approvedQuery->getBindings());

        $pendingQuery = AnggotaBiro::query()->pending();
        $this->assertStringContainsString('status_validasi', $pendingQuery->toSql());
        $this->assertContains('pending', $pendingQuery->getBindings());

        $rejectedQuery = AnggotaBiro::query()->rejected();
        $this->assertStringContainsString('status_validasi', $rejectedQuery->toSql());
        $this->assertContains('rejected', $rejectedQuery->getBindings());
    }

    public function test_generate_next_no_anggota_returns_001_for_new_biro(): void
    {
        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $biro = User::create([
            'name'      => 'Biro Baru Test',
            'username'  => 'biro_baru_' . uniqid(),
            'email'     => 'biro_baru_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '101',
        ]);
        $biro->roles()->attach($biroRole->id);

        $nextNo = AnggotaBiro::generateNextNoAnggota($biro->id);
        $this->assertEquals('001', $nextNo);
    }

    public function test_generate_next_no_anggota_increments_and_zero_pads_correctly(): void
    {
        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $biro = User::create([
            'name'      => 'Biro Increment Test',
            'username'  => 'biro_inc_' . uniqid(),
            'email'     => 'biro_inc_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '102',
        ]);
        $biro->roles()->attach($biroRole->id);

        // Anggota pertama dengan no_anggota 009
        AnggotaBiro::create([
            'biro_id'         => $biro->id,
            'nama'            => 'Anggota 9',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '009',
            'status_validasi' => 'approved',
        ]);

        $nextNo = AnggotaBiro::generateNextNoAnggota($biro->id);
        $this->assertEquals('010', $nextNo);

        // Tambah lagi dengan no_anggota 099 -> berikutnya harus 100
        AnggotaBiro::create([
            'biro_id'         => $biro->id,
            'nama'            => 'Anggota 99',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '099',
            'status_validasi' => 'approved',
        ]);

        $nextNo100 = AnggotaBiro::generateNextNoAnggota($biro->id);
        $this->assertEquals('100', $nextNo100);
    }
}
