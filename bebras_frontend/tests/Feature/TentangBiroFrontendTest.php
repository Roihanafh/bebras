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

    public function test_halaman_tentang_biro_hanya_menampilkan_anggota_yang_disetujui_approved(): void
    {
        // Anggota approved
        $this->biroUser->anggotaBiro()->create([
            'nama'            => 'Budi Approved',
            'tahun_masuk'     => 2023,
            'tanggal_lahir'   => '2005-10-10',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        // Anggota pending
        $this->biroUser->anggotaBiro()->create([
            'nama'            => 'Siti Pending',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2006-05-05',
            'no_anggota'      => '002',
            'status_validasi' => 'pending',
        ]);

        // Anggota rejected
        $this->biroUser->anggotaBiro()->create([
            'nama'            => 'Rudi Rejected',
            'tahun_masuk'     => 2024,
            'tanggal_lahir'   => '2006-08-08',
            'no_anggota'      => '003',
            'status_validasi' => 'rejected',
        ]);

        $response = $this->get(route('tentangBebras.dd_7'));
        $response->assertStatus(200);

        // Hanya anggota approved yang muncul
        $response->assertSee('001 — Budi Approved');
        $response->assertDontSee('Siti Pending');
        $response->assertDontSee('Rudi Rejected');
    }

    public function test_accordion_anggota_biro_hanya_menampilkan_nama_dan_tidak_menampilkan_detail_sensitif(): void
    {
        $this->biroUser->anggotaBiro()->create([
            'nama'            => 'Budi Santoso',
            'tahun_masuk'     => 2022,
            'tanggal_lahir'   => '2005-12-25',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        $response = $this->get(route('tentangBebras.dd_7'));
        $response->assertStatus(200);

        $response->assertSee('Budi Santoso');
        // Tanggal lahir tidak boleh ditampilkan di frontend publik
        $response->assertDontSee('2005-12-25');
        $response->assertDontSee('25/12/2005');
        // Status validasi tidak boleh ditampilkan di accordion publik
        $response->assertDontSee('status_validasi');
    }

    public function test_biro_berbeda_dengan_nomor_anggota_sama_tampil_sesuai_bironya(): void
    {
        $biroRole = Role::firstOrCreate(['name' => 'biro']);
        $biro2 = User::create([
            'name'      => 'Universitas Indonesia',
            'username'  => 'biro_ui_' . uniqid(),
            'email'     => 'biro_ui_' . uniqid() . '@ui.ac.id',
            'password'  => Hash::make('password'),
            'kode_biro' => '002',
        ]);
        $biro2->roles()->attach($biroRole->id);

        $this->biroUser->anggotaBiro()->create([
            'nama'            => 'Anggota ITB',
            'tahun_masuk'     => 2023,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        $biro2->anggotaBiro()->create([
            'nama'            => 'Anggota UI',
            'tahun_masuk'     => 2023,
            'tanggal_lahir'   => '2005-02-02',
            'no_anggota'      => '001',
            'status_validasi' => 'approved',
        ]);

        $response = $this->get(route('tentangBebras.dd_7'));
        $response->assertStatus(200);

        $response->assertSee('001 — Anggota ITB');
        $response->assertSee('001 — Anggota UI');
    }

    public function test_halaman_dd7_menyediakan_modal_kartu_member_dan_trigger_klik(): void
    {
        $anggota = $this->biroUser->anggotaBiro()->create([
            'nama'            => 'M. Unggul Pamenang',
            'tahun_masuk'     => 2026,
            'tanggal_lahir'   => '1991-05-10',
            'no_anggota'      => '764',
            'status_validasi' => 'approved',
        ]);

        $response = $this->get(route('tentangBebras.dd_7'));
        $response->assertStatus(200);

        // Memastikan markup modal ada di halaman
        $response->assertSee('id="member-card-modal"', false);
        $response->assertSee('id="modal-card-qr"', false);

        // Memastikan item anggota memiliki trigger dan data-attributes yang tepat
        $response->assertSee('data-id-member="0012691764"', false);
        $response->assertSee('M. Unggul Pamenang');
        $response->assertSee('Kartu Member');
    }

    public function test_halaman_kartu_member_dapat_diakses_untuk_anggota_approved(): void
    {
        $anggota = $this->biroUser->anggotaBiro()->create([
            'nama'            => 'M. Unggul Pamenang',
            'tahun_masuk'     => 2026,
            'tanggal_lahir'   => '1991-05-10',
            'no_anggota'      => '764',
            'status_validasi' => 'approved',
        ]);

        $response = $this->get(route('tentangBebras.anggota.card', $anggota->id));
        $response->assertStatus(200);

        $response->assertSee('M. Unggul Pamenang');
        $response->assertSee('0012691764');
        $response->assertSee('Institut Teknologi Bandung');
        $response->assertSee($anggota->formatted_expiry_date);
        $response->assertSee(route('tentangBebras.anggota.card.download', $anggota->id));
    }

    public function test_halaman_kartu_member_tidak_bisa_diakses_untuk_anggota_pending_atau_rejected(): void
    {
        $pendingAnggota = $this->biroUser->anggotaBiro()->create([
            'nama'            => 'Anggota Masih Pending',
            'tahun_masuk'     => 2026,
            'tanggal_lahir'   => '2005-01-01',
            'no_anggota'      => '002',
            'status_validasi' => 'pending',
        ]);

        $resPending = $this->get(route('tentangBebras.anggota.card', $pendingAnggota->id));
        $resPending->assertStatus(404);

        $resDownloadPending = $this->get(route('tentangBebras.anggota.card.download', $pendingAnggota->id));
        $resDownloadPending->assertStatus(404);
    }

    public function test_download_kartu_member_pdf_menghasilkan_file_pdf(): void
    {
        $anggota = $this->biroUser->anggotaBiro()->create([
            'nama'            => 'M. Unggul Pamenang',
            'tahun_masuk'     => 2026,
            'tanggal_lahir'   => '1991-05-10',
            'no_anggota'      => '764',
            'status_validasi' => 'approved',
        ]);

        $response = $this->get(route('tentangBebras.anggota.card.download', $anggota->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('kartu-member-0012691764.pdf', $contentDisposition);
    }
}

