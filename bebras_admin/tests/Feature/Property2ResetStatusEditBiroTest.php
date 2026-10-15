<?php

namespace Tests\Feature;

// Feature: role-biro-berita, Property 2: Reset status ke pending saat edit oleh biro

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Property 2: Reset Status saat Edit oleh Biro
 *
 * Validates: Requirements 3.3
 *
 * For any berita dengan nilai status_validasi bukan 'pending' (yaitu 'approved' atau 'rejected'),
 * setelah biro melakukan update konten apapun, nilai status_validasi harus berubah menjadi 'pending'.
 */
class Property2ResetStatusEditBiroTest extends TestCase
{
    use RefreshDatabase;

    private Role $roleBiro;
    private MenuKegiatan $menuKegiatan;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat role biro
        $this->roleBiro = Role::firstOrCreate(['name' => 'biro']);

        // Buat MenuKegiatan yang dibutuhkan oleh FK menu_kegiatan_id
        $this->menuKegiatan = MenuKegiatan::create([
            'parent_id' => null,
            'nama_menu' => 'Menu Berita Test',
            'slug'      => 'menu-berita-test',
            'judul'     => 'Menu Berita',
            'body'      => null,
            'gambar'    => null,
            'urutan'    => 1,
        ]);
    }

    /**
     * Buat user biro baru dengan index unik agar tidak tabrakan di test loop.
     */
    private function buatUserBiro(int $index): User
    {
        $user = User::create([
            'name'     => "Biro User {$index}",
            'username' => "biro_test_{$index}",
            'email'    => "biro_test_{$index}@example.com",
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($this->roleBiro->id);

        return $user;
    }

    /**
     * Buat berita milik $user dengan status $statusAwal.
     */
    private function buatBerita(User $user, string $statusAwal): Kegiatan
    {
        return Kegiatan::create([
            'menu_kegiatan_id' => $this->menuKegiatan->id,
            'tipe'             => 'berita',
            'judul'            => 'Judul Berita Awal',
            'deskripsi'        => 'Deskripsi berita awal.',
            'status_validasi'  => $statusAwal,
            'dibuat_oleh'      => $user->id,
            'urutan'           => 0,
        ]);
    }

    /**
     * Daftar konten acak yang bisa digunakan sebagai payload update.
     * Setiap elemen adalah pasangan [judul, deskripsi] yang berbeda-beda
     * untuk mensimulasikan "konten baru acak" dari biro.
     */
    private function kontenAcak(int $seed): array
    {
        $daftarKonten = [
            ['judul' => 'Berita Terbaru A', 'deskripsi' => 'Deskripsi baru untuk berita A.'],
            ['judul' => 'Update Kegiatan B', 'deskripsi' => 'Konten diperbarui untuk kegiatan B.'],
            ['judul' => 'Informasi Penting C', 'deskripsi' => 'Informasi terbaru yang perlu disampaikan C.'],
            ['judul' => 'Pengumuman Resmi D', 'deskripsi' => 'Pengumuman resmi dari biro D.'],
            ['judul' => 'Laporan Kegiatan E', 'deskripsi' => 'Laporan lengkap kegiatan berlangsung E.'],
            ['judul' => 'Revisi Berita F', 'deskripsi' => 'Isi berita direvisi setelah review F.'],
            ['judul' => 'Koreksi Data G', 'deskripsi' => 'Data pada berita ini telah dikoreksi G.'],
            ['judul' => 'Tambahan Info H', 'deskripsi' => 'Informasi tambahan untuk melengkapi H.'],
            ['judul' => 'Pembaruan Konten I', 'deskripsi' => 'Konten berita diperbarui secara lengkap I.'],
            ['judul' => 'Berita Edisi Baru J', 'deskripsi' => 'Edisi terbaru dari berita ini J.'],
        ];

        return $daftarKonten[$seed % count($daftarKonten)];
    }

    /**
     * Property 2 — Loop test: berita dengan status 'approved' harus reset ke 'pending' setelah biro edit.
     *
     * Validates: Requirements 3.3
     */
    public function test_berita_approved_direset_ke_pending_setelah_biro_edit(): void
    {
        // Feature: role-biro-berita, Property 2: Reset status ke pending saat edit oleh biro
        $iterasi = 20;

        for ($i = 0; $i < $iterasi; $i++) {
            $biro   = $this->buatUserBiro($i * 100);
            $berita = $this->buatBerita($biro, 'approved');

            $this->assertSame(
                'approved',
                $berita->status_validasi,
                "Iterasi {$i}: status awal seharusnya 'approved'"
            );

            $konten = $this->kontenAcak($i);

            $response = $this->actingAs($biro)
                ->put(route('berita.update', $berita->id), [
                    '_method'          => 'PUT',
                    'menu_kegiatan_id' => $this->menuKegiatan->id,
                    'judul'            => $konten['judul'],
                    'deskripsi'        => $konten['deskripsi'],
                    'urutan'           => $i,
                ]);

            $response->assertRedirect(route('berita.index'));

            $berita->refresh();

            $this->assertSame(
                'pending',
                $berita->status_validasi,
                "Iterasi {$i}: status setelah edit harus 'pending', bukan '{$berita->status_validasi}'"
            );
        }
    }

    /**
     * Property 2 — Loop test: berita dengan status 'rejected' harus reset ke 'pending' setelah biro edit.
     *
     * Validates: Requirements 3.3
     */
    public function test_berita_rejected_direset_ke_pending_setelah_biro_edit(): void
    {
        // Feature: role-biro-berita, Property 2: Reset status ke pending saat edit oleh biro
        $iterasi = 20;

        for ($i = 0; $i < $iterasi; $i++) {
            $biro   = $this->buatUserBiro($i * 200);
            $berita = $this->buatBerita($biro, 'rejected');

            $this->assertSame(
                'rejected',
                $berita->status_validasi,
                "Iterasi {$i}: status awal seharusnya 'rejected'"
            );

            $konten = $this->kontenAcak($i + 5);

            $response = $this->actingAs($biro)
                ->put(route('berita.update', $berita->id), [
                    '_method'          => 'PUT',
                    'menu_kegiatan_id' => $this->menuKegiatan->id,
                    'judul'            => $konten['judul'],
                    'deskripsi'        => $konten['deskripsi'],
                    'urutan'           => $i,
                ]);

            $response->assertRedirect(route('berita.index'));

            $berita->refresh();

            $this->assertSame(
                'pending',
                $berita->status_validasi,
                "Iterasi {$i}: status setelah edit harus 'pending', bukan '{$berita->status_validasi}'"
            );
        }
    }

    /**
     * Property 2 — Loop test: status acak dari ['approved', 'rejected'] selalu direset ke 'pending'.
     *
     * Generator utama: status dipilih secara acak dari ['approved', 'rejected'].
     * Ini adalah property test inti sesuai spesifikasi task 4.3.
     *
     * Validates: Requirements 3.3
     */
    public function test_status_approved_atau_rejected_selalu_direset_ke_pending_setelah_edit(): void
    {
        // Feature: role-biro-berita, Property 2: Reset status ke pending saat edit oleh biro
        $iterasi        = 25;
        $statusOptions  = ['approved', 'rejected'];

        for ($i = 0; $i < $iterasi; $i++) {
            // Generator: status acak dari ['approved', 'rejected']
            $statusAwal = $statusOptions[$i % 2 === 0 ? 0 : 1];
            // Tambah variasi berbasis seed untuk distribusi lebih realistis
            if ($i % 3 === 0) {
                $statusAwal = 'approved';
            } elseif ($i % 3 === 1) {
                $statusAwal = 'rejected';
            } else {
                $statusAwal = $statusOptions[array_rand($statusOptions)];
            }

            $biro   = $this->buatUserBiro($i * 300);
            $berita = $this->buatBerita($biro, $statusAwal);

            $konten = $this->kontenAcak($i * 7);

            $response = $this->actingAs($biro)
                ->put(route('berita.update', $berita->id), [
                    '_method'          => 'PUT',
                    'menu_kegiatan_id' => $this->menuKegiatan->id,
                    'judul'            => $konten['judul'],
                    'deskripsi'        => $konten['deskripsi'],
                    'kota'             => $i % 2 === 0 ? "Kota {$i}" : null,
                    'speaker'          => $i % 3 === 0 ? "Narasumber {$i}" : null,
                    'urutan'           => $i % 10,
                ]);

            $response->assertRedirect(route('berita.index'));

            $berita->refresh();

            $this->assertSame(
                'pending',
                $berita->status_validasi,
                "Iterasi {$i} (status awal: {$statusAwal}): status_validasi harus 'pending' setelah biro edit"
            );
        }
    }
}
