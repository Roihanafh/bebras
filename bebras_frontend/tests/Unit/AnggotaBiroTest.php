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
            'name'      => 'Biro Baru Frontend',
            'username'  => 'biro_baru_fe_' . uniqid(),
            'email'     => 'biro_baru_fe_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '201',
        ]);
        $biro->roles()->attach($biroRole->id);

        $nextNo = AnggotaBiro::generateNextNoAnggota($biro->id);
        $this->assertEquals('001', $nextNo);
    }

    public function test_generate_next_no_anggota_increments_and_zero_pads_correctly(): void
    {
        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $biro = User::create([
            'name'      => 'Biro Increment Frontend',
            'username'  => 'biro_inc_fe_' . uniqid(),
            'email'     => 'biro_inc_fe_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '202',
        ]);
        $biro->roles()->attach($biroRole->id);

        AnggotaBiro::create([
            'biro_id'         => $biro->id,
            'nama'            => 'Anggota 9 FE',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '009',
            'status_validasi' => 'approved',
        ]);

        $nextNo = AnggotaBiro::generateNextNoAnggota($biro->id);
        $this->assertEquals('010', $nextNo);

        AnggotaBiro::create([
            'biro_id'         => $biro->id,
            'nama'            => 'Anggota 99 FE',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '099',
            'status_validasi' => 'approved',
        ]);

        $nextNo100 = AnggotaBiro::generateNextNoAnggota($biro->id);
        $this->assertEquals('100', $nextNo100);
    }

    public function test_formatted_id_member_structure_matches_specification(): void
    {
        $biro = User::create([
            'name'      => 'Biro Polinema',
            'username'  => 'biro_polinema_' . uniqid(),
            'email'     => 'polinema_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '333',
        ]);

        $anggota = new AnggotaBiro([
            'biro_id'         => $biro->id,
            'nama'            => 'M. Unggul Pamenang',
            'tahun_masuk'     => 2026,
            'tanggal_lahir'   => '1991-05-10',
            'no_anggota'      => '764',
            'status_validasi' => 'approved',
        ]);
        $anggota->setRelation('biro', $biro);

        // yyy (333) + xx (26) + zz (91) + aaa (764) = 3332691764 (10 digit)
        $this->assertEquals('3332691764', $anggota->formatted_id_member);
    }

    public function test_formatted_id_member_zero_pads_properly(): void
    {
        $biro = User::create([
            'name'      => 'Biro Single Digit',
            'username'  => 'biro_single_' . uniqid(),
            'email'     => 'single_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '1',
        ]);

        $anggota = new AnggotaBiro([
            'biro_id'         => $biro->id,
            'nama'            => 'Budi Santoso',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2005-02-02',
            'no_anggota'      => '1',
            'status_validasi' => 'approved',
        ]);
        $anggota->setRelation('biro', $biro);

        // yyy (001) + xx (24) + zz (05) + aaa (001) = 0012405001
        $this->assertEquals('0012405001', $anggota->formatted_id_member);
    }

    public function test_expiry_date_is_automatically_one_year_after_registration(): void
    {
        $anggota = new AnggotaBiro([
            'nama' => 'Anggota Expiry Test',
        ]);
        $anggota->created_at = \Carbon\Carbon::parse('2026-10-11 10:00:00');

        $this->assertEquals('2027-10-11', $anggota->expiry_date->format('Y-m-d'));
        $this->assertEquals('exp 11/10/2027', $anggota->formatted_expiry_date);
    }

    public function test_qr_code_svg_and_base64_generation(): void
    {
        $biro = User::create([
            'name'      => 'Biro QR Test',
            'username'  => 'biro_qr_' . uniqid(),
            'email'     => 'qr_' . uniqid() . '@example.com',
            'password'  => bcrypt('secret'),
            'kode_biro' => '999',
        ]);

        $anggota = new AnggotaBiro([
            'biro_id'         => $biro->id,
            'nama'            => 'Anggota QR',
            'tahun_masuk'     => 2026,
            'tanggal_lahir'   => '2000-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);
        $anggota->setRelation('biro', $biro);

        $svg = $anggota->qr_code_svg;
        $this->assertStringContainsString('<svg', $svg);

        $base64 = $anggota->qr_code_base64;
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $base64);
    }
}

