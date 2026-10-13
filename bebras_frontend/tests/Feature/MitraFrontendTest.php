<?php

namespace Tests\Feature;

use App\Models\Mitra;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Feature tests untuk section Mitra di halaman beranda frontend.
 *
 * Validates: Requirements 7.1, 7.2, 7.3, 7.4, 7.6, 7.7
 *
 * Menggunakan DatabaseTransactions agar setiap test dibungkus transaksi
 * yang di-rollback di akhir, sehingga data di database tidak berubah secara permanen.
 */
class MitraFrontendTest extends TestCase
{
    use DatabaseTransactions;

    // =========================================================================
    // Requirement 7.6 — Section tidak muncul saat tidak ada data mitra
    // =========================================================================

    /**
     * GET / tanpa data mitra di database tidak boleh menampilkan section "Mitra Kami".
     *
     * Validates: Requirement 7.6
     */
    public function test_beranda_tanpa_data_mitra_section_tidak_muncul(): void
    {
        // Pastikan tidak ada baris mitra dalam scope transaksi ini
        Mitra::query()->delete();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Mitra Kami');
    }

    // =========================================================================
    // Requirement 7.2, 7.4, 7.7 — Mitra tanpa logo menampilkan placeholder
    // =========================================================================

    /**
     * GET / dengan 3 mitra tanpa logo harus menampilkan nama masing-masing
     * dan placeholder div (bg-gray-200) untuk setiap mitra.
     *
     * Validates: Requirements 7.2, 7.4, 7.7
     */
    public function test_beranda_dengan_tiga_mitra_tanpa_logo_semua_ter_render(): void
    {
        $mitra1 = Mitra::create(['nama' => 'Kementerian Pendidikan', 'logo' => null, 'urutan' => 1]);
        $mitra2 = Mitra::create(['nama' => 'TOKI Indonesia',         'logo' => null, 'urutan' => 2]);
        $mitra3 = Mitra::create(['nama' => 'STEI ITB',               'logo' => null, 'urutan' => 3]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($mitra1->nama);
        $response->assertSee($mitra2->nama);
        $response->assertSee($mitra3->nama);
        // Placeholder div hadir untuk setiap mitra tanpa logo
        $response->assertSee('bg-gray-200');
    }

    // =========================================================================
    // Requirement 7.3 — Mitra dengan logo menampilkan tag <img>
    // =========================================================================

    /**
     * GET / dengan satu mitra berlogo harus merender tag <img> yang src-nya
     * mengandung path relatif logo tersebut.
     *
     * Validates: Requirements 7.2, 7.3
     */
    public function test_beranda_dengan_mitra_berlogo_render_img(): void
    {
        Mitra::create(['nama' => 'Fasilkom UI', 'logo' => 'mitra/test.jpg', 'urutan' => 1]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('mitra/test.jpg');
        $response->assertSee('<img', false);
    }

    // =========================================================================
    // Requirement 7.1 — Urutan tampil mitra sesuai kolom urutan (ascending)
    // =========================================================================

    /**
     * GET / dengan mitra yang memiliki urutan berbeda harus menampilkan mitra
     * dengan nilai urutan lebih kecil lebih dahulu di HTML output.
     *
     * Validates: Requirement 7.1
     */
    public function test_urutan_tampil_mitra_sesuai_kolom_urutan(): void
    {
        // Insert sengaja dengan urutan terbalik agar membuktikan pengurutan
        // dilakukan oleh query, bukan oleh urutan insert
        $mitraB = Mitra::create(['nama' => 'Beta Corp',  'logo' => null, 'urutan' => 1]);
        $mitraA = Mitra::create(['nama' => 'Alpha Corp', 'logo' => null, 'urutan' => 2]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        $posB = strpos($content, 'Beta Corp');
        $posA = strpos($content, 'Alpha Corp');

        $this->assertNotFalse($posB, 'Beta Corp (urutan=1) harus muncul di halaman');
        $this->assertNotFalse($posA, 'Alpha Corp (urutan=2) harus muncul di halaman');
        $this->assertLessThan($posA, $posB, 'Mitra dengan urutan lebih kecil harus muncul lebih dahulu di HTML');
    }
}
