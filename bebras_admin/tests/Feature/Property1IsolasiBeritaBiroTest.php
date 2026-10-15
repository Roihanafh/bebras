<?php

namespace Tests\Feature;

// Feature: role-biro-berita, Property 1: Isolasi berita biro
// Untuk setiap kombinasi user biro dan dataset berita dari berbagai biro,
// query daftar berita hanya mengembalikan berita milik biro yang login.
// Validates: Requirements 3.2

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Property1IsolasiBeritaBiroTest extends TestCase
{
    use RefreshDatabase;

    /** @var Role */
    private Role $roleBiro;

    /** @var MenuKegiatan */
    private MenuKegiatan $menuKegiatan;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed role biro
        $this->roleBiro = Role::firstOrCreate(['name' => 'biro']);

        // Buat satu MenuKegiatan yang digunakan bersama sebagai FK
        $this->menuKegiatan = MenuKegiatan::create([
            'nama_menu' => 'Berita Umum',
            'slug'      => 'berita-umum',
            'judul'     => 'Berita Umum',
            'body'      => null,
            'urutan'    => 1,
        ]);
    }

    /**
     * Helper: buat user biro baru dengan nama unik.
     */
    private function buatUserBiro(string $suffix): User
    {
        $user = User::create([
            'name'     => "Biro {$suffix}",
            'username' => "biro_{$suffix}",
            'email'    => "biro_{$suffix}@test.com",
            'password' => Hash::make('password'),
        ]);
        $user->roles()->sync([$this->roleBiro->id]);

        return $user;
    }

    /**
     * Helper: buat sejumlah berita untuk user tertentu.
     *
     * @return Kegiatan[]
     */
    private function buatBeritaUntuk(User $user, int $jumlah): array
    {
        $hasil = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $hasil[] = Kegiatan::create([
                'menu_kegiatan_id' => $this->menuKegiatan->id,
                'tipe'             => 'berita',
                'judul'            => "Berita {$user->username} #{$i}",
                'deskripsi'        => 'Isi berita.',
                'status_validasi'  => 'pending',
                'dibuat_oleh'      => $user->id,
                'urutan'           => $i,
            ]);
        }

        return $hasil;
    }

    /**
     * Helper: query yang sama seperti di BeritaBiroController::list().
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Kegiatan>
     */
    private function queryBeritaBiro(int $biroId)
    {
        return Kegiatan::where('tipe', 'berita')
            ->where('dibuat_oleh', $biroId)
            ->get();
    }

    /**
     * Property 1 — Isolasi Berita Biro
     *
     * Untuk setiap iterasi:
     * - Buat 2–5 user biro secara acak
     * - Buat 1–5 berita untuk setiap biro
     * - Pilih satu biro sebagai "biro yang login"
     * - Assert: query daftar berita hanya mengembalikan berita
     *   yang dibuat oleh biro tersebut (dibuat_oleh === biroId)
     *
     * 20 iterasi minimum, setiap iterasi menggunakan dataset segar.
     *
     * Validates: Requirements 3.2
     */
    public function test_property1_isolasi_berita_biro(): void
    {
        $iterasi = 20;
        $totalAssertions = 0;

        for ($iter = 0; $iter < $iterasi; $iter++) {
            // --- Setup: buat data segar per iterasi ---

            // Jumlah biro acak antara 2 dan 5
            $jumlahBiro = rand(2, 5);
            $users = [];
            for ($b = 0; $b < $jumlahBiro; $b++) {
                $users[] = $this->buatUserBiro("{$iter}_{$b}");
            }

            // Buat berita acak (1–5) untuk setiap biro
            $beritaPerBiro = [];
            foreach ($users as $user) {
                $jumlahBerita = rand(1, 5);
                $beritaPerBiro[$user->id] = $this->buatBeritaUntuk($user, $jumlahBerita);
            }

            // Pilih satu biro secara acak sebagai "biro yang login"
            $biroLogin = $users[array_rand($users)];

            // --- Act: jalankan query ---
            $hasil = $this->queryBeritaBiro($biroLogin->id);

            // --- Assert ---

            // 1. Jumlah hasil harus sesuai dengan berita milik biro ini
            $expectedCount = count($beritaPerBiro[$biroLogin->id]);
            $this->assertCount(
                $expectedCount,
                $hasil,
                "Iterasi {$iter}: Jumlah berita yang dikembalikan ({$hasil->count()}) "
                . "tidak sesuai dengan yang dimiliki biro login ({$expectedCount})"
            );

            // 2. Setiap record harus dimiliki oleh biro yang login
            foreach ($hasil as $berita) {
                $this->assertSame(
                    $biroLogin->id,
                    $berita->dibuat_oleh,
                    "Iterasi {$iter}: Ditemukan berita dengan dibuat_oleh={$berita->dibuat_oleh} "
                    . "padahal biro login id={$biroLogin->id}"
                );

                $totalAssertions++;
            }

            // 3. Tidak ada berita dari biro lain yang masuk ke hasil
            $idBeritaMilikBiroLain = collect($beritaPerBiro)
                ->except($biroLogin->id)
                ->flatten()
                ->pluck('id')
                ->toArray();

            $idsHasil = $hasil->pluck('id')->toArray();

            foreach ($idBeritaMilikBiroLain as $idLain) {
                $this->assertNotContains(
                    $idLain,
                    $idsHasil,
                    "Iterasi {$iter}: Berita id={$idLain} milik biro lain ikut muncul di hasil biro login"
                );
                $totalAssertions++;
            }

            // Bersihkan data agar iterasi berikutnya segar
            Kegiatan::whereIn('dibuat_oleh', collect($users)->pluck('id')->toArray())->delete();
            foreach ($users as $user) {
                $user->roles()->detach();
                $user->delete();
            }
        }

        // Pastikan ada assertion yang dijalankan (minimal 20 iterasi × 1 berita = 20)
        $this->assertGreaterThanOrEqual(20, $totalAssertions, 'Jumlah assertion terlalu sedikit');
    }

    /**
     * Property 1 — Edge case: biro tanpa berita tidak melihat berita biro lain.
     *
     * Validates: Requirements 3.2
     */
    public function test_property1_biro_tanpa_berita_tidak_melihat_milik_biro_lain(): void
    {
        $biroTanpaBerita  = $this->buatUserBiro('kosong');
        $biroAdaBerita    = $this->buatUserBiro('punya');

        // Buat berita hanya untuk biroAdaBerita
        $this->buatBeritaUntuk($biroAdaBerita, 3);

        $hasil = $this->queryBeritaBiro($biroTanpaBerita->id);

        $this->assertCount(0, $hasil, 'Biro tanpa berita tidak boleh melihat berita biro lain');
    }

    /**
     * Property 1 — Berita non-'berita' (tipe lain) tidak ikut muncul,
     * meski dibuat_oleh adalah biro yang sama.
     *
     * Validates: Requirements 3.2 (filter tipe='berita')
     */
    public function test_property1_berita_tipe_lain_tidak_masuk_hasil(): void
    {
        $biro = $this->buatUserBiro('tipetest');

        // Buat 2 berita bertipe 'berita'
        $this->buatBeritaUntuk($biro, 2);

        // Buat 1 kegiatan bertipe 'workshop' dengan dibuat_oleh = biro
        Kegiatan::create([
            'menu_kegiatan_id' => $this->menuKegiatan->id,
            'tipe'             => 'workshop',
            'judul'            => 'Workshop Biro',
            'deskripsi'        => 'Deskripsi workshop.',
            'status_validasi'  => 'pending',
            'dibuat_oleh'      => $biro->id,
            'urutan'           => 99,
        ]);

        $hasil = $this->queryBeritaBiro($biro->id);

        // Hanya 2 berita bertipe 'berita' yang muncul
        $this->assertCount(2, $hasil);

        foreach ($hasil as $record) {
            $this->assertSame('berita', $record->tipe, 'Hanya record bertipe berita yang boleh dikembalikan');
        }
    }
}
