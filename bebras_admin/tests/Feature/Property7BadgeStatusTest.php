<?php

namespace Tests\Feature;

// Feature: role-biro-berita, Property 7: Badge sesuai status
// Untuk setiap nilai status_validasi yang mungkin (pending, approved, rejected),
// respon DataTables dari BeritaBiroController::list() harus mengandung class badge
// yang sesuai: bg-warning (pending), bg-success (approved), bg-danger (rejected).
// Validates: Requirements 7.1

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Property7BadgeStatusTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function buatUserBiro(): User
    {
        $role = Role::firstOrCreate(['name' => 'biro']);

        $user = User::create([
            'name'     => 'Biro Test',
            'username' => 'biro_test_' . uniqid(),
            'email'    => 'biro_badge_' . uniqid() . '@test.com',
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    private function buatMenuKegiatan(): MenuKegiatan
    {
        return MenuKegiatan::create([
            'nama_menu' => 'Berita Badge Test',
            'slug'      => 'berita-badge-test-' . uniqid(),
            'judul'     => 'Judul Menu Badge Test',
            'urutan'    => 1,
        ]);
    }

    private function buatBerita(User $biro, string $status, MenuKegiatan $menu): Kegiatan
    {
        return Kegiatan::create([
            'menu_kegiatan_id' => $menu->id,
            'tipe'             => 'berita',
            'judul'            => "Berita Status {$status}",
            'deskripsi'        => 'Deskripsi berita uji badge.',
            'urutan'           => 0,
            'status_validasi'  => $status,
            'dibuat_oleh'      => $biro->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // Data provider: setiap pasangan (status → class badge yang diharapkan)
    // -----------------------------------------------------------------------

    /**
     * @return array<string, array{string, string, string}>
     *   [nama_kasus => [status_validasi, class_badge_wajib, class_badge_dilarang]]
     */
    public static function statusBadgeProvider(): array
    {
        return [
            'pending → bg-warning' => [
                'pending',
                'bg-warning',
                'bg-success',
            ],
            'approved → bg-success' => [
                'approved',
                'bg-success',
                'bg-warning',
            ],
            'rejected → bg-danger' => [
                'rejected',
                'bg-danger',
                'bg-success',
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // Property 7 — Badge Status Sesuai Nilai (DataTables AJAX response)
    // -----------------------------------------------------------------------

    /**
     * Property 7: Untuk setiap nilai status_validasi yang mungkin, respon
     * DataTables list() harus mengandung class badge CSS yang sesuai.
     *
     * Mapping:
     *   pending  → bg-warning (kuning)
     *   approved → bg-success (hijau)
     *   rejected → bg-danger  (merah)
     *
     * Validates: Requirements 7.1
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('statusBadgeProvider')]
    public function test_property7_badge_sesuai_status_validasi(
        string $status,
        string $classWajib,
        string $classTidakBolehMuncul
    ): void {
        $biro  = $this->buatUserBiro();
        $menu  = $this->buatMenuKegiatan();
        $this->buatBerita($biro, $status, $menu);

        $response = $this->actingAs($biro)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('berita.list'));

        $response->assertStatus(200);

        $json = $response->json();

        // DataTables mengembalikan key 'data'
        $this->assertArrayHasKey('data', $json, 'Respon DataTables harus punya key "data"');
        $this->assertNotEmpty($json['data'], 'Harus ada minimal satu baris di data');

        $baris = $json['data'][0];

        // Kolom status_validasi harus ada
        $this->assertArrayHasKey(
            'status_validasi',
            $baris,
            'Kolom status_validasi tidak ada di respon DataTables'
        );

        $htmlBadge = $baris['status_validasi'];

        // Harus mengandung class badge yang sesuai
        $this->assertStringContainsString(
            $classWajib,
            $htmlBadge,
            "Status '{$status}' harus merender badge dengan class '{$classWajib}'. HTML: {$htmlBadge}"
        );

        // Tidak boleh mengandung class badge yang salah
        $this->assertStringNotContainsString(
            $classTidakBolehMuncul,
            $htmlBadge,
            "Status '{$status}' tidak boleh merender class '{$classTidakBolehMuncul}'. HTML: {$htmlBadge}"
        );
    }

    /**
     * Property 7 — Variasi: berita dengan berbagai status dirender dalam
     * satu respon DataTables, setiap baris badge-nya harus sesuai dengan
     * status masing-masing.
     *
     * Ini mensimulasikan skenario "generator acak" karena satu request
     * memuat semua variasi status sekaligus.
     *
     * Validates: Requirements 7.1
     */
    public function test_property7_semua_status_badge_sesuai_dalam_satu_response(): void
    {
        $biro = $this->buatUserBiro();
        $menu = $this->buatMenuKegiatan();

        // Buat berita untuk ketiga status
        $this->buatBerita($biro, 'pending', $menu);
        $this->buatBerita($biro, 'approved', $menu);
        $this->buatBerita($biro, 'rejected', $menu);

        $response = $this->actingAs($biro)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('berita.list'));

        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertCount(3, $json['data'], 'Tiga baris berita harus muncul di respon');

        // Petakan judul ke HTML badge dari respon
        $badgePerJudul = [];
        foreach ($json['data'] as $baris) {
            $badgePerJudul[$baris['judul']] = $baris['status_validasi'];
        }

        // Assert setiap status punya badge yang tepat
        $ekspektasi = [
            'Berita Status pending'  => 'bg-warning',
            'Berita Status approved' => 'bg-success',
            'Berita Status rejected' => 'bg-danger',
        ];

        foreach ($ekspektasi as $judul => $classWajib) {
            $this->assertArrayHasKey($judul, $badgePerJudul, "Berita '{$judul}' tidak ditemukan di respon");
            $this->assertStringContainsString(
                $classWajib,
                $badgePerJudul[$judul],
                "Berita '{$judul}' harus memiliki badge class '{$classWajib}'. HTML: {$badgePerJudul[$judul]}"
            );
        }
    }

    /**
     * Property 7 — Badge pending harus mengandung teks "Pending".
     * Property 7 — Badge approved harus mengandung teks "Approved".
     * Property 7 — Badge rejected harus mengandung teks "Rejected".
     *
     * Validates: Requirements 7.1
     */
    public function test_property7_badge_mengandung_teks_label_yang_sesuai(): void
    {
        $biro = $this->buatUserBiro();
        $menu = $this->buatMenuKegiatan();

        $statusDanLabel = [
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        foreach ($statusDanLabel as $status => $labelDiharapkan) {
            $berita = $this->buatBerita($biro, $status, $menu);

            $response = $this->actingAs($biro)
                ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                ->getJson(route('berita.list'));

            $response->assertStatus(200);
            $json = $response->json();

            // Cari baris yang sesuai dari respon
            $barisTarget = collect($json['data'])->firstWhere('judul', "Berita Status {$status}");
            $this->assertNotNull($barisTarget, "Baris untuk status '{$status}' tidak ditemukan");

            $htmlBadge = $barisTarget['status_validasi'];
            $this->assertStringContainsString(
                $labelDiharapkan,
                $htmlBadge,
                "Badge untuk status '{$status}' harus mengandung teks '{$labelDiharapkan}'. HTML: {$htmlBadge}"
            );

            // Bersihkan berita untuk iterasi berikutnya
            $berita->delete();
        }
    }
}
