<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Integration tests untuk KegiatanController frontend — route /kegiatan/{slug}
 *
 * Validates: Requirements 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 10.1, 10.2
 *
 * Menggunakan DatabaseTransactions agar setiap test dibungkus transaksi
 * yang di-rollback di akhir, sehingga data di database tidak berubah secara permanen.
 */
class KegiatanFrontendShowTest extends TestCase
{
    use DatabaseTransactions;

    // -------------------------------------------------------------------------
    // Helper: buat baris menu_kegiatan via DB langsung
    // -------------------------------------------------------------------------

    /**
     * Insert satu baris ke tabel menu_kegiatan dan kembalikan ID-nya.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function buatMenu(array $overrides = []): int
    {
        $default = [
            'parent_id' => null,
            'nama_menu' => 'Menu Test',
            'slug'      => 'menu-test-' . uniqid(),
            'judul'     => 'Judul Menu Test',
            'body'      => null,
            'gambar'    => null,
            'url'       => null,
            'urutan'    => 0,
            'template'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $data = array_merge($default, $overrides);

        return DB::table('menu_kegiatan')->insertGetId($data);
    }

    // =========================================================================
    // Requirements 10.1 — Menu dengan children → 404
    // =========================================================================

    /**
     * GET /kegiatan/{slug} pada menu yang memiliki children harus mengembalikan 404.
     *
     * Validates: Requirement 10.1
     */
    public function test_menu_dengan_children_mengembalikan_404(): void
    {
        // Buat parent menu
        $slugParent = 'parent-menu-' . uniqid();
        $parentId   = $this->buatMenu([
            'slug'     => $slugParent,
            'template' => 'workshop',
        ]);

        // Buat child menu yang merujuk ke parent
        $this->buatMenu([
            'parent_id' => $parentId,
            'slug'      => 'child-menu-' . uniqid(),
        ]);

        $response = $this->get("/kegiatan/{$slugParent}");

        $response->assertStatus(404);
    }

    // =========================================================================
    // Requirements 10.2 — Slug tidak ada → 404
    // =========================================================================

    /**
     * GET /kegiatan/{slug} dengan slug yang tidak ada di DB harus mengembalikan 404.
     *
     * Validates: Requirement 10.2
     */
    public function test_slug_tidak_ada_mengembalikan_404(): void
    {
        $response = $this->get('/kegiatan/slug-yang-sama-sekali-tidak-ada-' . uniqid());

        $response->assertStatus(404);
    }

    // =========================================================================
    // Requirements 6.1 — Menu dengan URL eksternal → redirect
    // =========================================================================

    /**
     * GET /kegiatan/{slug} pada menu dengan url eksternal harus redirect ke URL tersebut.
     *
     * Validates: Requirement 6.1
     */
    public function test_menu_dengan_url_eksternal_melakukan_redirect(): void
    {
        $urlEksternal = 'https://bebras.id/external';
        $slug         = 'menu-eksternal-' . uniqid();

        $this->buatMenu([
            'slug' => $slug,
            'url'  => $urlEksternal,
        ]);

        $response = $this->get("/kegiatan/{$slug}");

        $response->assertRedirect($urlEksternal);
    }

    // =========================================================================
    // Requirements 6.3 — Template bebras_challenge → view pages.kegiatan.bebras_challenge
    // =========================================================================

    /**
     * GET /kegiatan/{slug} pada menu dengan template bebras_challenge
     * harus me-render view pages.kegiatan.bebras_challenge dengan status 200.
     *
     * Validates: Requirements 6.2, 6.3
     */
    public function test_template_bebras_challenge_merender_view_yang_benar(): void
    {
        $slug = 'bebras-challenge-' . uniqid();

        $this->buatMenu([
            'slug'     => $slug,
            'template' => 'bebras_challenge',
        ]);

        $response = $this->get("/kegiatan/{$slug}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.kegiatan.bebras_challenge');
    }

    // =========================================================================
    // Requirements 6.4 — Template workshop → view pages.kegiatan.workshop
    // =========================================================================

    /**
     * GET /kegiatan/{slug} pada menu dengan template workshop
     * harus me-render view pages.kegiatan.workshop dengan status 200.
     *
     * Validates: Requirements 6.2, 6.4
     */
    public function test_template_workshop_merender_view_yang_benar(): void
    {
        $slug = 'workshop-' . uniqid();

        $this->buatMenu([
            'slug'     => $slug,
            'template' => 'workshop',
        ]);

        $response = $this->get("/kegiatan/{$slug}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.kegiatan.workshop');
    }

    // =========================================================================
    // Requirements 6.5 — Template pengumuman_hasil → view pages.kegiatan.pengumuman_hasil
    // =========================================================================

    /**
     * GET /kegiatan/{slug} pada menu dengan template pengumuman_hasil
     * harus me-render view pages.kegiatan.pengumuman_hasil dengan status 200.
     *
     * Validates: Requirements 6.2, 6.5
     */
    public function test_template_pengumuman_hasil_merender_view_yang_benar(): void
    {
        $slug = 'pengumuman-hasil-' . uniqid();

        $this->buatMenu([
            'slug'     => $slug,
            'template' => 'pengumuman_hasil',
        ]);

        $response = $this->get("/kegiatan/{$slug}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.kegiatan.pengumuman_hasil');
    }

    /**
     * GET /kegiatan/{slug} pada menu dengan pengumuman_hasil_files dari database
     * harus me-render nama kategori dan link unduh file.
     */
    public function test_template_pengumuman_hasil_dengan_file_database_merender_tabs_dan_link_unduh(): void
    {
        $slug = 'pengumuman-db-' . uniqid();

        $menuId = $this->buatMenu([
            'slug'     => $slug,
            'template' => 'pengumuman_hasil',
        ]);

        \App\Models\PengumumanHasilFile::create([
            'menu_kegiatan_id' => $menuId,
            'kategori'         => 'sikecil',
            'file_path'        => 'pengumuman_hasil/test-sikecil.csv',
            'original_name'    => 'Hasil-Sikecil-2026.csv',
        ]);

        \App\Models\PengumumanHasilFile::create([
            'menu_kegiatan_id' => $menuId,
            'kategori'         => 'penegak',
            'file_path'        => 'pengumuman_hasil/test-penegak.xlsx',
            'original_name'    => 'Hasil-Penegak-2026.xlsx',
        ]);

        $response = $this->get("/kegiatan/{$slug}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.kegiatan.pengumuman_hasil');
        $response->assertSee('Si Kecil');
        $response->assertSee('Penegak');
        $response->assertSee('Hasil-Sikecil-2026.csv');
        $response->assertSee('Hasil-Penegak-2026.xlsx');
    }

    // =========================================================================
    // Requirements 6.6 — Template null → view pages.kegiatan.show (generik)
    // =========================================================================

    /**
     * GET /kegiatan/{slug} pada menu tanpa template (null)
     * harus me-render view pages.kegiatan.show dengan status 200.
     *
     * Validates: Requirements 6.2, 6.6
     */
    public function test_template_null_merender_view_generik_show(): void
    {
        $slug = 'menu-tanpa-template-' . uniqid();

        $this->buatMenu([
            'slug'     => $slug,
            'template' => null,
        ]);

        $response = $this->get("/kegiatan/{$slug}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.kegiatan.show');
    }
}
