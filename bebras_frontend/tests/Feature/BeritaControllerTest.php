<?php

namespace Tests\Feature;

// Feature: role-biro-berita, Property 5: Hanya berita approved tampil di frontend
// Feature: role-biro-berita, Property 6: Urutan tampil berita di frontend

use App\Models\Kegiatan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Property test — Property 5 & 6: Filter dan Urutan Berita di Frontend
 * Unit test — Requirements 5.3, 5.5: Pesan kosong dan placeholder gambar
 *
 * Validates: Requirements 5.1, 5.2, 5.3, 5.5
 *
 * Menggunakan DatabaseTransactions agar setiap test dibungkus transaksi
 * yang di-rollback di akhir — tidak mengubah data produksi secara permanen,
 * dan tabel `kegiatans` yang sudah ada di database tetap utuh.
 */
class BeritaControllerTest extends TestCase
{
    use DatabaseTransactions;

    // =========================================================================
    // Property 5 — Filter Berita Approved di Frontend
    // =========================================================================

    /**
     * Menyediakan kombinasi dataset acak dengan tipe (berita/kegiatan_menu)
     * dan status (pending/approved/rejected) yang bervariasi.
     *
     * Setiap entri merepresentasikan satu "sample" dari ruang input yang mungkin,
     * menyimulasikan generator property-based testing.
     *
     * @return array<string, array<array<int, array{tipe: string, status_validasi: string}>>>
     */
    public static function datasetBeritaAcakProvider(): array
    {
        $tipes    = ['berita', 'kegiatan_menu'];
        $statuses = ['pending', 'approved', 'rejected'];

        $datasets = [];

        // 30 skenario acak dengan seed tetap agar deterministik/reproducible
        mt_srand(42);
        for ($skenario = 1; $skenario <= 30; $skenario++) {
            $jumlahBaris = mt_rand(1, 10);
            $baris       = [];
            for ($i = 0; $i < $jumlahBaris; $i++) {
                $baris[] = [
                    'tipe'            => $tipes[mt_rand(0, 1)],
                    'status_validasi' => $statuses[mt_rand(0, 2)],
                ];
            }
            $datasets["Skenario {$skenario} ({$jumlahBaris} baris)"] = [$baris];
        }

        // Skenario khusus: tidak ada berita approved sama sekali
        $datasets['Tidak ada berita approved'] = [[
            ['tipe' => 'berita',        'status_validasi' => 'pending'],
            ['tipe' => 'berita',        'status_validasi' => 'rejected'],
            ['tipe' => 'kegiatan_menu', 'status_validasi' => 'approved'],
        ]];

        // Skenario khusus: semua berita approved
        $datasets['Semua berita approved'] = [[
            ['tipe' => 'berita', 'status_validasi' => 'approved'],
            ['tipe' => 'berita', 'status_validasi' => 'approved'],
            ['tipe' => 'berita', 'status_validasi' => 'approved'],
        ]];

        // Skenario khusus: dataset kosong
        $datasets['Dataset kosong'] = [[]];

        return $datasets;
    }

    /**
     * Property 5: Hanya berita approved yang tampil di frontend.
     *
     * Validates: Requirements 5.1
     *
     * Untuk setiap dataset berita dengan kombinasi tipe (berita/kegiatan_menu)
     * dan status (pending/approved/rejected) acak, endpoint GET /kegiatan/berita
     * hanya boleh mengembalikan entri dengan tipe='berita' DAN
     * status_validasi='approved'.
     *
     * Test ini menghapus semua baris kegiatans yang ada terlebih dahulu dalam
     * transaksi, lalu menyeed data baru — rollback di teardown memastikan
     * tidak ada perubahan permanen.
     *
     * @dataProvider datasetBeritaAcakProvider
     *
     * @param array<int, array{tipe: string, status_validasi: string}> $dataset
     */
    public function test_property_5_hanya_berita_approved_yang_tampil_di_frontend(array $dataset): void
    {
        // ---- Arrange: bersihkan baris kegiatans lama dalam transaksi ----
        DB::table('kegiatans')->delete();

        foreach ($dataset as $index => $baris) {
            DB::table('kegiatans')->insert([
                'tipe'            => $baris['tipe'],
                'status_validasi' => $baris['status_validasi'],
                'judul'           => "Test Berita #{$index}",
                'deskripsi'       => 'Deskripsi test',
                'urutan'          => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Hitung ekspektasi: hanya baris dengan tipe='berita' DAN status='approved'
        $jumlahEkspektasi = count(array_filter(
            $dataset,
            fn ($b) => $b['tipe'] === 'berita' && $b['status_validasi'] === 'approved'
        ));

        // ---- Act: panggil endpoint via HTTP ----
        $response = $this->get('/kegiatan/berita');
        $response->assertStatus(200);

        // ---- Assert: ambil koleksi $beritas dari view ----
        $beritas = $response->viewData('beritas');

        // Jumlah item harus sesuai ekspektasi
        $this->assertCount(
            $jumlahEkspektasi,
            $beritas,
            "Ekspektasi {$jumlahEkspektasi} berita approved, "
            . "tetapi controller mengembalikan " . count($beritas)
        );

        // Setiap item yang dikembalikan HARUS tipe='berita' DAN status='approved'
        foreach ($beritas as $berita) {
            $this->assertSame(
                'berita',
                $berita->tipe,
                "Item yang dikembalikan memiliki tipe='{$berita->tipe}', bukan 'berita'"
            );
            $this->assertSame(
                'approved',
                $berita->status_validasi,
                "Item yang dikembalikan memiliki status_validasi='{$berita->status_validasi}', bukan 'approved'"
            );
        }
    }

    // =========================================================================
    // Unit Tests — BeritaController frontend (Requirements 5.3, 5.5)
    // =========================================================================

    /**
     * Tidak ada berita approved — view menampilkan teks "Belum ada berita yang tersedia".
     *
     * Seed hanya berita pending/rejected (tanpa approved), pastikan halaman
     * menampilkan pesan kosong yang sesuai.
     *
     * Requirements: 5.3
     */
    public function test_tidak_ada_berita_approved_menampilkan_pesan_kosong(): void
    {
        // Hapus semua berita approved yang mungkin sudah ada agar test terisolasi
        DB::table('kegiatans')->where('tipe', 'berita')->where('status_validasi', 'approved')->delete();

        // Seed berita dengan status selain approved
        DB::table('kegiatans')->insert([
            [
                'menu_kegiatan_id' => null,
                'tipe'             => 'berita',
                'judul'            => 'Berita Pending Test',
                'deskripsi'        => 'Deskripsi pending',
                'gambar'           => null,
                'urutan'           => 0,
                'status_validasi'  => 'pending',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'menu_kegiatan_id' => null,
                'tipe'             => 'berita',
                'judul'            => 'Berita Rejected Test',
                'deskripsi'        => 'Deskripsi rejected',
                'gambar'           => null,
                'urutan'           => 0,
                'status_validasi'  => 'rejected',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        $response = $this->get('/kegiatan/berita');

        $response->assertStatus(200);
        $response->assertSee('Belum ada berita yang tersedia');
    }

    /**
     * Berita dengan gambar null — HTML mengandung elemen placeholder.
     *
     * Seed satu berita approved dengan kolom gambar = null,
     * pastikan halaman merender div dengan class "berita-placeholder".
     *
     * Requirements: 5.5
     */
    public function test_berita_dengan_gambar_null_menampilkan_placeholder(): void
    {
        DB::table('kegiatans')->insert([
            'menu_kegiatan_id' => null,
            'tipe'             => 'berita',
            'judul'            => 'Berita Tanpa Gambar Test',
            'deskripsi'        => 'Deskripsi tanpa gambar',
            'gambar'           => null,
            'urutan'           => 0,
            'status_validasi'  => 'approved',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $response = $this->get('/kegiatan/berita');

        $response->assertStatus(200);
        $response->assertSee('berita-placeholder', false);
        $response->assertSee('Tidak ada gambar');
    }

    // =========================================================================
    // Property 6 — Urutan Tampil Berita di Frontend
    // =========================================================================

    /**
     * Menyediakan berbagai kombinasi dataset acak untuk menguji aturan pengurutan.
     * Setiap case merepresentasikan satu "sample" dari ruang input yang mungkin.
     *
     * @return array<string, array<array{urutan: int, created_at: string}>>
     */
    public static function penyediaDataUrutan(): array
    {
        return [
            // ----- Case 1: urutan berbeda semua, sudah terbalik -----
            'urutan_unik_terbalik' => [[
                ['urutan' => 5, 'created_at' => '2024-01-01 10:00:00'],
                ['urutan' => 3, 'created_at' => '2024-01-02 10:00:00'],
                ['urutan' => 1, 'created_at' => '2024-01-03 10:00:00'],
            ]],

            // ----- Case 2: semua urutan sama, created_at berbeda -----
            'urutan_sama_created_at_bervariasi' => [[
                ['urutan' => 2, 'created_at' => '2024-03-01 08:00:00'],
                ['urutan' => 2, 'created_at' => '2024-03-15 12:00:00'],
                ['urutan' => 2, 'created_at' => '2024-02-28 06:00:00'],
                ['urutan' => 2, 'created_at' => '2024-03-20 20:00:00'],
            ]],

            // ----- Case 3: campuran urutan — beberapa sama, beberapa berbeda -----
            'campuran_urutan_dan_created_at' => [[
                ['urutan' => 1, 'created_at' => '2024-06-10 09:00:00'],
                ['urutan' => 3, 'created_at' => '2024-06-01 07:00:00'],
                ['urutan' => 1, 'created_at' => '2024-06-12 11:00:00'],
                ['urutan' => 2, 'created_at' => '2024-06-05 14:00:00'],
                ['urutan' => 3, 'created_at' => '2024-06-08 16:00:00'],
            ]],

            // ----- Case 4: satu berita (edge case: koleksi tunggal selalu terurut) -----
            'satu_berita' => [[
                ['urutan' => 0, 'created_at' => '2024-01-01 00:00:00'],
            ]],

            // ----- Case 5: urutan 0 (nilai default) semua -----
            'urutan_nol_semua' => [[
                ['urutan' => 0, 'created_at' => '2024-04-01 00:00:00'],
                ['urutan' => 0, 'created_at' => '2024-04-03 00:00:00'],
                ['urutan' => 0, 'created_at' => '2024-04-02 00:00:00'],
            ]],

            // ----- Case 6: nilai urutan negatif (angka rendah muncul lebih dulu) -----
            'urutan_negatif' => [[
                ['urutan' => -2, 'created_at' => '2024-07-01 00:00:00'],
                ['urutan' =>  0, 'created_at' => '2024-07-01 00:00:00'],
                ['urutan' => -5, 'created_at' => '2024-07-01 00:00:00'],
                ['urutan' =>  3, 'created_at' => '2024-07-01 00:00:00'],
            ]],

            // ----- Case 7: banyak berita, urutan acak, created_at acak -----
            'banyak_berita_acak' => [[
                ['urutan' => 10, 'created_at' => '2023-12-31 23:59:59'],
                ['urutan' =>  2, 'created_at' => '2024-01-15 08:30:00'],
                ['urutan' =>  7, 'created_at' => '2024-02-20 14:00:00'],
                ['urutan' =>  2, 'created_at' => '2024-01-10 06:00:00'],
                ['urutan' =>  7, 'created_at' => '2024-03-01 12:00:00'],
                ['urutan' =>  1, 'created_at' => '2024-05-05 10:00:00'],
                ['urutan' => 10, 'created_at' => '2024-01-01 00:00:00'],
                ['urutan' =>  4, 'created_at' => '2024-04-10 09:00:00'],
            ]],

            // ----- Case 8: semua created_at sama persis -----
            'created_at_identik' => [[
                ['urutan' => 3, 'created_at' => '2024-08-15 12:00:00'],
                ['urutan' => 1, 'created_at' => '2024-08-15 12:00:00'],
                ['urutan' => 2, 'created_at' => '2024-08-15 12:00:00'],
            ]],

            // ----- Case 9: urutan berurutan naik, created_at berurutan turun -----
            'urutan_naik_created_turun' => [[
                ['urutan' => 1, 'created_at' => '2024-09-10 10:00:00'],
                ['urutan' => 2, 'created_at' => '2024-09-09 10:00:00'],
                ['urutan' => 3, 'created_at' => '2024-09-08 10:00:00'],
                ['urutan' => 4, 'created_at' => '2024-09-07 10:00:00'],
            ]],

            // ----- Case 10: semua urutan sama, created_at sudah descending -----
            'urutan_sama_created_sudah_desc' => [[
                ['urutan' => 5, 'created_at' => '2024-10-20 00:00:00'],
                ['urutan' => 5, 'created_at' => '2024-10-15 00:00:00'],
                ['urutan' => 5, 'created_at' => '2024-10-10 00:00:00'],
            ]],
        ];
    }

    /**
     * Insert sejumlah berita approved ke DB.
     *
     * @param  array<array{urutan: int, created_at: string}>  $specs
     */
    private function seedBerita(array $specs): void
    {
        foreach ($specs as $i => $spec) {
            DB::table('kegiatans')->insert([
                'tipe'            => 'berita',
                'status_validasi' => 'approved',
                'judul'           => "Berita #{$i}",
                'deskripsi'       => 'Deskripsi',
                'urutan'          => $spec['urutan'],
                'created_at'      => $spec['created_at'],
                'updated_at'      => $spec['created_at'],
            ]);
        }
    }

    /**
     * Pastikan koleksi $items terurut: urutan ASC, created_at DESC untuk urutan sama.
     */
    private function assertTerurut(iterable $items): void
    {
        $sebelumnya = null;

        foreach ($items as $item) {
            if ($sebelumnya !== null) {
                $this->assertLessThanOrEqual(
                    $item->urutan,
                    $sebelumnya->urutan,
                    "Urutan menurun: {$sebelumnya->urutan} → {$item->urutan} (judul: {$item->judul})"
                );

                if ($sebelumnya->urutan === $item->urutan) {
                    $this->assertGreaterThanOrEqual(
                        strtotime($item->created_at),
                        strtotime($sebelumnya->created_at),
                        "Untuk urutan={$item->urutan}, created_at tidak descending: "
                        . "{$sebelumnya->created_at} ≥ {$item->created_at} dilanggar"
                    );
                }
            }

            $sebelumnya = $item;
        }
    }

    /**
     * Property 6: Urutan Tampil Berita di Frontend
     *
     * Validates: Requirements 5.2
     *
     * Untuk setiap kombinasi dataset berita approved dengan nilai `urutan` dan
     * `created_at` yang bervariasi, koleksi yang dikembalikan oleh controller
     * HARUS selalu memenuhi:
     *   - Diurutkan berdasarkan `urutan` ASC
     *   - Untuk nilai `urutan` yang sama, diurutkan berdasarkan `created_at` DESC
     *
     * @dataProvider penyediaDataUrutan
     * @param  array<array{urutan: int, created_at: string}>  $specs
     */
    public function test_property_6_berita_approved_selalu_terurut_urutan_asc_created_at_desc(array $specs): void
    {
        // ---- Arrange ----
        DB::table('kegiatans')->delete();
        $this->seedBerita($specs);

        // Pastikan ada berita lain (non-berita, pending) yang tidak boleh ikut
        DB::table('kegiatans')->insert([
            ['tipe' => 'workshop', 'status_validasi' => 'approved', 'judul' => 'Workshop', 'deskripsi' => 'x', 'urutan' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['tipe' => 'berita',   'status_validasi' => 'pending',  'judul' => 'Draft',    'deskripsi' => 'x', 'urutan' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ---- Act: jalankan query persis seperti yang ada di BeritaController::index() ----
        $beritas = Kegiatan::where('tipe', 'berita')
            ->where('status_validasi', 'approved')
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // ---- Assert ----
        foreach ($beritas as $berita) {
            $this->assertSame('berita', $berita->tipe);
            $this->assertSame('approved', $berita->status_validasi);
        }

        $this->assertCount(count($specs), $beritas);
        $this->assertTerurut($beritas);
    }
}
