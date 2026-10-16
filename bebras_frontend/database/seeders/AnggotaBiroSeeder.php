<?php

namespace Database\Seeders;

use App\Models\AnggotaBiro;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnggotaBiroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan biro sudah ada
        $biroCount = User::whereHas('roles', fn($q) => $q->where('name', 'biro'))->count();
        if ($biroCount === 0) {
            $this->call(BiroSeeder::class);
        }

        // Data anggota untuk beberapa biro utama (testing tampilan accordion frontend & validasi admin)
        $sampleData = [
            '001' => [ // Institut Teknologi Bandung
                [
                    'no_anggota'      => '001',
                    'nama'            => 'Ahmad Fauzi',
                    'tahun_masuk'     => 2021,
                    'tanggal_lahir'   => '2003-04-12',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '002',
                    'nama'            => 'Siti Nurhaliza',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-07-21',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '003',
                    'nama'            => 'Rizky Ramadhan',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-11-03',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '004',
                    'nama'            => 'Fajar Pratama',
                    'tahun_masuk'     => 2023,
                    'tanggal_lahir'   => '2005-02-18',
                    'status_validasi' => 'pending',
                ],
                [
                    'no_anggota'      => '005',
                    'nama'            => 'Dewi Anggraini',
                    'tahun_masuk'     => 2023,
                    'tanggal_lahir'   => '2005-09-09',
                    'status_validasi' => 'rejected',
                ],
            ],
            '002' => [ // Institut Pertanian Bogor
                [
                    'no_anggota'      => '001',
                    'nama'            => 'Bagus Hendrawan',
                    'tahun_masuk'     => 2021,
                    'tanggal_lahir'   => '2003-01-15',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '002',
                    'nama'            => 'Annisa Rahmawati',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-03-30',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '003',
                    'nama'            => 'Dimas Setiawan',
                    'tahun_masuk'     => 2023,
                    'tanggal_lahir'   => '2005-06-14',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '004',
                    'nama'            => 'Gita Safitri',
                    'tahun_masuk'     => 2024,
                    'tanggal_lahir'   => '2006-08-20',
                    'status_validasi' => 'pending',
                ],
            ],
            '003' => [ // Universitas Indonesia
                [
                    'no_anggota'      => '001',
                    'nama'            => 'Kevin Sanjaya',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-05-19',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '002',
                    'nama'            => 'Maya Putri',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-10-10',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '003',
                    'nama'            => 'Hendra Gunawan',
                    'tahun_masuk'     => 2023,
                    'tanggal_lahir'   => '2005-12-01',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '004',
                    'nama'            => 'Nabila Azzahra',
                    'tahun_masuk'     => 2024,
                    'tanggal_lahir'   => '2006-02-28',
                    'status_validasi' => 'pending',
                ],
            ],
            '004' => [ // Institut Teknologi Sepuluh Nopember
                [
                    'no_anggota'      => '001',
                    'nama'            => 'Bima Perkasa',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-08-11',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '002',
                    'nama'            => 'Dian Permata',
                    'tahun_masuk'     => 2023,
                    'tanggal_lahir'   => '2005-04-17',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '003',
                    'nama'            => 'Ilham Hidayat',
                    'tahun_masuk'     => 2024,
                    'tanggal_lahir'   => '2006-11-05',
                    'status_validasi' => 'pending',
                ],
            ],
            '005' => [ // Universitas Gadjah Mada
                [
                    'no_anggota'      => '001',
                    'nama'            => 'Farhan Maulana',
                    'tahun_masuk'     => 2021,
                    'tanggal_lahir'   => '2003-06-22',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '002',
                    'nama'            => 'Larasati Wulandari',
                    'tahun_masuk'     => 2022,
                    'tanggal_lahir'   => '2004-09-14',
                    'status_validasi' => 'approved',
                ],
                [
                    'no_anggota'      => '003',
                    'nama'            => 'Ryan Kurniawan',
                    'tahun_masuk'     => 2023,
                    'tanggal_lahir'   => '2005-01-30',
                    'status_validasi' => 'approved',
                ],
            ],
        ];

        foreach ($sampleData as $kodeBiro => $members) {
            $biro = User::where('kode_biro', $kodeBiro)->first();

            if (!$biro) {
                $biros = User::whereHas('roles', fn($q) => $q->where('name', 'biro'))->orderBy('id')->get();
                $idx = ((int)$kodeBiro) - 1;
                $biro = $biros[$idx] ?? null;
            }

            if ($biro) {
                foreach ($members as $m) {
                    AnggotaBiro::updateOrCreate(
                        [
                            'biro_id'    => $biro->id,
                            'no_anggota' => $m['no_anggota'],
                        ],
                        [
                            'nama'            => $m['nama'],
                            'tahun_masuk'     => $m['tahun_masuk'],
                            'tanggal_lahir'   => $m['tanggal_lahir'],
                            'status_validasi' => $m['status_validasi'],
                        ]
                    );
                }
            }
        }
    }
}
