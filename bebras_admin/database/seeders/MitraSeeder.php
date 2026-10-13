<?php

namespace Database\Seeders;

use App\Models\Mitra;
use Illuminate\Database\Seeder;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mitras = [
            ['nama' => 'Bakti Pendidikan Djarum Foundation', 'urutan' => 1],
            ['nama' => 'Pusat Prestasi Nasional',            'urutan' => 2],
            ['nama' => 'Kemendikdasmen',                     'urutan' => 3],
            ['nama' => 'Pusat Perbukuan',                    'urutan' => 4],
            ['nama' => 'Kementerian Pendidikan',             'urutan' => 5],
            ['nama' => 'Pusat Asesmen Pendidikan',           'urutan' => 6],
            ['nama' => 'Pusat Kurikulum',                    'urutan' => 7],
            ['nama' => 'Google.org',                         'urutan' => 8],
            ['nama' => 'ASEAN Foundation',                   'urutan' => 9],
        ];

        foreach ($mitras as $data) {
            Mitra::firstOrCreate(
                ['nama' => $data['nama']],
                ['logo' => null, 'urutan' => $data['urutan']]
            );
        }
    }
}