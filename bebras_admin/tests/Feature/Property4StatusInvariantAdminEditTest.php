<?php

namespace Tests\Feature;

// Feature: role-biro-berita, Property 4: Status tidak berubah saat admin edit konten
// Untuk semua berita dengan status apapun ('pending', 'approved', 'rejected'),
// setelah Admin melakukan update field konten (judul, deskripsi, kota, tanggal_lokasi,
// speaker, urutan), nilai status_validasi harus tetap sama dengan nilai sebelum update.
// Validates: Requirements 4.7

use App\Models\Kegiatan;
use App\Models\MenuKegiatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Property4StatusInvariantAdminEditTest extends TestCase
{
    use RefreshDatabase;

    private Role $roleAdmin;
    private MenuKegiatan $menuKegiatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        $this->menuKegiatan = MenuKegiatan::create([
            'parent_id' => null,
            'nama_menu' => 'Menu Berita Test',
            'slug'      => 'menu-berita-test-p4',
            'judul'     => 'Menu Berita',
            'body'      => null,
            'gambar'    => null,
            'urutan'    => 1,
        ]);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Buat user admin dengan role admin. */
    private function buatAdmin(int $index): User
    {
        $user = User::create([
            'name'     => "Admin User {$index}",
            'username' => "admin_test_{$index}",
            'email'    => "admin_test_{$index}@example.com",
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($this->roleAdmin->id);

        return $user;
    }

    /** Buat user biro sebagai pemilik berita. */
    private function buatBiro(int $index): User
    {
        $roleBiro = Role::firstOrCreate(['name' => 'biro']);

        $user = User::create([
            'name'     => "Biro User {$index}",
            'username' => "biro_p4_{$index}",
            'email'    => "biro_p4_{$index}@example.com",
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach($roleBiro->id);

        return $user;
    }

    /** Buat berita dengan status tertentu. */
    private function buatBerita(User $pemilik, string $status): Kegiatan
    {
        return Kegiatan::create([
            'menu_kegiatan_id' => $this->menuKegiatan->id,
            'tipe'             => 'berita',
            'judul'            => "Berita Awal {$pemilik->id} {$status}",
            'deskripsi'        => 'Deskripsi berita awal yang akan diedit oleh admin.',
            'status_validasi'  => $status,
            'dibuat_oleh'      => $pemilik->id,
            'urutan'           => 0,
        ]);
    }

    /**
     * Konten baru yang bervariasi, digunakan sebagai payload update dari admin.
     * Setiap seed menghasilkan kombinasi field konten yang berbeda.
     */
    private function kontenBaru(int $seed): array
    {
        $daftarKonten = [
            [
                'judul'          => 'Judul Diperbarui A',
                'deskripsi'      => 'Deskripsi baru versi A.',
                'kota'           => 'Jakarta',
                'tanggal_lokasi' => '2025-12-01',
                'speaker'        => 'Narasumber A',
                'urutan'         => 1,
            ],
            [
                'judul'          => 'Judul Diperbarui B',
                'deskripsi'      => 'Deskripsi baru versi B yang lebih panjang.',
                'kota'           => 'Bandung',
                'tanggal_lokasi' => '2025-11-15',
                'speaker'        => null,
                'urutan'         => 2,
            ],
            [
                'judul'          => 'Pembaruan Konten C',
                'deskripsi'      => 'Konten direvisi oleh admin untuk versi C.',
                'kota'           => null,
                'tanggal_lokasi' => null,
                'speaker'        => 'Pembicara C',
                'urutan'         => 0,
            ],
            [
                'judul'          => 'Informasi Diperbarui D',
                'deskripsi'      => 'Admin memperbarui informasi berita D.',
                'kota'           => 'Surabaya',
                'tanggal_lokasi' => '2026-01-20',
                'speaker'        => 'Narasumber D',
                'urutan'         => 5,
            ],
            [
                'judul'          => 'Revisi Resmi E',
                'deskripsi'      => 'Berita direvisi secara resmi oleh admin E.',
                'kota'           => null,
                'tanggal_lokasi' => '2026-03-10',
                'speaker'        => null,
                'urutan'         => 3,
            ],
            [
                'judul'          => 'Koreksi Konten F',
                'deskripsi'      => 'Koreksi diperlukan pada bagian tertentu F.',
                'kota'           => 'Medan',
                'tanggal_lokasi' => null,
                'speaker'        => 'Pakar F',
                'urutan'         => 7,
            ],
            [
                'judul'          => 'Update Resmi G',
                'deskripsi'      => 'Update resmi dari tim admin G.',
                'kota'           => 'Yogyakarta',
                'tanggal_lokasi' => '2025-10-05',
                'speaker'        => null,
                'urutan'         => 4,
            ],
            [
                'judul'          => 'Pembaruan Akhir H',
                'deskripsi'      => 'Ini adalah pembaruan akhir yang dilakukan admin H.',
                'kota'           => null,
                'tanggal_lokasi' => null,
                'speaker'        => null,
                'urutan'         => 9,
            ],
        ];

        return $daftarKonten[$seed % count($daftarKonten)];
    }

    // -----------------------------------------------------------------------
    // Property 4 — status_validasi tidak berubah saat admin edit konten
    // -----------------------------------------------------------------------

    /**
     * Property 4: Invariant Status saat Admin Edit
     *
     * Untuk setiap berita dengan status acak dari ['pending', 'approved', 'rejected'],
     * setelah Admin melakukan update konten (tanpa menyentuh status_validasi),
     * nilai status_validasi harus tetap sama persis dengan nilai sebelum update.
     *
     * Dijalankan dalam 30 iterasi dengan kombinasi status dan konten yang bervariasi.
     *
     * Validates: Requirements 4.7
     */
    public function test_property4_status_validasi_tidak_berubah_saat_admin_edit_konten(): void
    {
        // Feature: role-biro-berita, Property 4: Status tidak berubah saat admin edit konten
        $iterasi       = 30;
        $statusOptions = ['pending', 'approved', 'rejected'];

        for ($i = 0; $i < $iterasi; $i++) {
            // Generator: status acak dari tiga nilai enum yang valid
            $statusAwal = $statusOptions[$i % count($statusOptions)];

            // Tambah variasi lebih realistis dengan rotasi berbeda
            if ($i % 7 === 0) {
                $statusAwal = 'pending';
            } elseif ($i % 7 === 1 || $i % 7 === 4) {
                $statusAwal = 'approved';
            } elseif ($i % 7 === 2 || $i % 7 === 5) {
                $statusAwal = 'rejected';
            } else {
                $statusAwal = $statusOptions[array_rand($statusOptions)];
            }

            $admin  = $this->buatAdmin($i);
            $biro   = $this->buatBiro($i);
            $berita = $this->buatBerita($biro, $statusAwal);

            // Verifikasi nilai status sebelum update
            $this->assertSame(
                $statusAwal,
                $berita->status_validasi,
                "Iterasi {$i}: status awal harus '{$statusAwal}'"
            );

            // Generator: konten baru yang bervariasi setiap iterasi
            $konten = $this->kontenBaru($i);

            // Admin melakukan PUT update konten — tidak ada field status_validasi di payload
            $payload = [
                'menu_kegiatan_id' => $this->menuKegiatan->id,
                'judul'            => $konten['judul'],
                'deskripsi'        => $konten['deskripsi'],
                'urutan'           => $konten['urutan'],
            ];

            // Tambah field opsional hanya jika ada nilainya
            if ($konten['kota'] !== null) {
                $payload['kota'] = $konten['kota'];
            }
            if ($konten['tanggal_lokasi'] !== null) {
                $payload['tanggal_lokasi'] = $konten['tanggal_lokasi'];
            }
            if ($konten['speaker'] !== null) {
                $payload['speaker'] = $konten['speaker'];
            }

            $response = $this->actingAs($admin)
                ->put(route('admin.berita.update', $berita->id), $payload);

            // Update harus berhasil dan redirect ke index
            $response->assertRedirect(route('admin.berita.index'));

            // Assert: status_validasi setelah update === sebelum update (invariant)
            $berita->refresh();

            $this->assertSame(
                $statusAwal,
                $berita->status_validasi,
                "Iterasi {$i} (status awal: '{$statusAwal}'): status_validasi harus tetap '{$statusAwal}' setelah admin edit konten, bukan '{$berita->status_validasi}'"
            );
        }
    }

    /**
     * Property 4 — berita pending: status tidak berubah setelah admin edit
     *
     * Validates: Requirements 4.7
     */
    public function test_property4_status_pending_tidak_berubah_setelah_admin_edit(): void
    {
        // Feature: role-biro-berita, Property 4: Status tidak berubah saat admin edit konten
        $iterasi = 10;

        for ($i = 0; $i < $iterasi; $i++) {
            $admin  = $this->buatAdmin(100 + $i);
            $biro   = $this->buatBiro(100 + $i);
            $berita = $this->buatBerita($biro, 'pending');

            $konten = $this->kontenBaru($i + 2);

            $this->actingAs($admin)
                ->put(route('admin.berita.update', $berita->id), [
                    'menu_kegiatan_id' => $this->menuKegiatan->id,
                    'judul'            => $konten['judul'],
                    'deskripsi'        => $konten['deskripsi'],
                    'urutan'           => $konten['urutan'],
                ])
                ->assertRedirect(route('admin.berita.index'));

            $berita->refresh();

            $this->assertSame(
                'pending',
                $berita->status_validasi,
                "Iterasi {$i}: berita pending harus tetap 'pending' setelah admin edit"
            );
        }
    }

    /**
     * Property 4 — berita approved: status tidak berubah setelah admin edit
     *
     * Validates: Requirements 4.7
     */
    public function test_property4_status_approved_tidak_berubah_setelah_admin_edit(): void
    {
        // Feature: role-biro-berita, Property 4: Status tidak berubah saat admin edit konten
        $iterasi = 10;

        for ($i = 0; $i < $iterasi; $i++) {
            $admin  = $this->buatAdmin(200 + $i);
            $biro   = $this->buatBiro(200 + $i);
            $berita = $this->buatBerita($biro, 'approved');

            $konten = $this->kontenBaru($i + 4);

            $this->actingAs($admin)
                ->put(route('admin.berita.update', $berita->id), [
                    'menu_kegiatan_id' => $this->menuKegiatan->id,
                    'judul'            => $konten['judul'],
                    'deskripsi'        => $konten['deskripsi'],
                    'kota'             => $konten['kota'],
                    'urutan'           => $konten['urutan'],
                ])
                ->assertRedirect(route('admin.berita.index'));

            $berita->refresh();

            $this->assertSame(
                'approved',
                $berita->status_validasi,
                "Iterasi {$i}: berita approved harus tetap 'approved' setelah admin edit"
            );
        }
    }

    /**
     * Property 4 — berita rejected: status tidak berubah setelah admin edit
     *
     * Validates: Requirements 4.7
     */
    public function test_property4_status_rejected_tidak_berubah_setelah_admin_edit(): void
    {
        // Feature: role-biro-berita, Property 4: Status tidak berubah saat admin edit konten
        $iterasi = 10;

        for ($i = 0; $i < $iterasi; $i++) {
            $admin  = $this->buatAdmin(300 + $i);
            $biro   = $this->buatBiro(300 + $i);
            $berita = $this->buatBerita($biro, 'rejected');

            $konten = $this->kontenBaru($i + 6);

            $this->actingAs($admin)
                ->put(route('admin.berita.update', $berita->id), [
                    'menu_kegiatan_id' => $this->menuKegiatan->id,
                    'judul'            => $konten['judul'],
                    'deskripsi'        => $konten['deskripsi'],
                    'speaker'          => $konten['speaker'],
                    'urutan'           => $konten['urutan'],
                ])
                ->assertRedirect(route('admin.berita.index'));

            $berita->refresh();

            $this->assertSame(
                'rejected',
                $berita->status_validasi,
                "Iterasi {$i}: berita rejected harus tetap 'rejected' setelah admin edit"
            );
        }
    }

    /**
     * Sanity check: approve mengubah status (memastikan test ini sensitif terhadap perubahan).
     *
     * Ini memverifikasi bahwa jika admin memang mengubah status lewat approve,
     * perubahan itu terdeteksi — membuktikan test property 4 di atas memang valid.
     *
     * Validates: Requirements 4.3 (sanity check untuk konteks property 4)
     */
    public function test_approve_mengubah_status_ke_approved(): void
    {
        $admin  = $this->buatAdmin(999);
        $biro   = $this->buatBiro(999);
        $berita = $this->buatBerita($biro, 'pending');

        $this->actingAs($admin)
            ->post(route('admin.berita.approve', $berita->id))
            ->assertRedirect(route('admin.berita.index'));

        $berita->refresh();

        $this->assertSame(
            'approved',
            $berita->status_validasi,
            'Approve harus mengubah status menjadi approved'
        );
    }
}
