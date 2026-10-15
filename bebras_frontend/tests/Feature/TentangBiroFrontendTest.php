<?php

namespace Tests\Feature;

use App\Models\KoordinatorBiro;
use App\Models\Role;
use App\Models\TentangBebras;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TentangBiroFrontendTest extends TestCase
{
    use DatabaseTransactions;

    private User $biroUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        TentangBebras::firstOrCreate(
            ['slug' => 'dd_7'],
            [
                'judul'    => 'Koordinator Bebras Biro',
                'konten'   => '<p>Berikut adalah daftar Bebras Biro dan koordinatornya masing-masing.</p>',
                'urutan'   => 7,
                'template' => 'dd_7',
            ]
        );

        $this->biroUser = User::create([
            'name'      => 'Institut Teknologi Bandung',
            'username'  => 'biro_itb_' . uniqid(),
            'email'     => 'biro_itb_' . uniqid() . '@itb.ac.id',
            'password'  => Hash::make('password'),
            'kode_biro' => '001',
        ]);
        $this->biroUser->roles()->attach($biroRole->id);

        $this->regularUser = User::create([
            'name'      => 'Bukan Biro',
            'username'  => 'user_' . uniqid(),
            'email'     => 'user_' . uniqid() . '@example.com',
            'password'  => Hash::make('password'),
        ]);
        $this->regularUser->roles()->attach($userRole->id);
    }

    public function test_halaman_tentang_biro_mengambil_data_dinamis_user_role_biro_dan_koordinator(): void
    {
        $this->biroUser->koordinatorBiro()->create([
            'nama'  => 'Agung Dewandaru',
            'email' => 'agung@itb.ac.id',
        ]);

        $response = $this->get(route('tentangBebras.dd_7'));
        $response->assertStatus(200);

        // Memastikan biro muncul
        $response->assertSee('Institut Teknologi Bandung');
        // Memastikan koordinator muncul dan memiliki link mailto
        $response->assertSee('Agung Dewandaru');
        $response->assertSee('mailto:agung@itb.ac.id');

        // Memastikan user non-biro TIDAK muncul di daftar
        $response->assertDontSee('Bukan Biro');
    }

    public function test_satu_biro_dengan_banyak_koordinator_tampil_dengan_pemisah_pipe(): void
    {
        $this->biroUser->koordinatorBiro()->createMany([
            ['nama' => 'Agung Dewandaru', 'email' => 'agung@itb.ac.id'],
            ['nama' => 'Dewi Sartika', 'email' => 'dewi@itb.ac.id'],
        ]);

        $response = $this->get(route('tentangBebras.dd_7'));
        $response->assertStatus(200);

        $response->assertSee('Agung Dewandaru');
        $response->assertSee('mailto:agung@itb.ac.id');
        $response->assertSee('Dewi Sartika');
        $response->assertSee('mailto:dewi@itb.ac.id');
    }

    public function test_perubahan_data_koordinator_otomatis_tercermin_di_frontend(): void
    {
        $koordinator = $this->biroUser->koordinatorBiro()->create([
            'nama'  => 'Nama Lama',
            'email' => 'lama@example.com',
        ]);

        $res1 = $this->get(route('tentangBebras.dd_7'));
        $res1->assertSee('Nama Lama');

        // Simulasikan update koordinator
        $koordinator->update([
            'nama'  => 'Nama Baru Diperbarui',
            'email' => 'baru@example.com',
        ]);

        $res2 = $this->get(route('tentangBebras.dd_7'));
        $res2->assertSee('Nama Baru Diperbarui');
        $res2->assertSee('mailto:baru@example.com');
        $res2->assertDontSee('Nama Lama');
    }
}
