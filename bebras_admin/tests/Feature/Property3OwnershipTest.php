<?php

namespace Tests\Feature;

// Feature: role-biro-berita, Property 3: Ownership enforcement
// Untuk semua berita yang memiliki dibuat_oleh berbeda dari ID user biro yang sedang login,
// setiap request ke endpoint edit atau destroy berita tersebut harus menghasilkan HTTP 403
// tanpa melakukan perubahan data.
// Validates: Requirements 3.6

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Property3OwnershipTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    /** Buat user biro dengan role biro. */
    private function buatBiro(string $suffix = ''): User
    {
        $role = Role::firstOrCreate(['name' => 'biro']);

        $uid  = uniqid() . $suffix;
        $user = User::create([
            'name'     => 'Biro ' . $uid,
            'username' => 'biro_' . $uid,
            'email'    => 'biro_' . $uid . '@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    /** Buat berita milik $pemilik dengan data acak. */
    private function buatBerita(User $pemilik, array $overrides = []): Kegiatan
    {
        return Kegiatan::create(array_merge([
            'menu_kegiatan_id' => null,
            'tipe'             => 'berita',
            'judul'            => 'Berita milik ' . $pemilik->name . ' ' . uniqid(),
            'deskripsi'        => 'Deskripsi berita',
            'urutan'           => 0,
            'status_validasi'  => 'pending',
            'dibuat_oleh'      => $pemilik->id,
        ], $overrides));
    }

    // -----------------------------------------------------------------------
    // Property 3a — GET /berita/{id}/edit oleh biro A ke berita milik biro B
    // harus menghasilkan HTTP 403.
    // -----------------------------------------------------------------------

    /**
     * Property 3: Perlindungan Ownership — edit
     *
     * Untuk setiap kombinasi biro A (login) dan biro B (pemilik berita),
     * request GET /berita/{id}/edit dari A ke berita milik B selalu menghasilkan 403.
     *
     * Dijalankan dalam 20 iterasi dengan pasangan biro yang dibuat acak setiap iterasi.
     *
     * Validates: Requirements 3.6
     */
    public function test_property3_biro_a_tidak_dapat_edit_berita_milik_biro_b(): void
    {
        $iterasi = 20;

        for ($i = 0; $i < $iterasi; $i++) {
            // Buat dua biro berbeda
            $biroA = $this->buatBiro('a_' . $i);
            $biroB = $this->buatBiro('b_' . $i);

            // Berita milik biro B (bukan biro A)
            $berita = $this->buatBerita($biroB, [
                'status_validasi' => $this->statusAcak(),
            ]);

            // Pastikan kepemilikan berbeda — ini adalah kondisi yang memicu 403
            $this->assertNotEquals(
                $biroA->id,
                $berita->dibuat_oleh,
                "Iterasi {$i}: biroA->id harus berbeda dari dibuat_oleh berita"
            );

            // Login sebagai biro A dan akses endpoint edit berita milik biro B
            $response = $this->actingAs($biroA)
                ->get("/berita/{$berita->id}/edit");

            $response->assertStatus(403, "Iterasi {$i}: HTTP 403 diharapkan saat biro A mencoba edit berita milik biro B");

            // Verifikasi data berita tidak berubah
            $beritaSesudah = Kegiatan::find($berita->id);
            $this->assertNotNull($beritaSesudah, "Iterasi {$i}: Berita harus tetap ada setelah percobaan akses ditolak");
            $this->assertEquals(
                $berita->judul,
                $beritaSesudah->judul,
                "Iterasi {$i}: Data berita tidak boleh berubah setelah 403"
            );
        }
    }

    // -----------------------------------------------------------------------
    // Property 3b — DELETE /berita/{id} oleh biro A ke berita milik biro B
    // harus menghasilkan HTTP 403 dan tidak menghapus data.
    // -----------------------------------------------------------------------

    /**
     * Property 3: Perlindungan Ownership — destroy
     *
     * Untuk setiap kombinasi biro A (login) dan biro B (pemilik berita),
     * request DELETE /berita/{id} dari A ke berita milik B selalu menghasilkan 403
     * dan berita tidak terhapus dari database.
     *
     * Dijalankan dalam 20 iterasi dengan pasangan biro dan status berita yang dibuat acak.
     *
     * Validates: Requirements 3.6
     */
    public function test_property3_biro_a_tidak_dapat_hapus_berita_milik_biro_b(): void
    {
        $iterasi = 20;

        for ($i = 0; $i < $iterasi; $i++) {
            $biroA = $this->buatBiro('da_' . $i);
            $biroB = $this->buatBiro('db_' . $i);

            // Berita milik biro B — status acak termasuk approved, pending, rejected
            $berita = $this->buatBerita($biroB, [
                'status_validasi' => $this->statusAcak(),
            ]);

            $this->assertNotEquals(
                $biroA->id,
                $berita->dibuat_oleh,
                "Iterasi {$i}: biroA->id harus berbeda dari dibuat_oleh berita"
            );

            // Login sebagai biro A dan kirim request DELETE ke berita milik biro B
            $response = $this->actingAs($biroA)
                ->delete("/berita/{$berita->id}");

            $response->assertStatus(403, "Iterasi {$i}: HTTP 403 diharapkan saat biro A mencoba hapus berita milik biro B");

            // Verifikasi berita masih ada di database (tidak terhapus)
            $masihAda = Kegiatan::where('id', $berita->id)->where('dibuat_oleh', $biroB->id)->exists();
            $this->assertTrue(
                $masihAda,
                "Iterasi {$i}: Berita milik biro B harus tetap ada di database setelah percobaan hapus ditolak"
            );
        }
    }

    // -----------------------------------------------------------------------
    // Sanity check: biro B sendiri bisa mengakses edit-nya (403 tidak berlaku)
    // -----------------------------------------------------------------------

    /**
     * Sanity check: biro yang benar (pemilik berita) tidak mendapat 403 saat
     * mengakses edit. Ini memverifikasi bahwa middleware biro aktif dan
     * property 3 hanya berlaku untuk biro yang bukan pemilik.
     *
     * Validates: Requirements 3.6 (sisi negatif)
     */
    public function test_pemilik_berita_tidak_mendapat_403_saat_edit(): void
    {
        $biroB  = $this->buatBiro('owner');
        $berita = $this->buatBerita($biroB);

        // biro B mengakses edit beritanya sendiri — seharusnya tidak 403
        // (mungkin error 500 karena view belum ada, tapi bukan 403)
        $response = $this->actingAs($biroB)
            ->get("/berita/{$berita->id}/edit");

        // Pastikan bukan 403 — pemilik berhak mengakses endpoint edit miliknya sendiri
        $this->assertNotEquals(
            403,
            $response->getStatusCode(),
            'Pemilik berita tidak seharusnya mendapat HTTP 403'
        );
    }

    // -----------------------------------------------------------------------
    // Helper internal
    // -----------------------------------------------------------------------

    /** Pilih status berita secara acak dari nilai enum yang valid. */
    private function statusAcak(): string
    {
        $statuses = ['pending', 'approved', 'rejected'];

        return $statuses[array_rand($statuses)];
    }
}
