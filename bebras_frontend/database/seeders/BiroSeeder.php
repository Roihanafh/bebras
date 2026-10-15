<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BiroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $biroRole = Role::firstOrCreate(['name' => 'biro']);

        $biros = [
            [
                'kode_biro'   => '001',
                'name'        => 'Institut Teknologi Bandung',
                'username'    => 'biro_001',
                'email'       => 'dewandaru@staff.stei.itb.ac.id',
                'koordinator' => [
                    ['nama' => 'Agung Dewandaru', 'email' => 'dewandaru@staff.stei.itb.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '002',
                'name'        => 'Institut Pertanian Bogor',
                'username'    => 'biro_002',
                'email'       => 'deanaprianaramadhan@apps.ipb.ac.id',
                'koordinator' => [
                    ['nama' => 'Dean Apriana Ramadhan', 'email' => 'deanaprianaramadhan@apps.ipb.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '003',
                'name'        => 'Universitas Indonesia',
                'username'    => 'biro_003',
                'email'       => 'bebras-biro-ui@cs.ui.ac.id',
                'koordinator' => [
                    ['nama' => 'Suryana Setiawan', 'email' => 'bebras-biro-ui@cs.ui.ac.id'],
                    ['nama' => 'Yugo Kartono Isal', 'email' => 'yugo@cs.ui.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '004',
                'name'        => 'Institut Teknologi Sepuluh November',
                'username'    => 'biro_004',
                'email'       => 'rully130270@gmail.com',
                'koordinator' => [
                    ['nama' => 'Rully Soelaiman', 'email' => 'rully130270@gmail.com'],
                    ['nama' => 'Yudhi Purwananto', 'email' => 'purwananto@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '005',
                'name'        => 'Universitas Kristen Maranatha',
                'username'    => 'biro_005',
                'email'       => 'mewati.ayub@it.maranatha.edu',
                'koordinator' => [
                    ['nama' => 'Mewati Ayub', 'email' => 'mewati.ayub@it.maranatha.edu'],
                ],
            ],
            [
                'kode_biro'   => '006',
                'name'        => 'Universitas Atmajaya Yogyakarta',
                'username'    => 'biro_006',
                'email'       => 'irya@staff.uajy.ac.id',
                'koordinator' => [
                    ['nama' => 'Irya Wisnubhadra', 'email' => 'irya@staff.uajy.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '007',
                'name'        => 'Universitas Diponegoro',
                'username'    => 'biro_007',
                'email'       => 'ragil.saputra@gmail.com',
                'koordinator' => [
                    ['nama' => 'Ragil Saputra', 'email' => 'ragil.saputra@gmail.com'],
                    ['nama' => 'Edy Suharto', 'email' => 'edy.suharto@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '008',
                'name'        => 'Universitas Islam Indonesia',
                'username'    => 'biro_008',
                'email'       => 'hanson@uii.ac.id',
                'koordinator' => [
                    ['nama' => 'Hanson Prihantoro Putro', 'email' => 'hanson@uii.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '009',
                'name'        => 'Universitas Jendral Soedirman',
                'username'    => 'biro_009',
                'email'       => 'swahesti@unsoed.ac.id',
                'koordinator' => [
                    ['nama' => 'Swahesti PR', 'email' => 'swahesti@unsoed.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '010',
                'name'        => 'Universitas Jember',
                'username'    => 'biro_010',
                'email'       => 'windi.ilkom@unej.ac.id',
                'koordinator' => [
                    ['nama' => 'Windi Eka Yulia R', 'email' => 'windi.ilkom@unej.ac.id'],
                    ['nama' => 'Diksy M. Firmansyah', 'email' => 'diksy@unej.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '011',
                'name'        => 'Universitas Sriwijaya',
                'username'    => 'biro_011',
                'email'       => 'mfachrz@unsri.ac.id',
                'koordinator' => [
                    ['nama' => 'Muhammad Fachrurrozi (obets)', 'email' => 'mfachrz@unsri.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '012',
                'name'        => 'Institut Teknologi Del',
                'username'    => 'biro_012',
                'email'       => 'samuel.situmeang@del.ac.id',
                'koordinator' => [
                    ['nama' => 'Samuel Situmeang', 'email' => 'samuel.situmeang@del.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '013',
                'name'        => 'Institut Teknologi Garut',
                'username'    => 'biro_013',
                'email'       => 'dewi.tresnawati@sttgarut.ac.id',
                'koordinator' => [
                    ['nama' => 'Dewi Tresnawati', 'email' => 'dewi.tresnawati@sttgarut.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '014',
                'name'        => 'Universitas Mulawarman',
                'username'    => 'biro_014',
                'email'       => 'yuniutamiasih@fkip.unmul.ac.id',
                'koordinator' => [
                    ['nama' => 'Dr. Yuni Utami Asih, M. Pd.', 'email' => 'yuniutamiasih@fkip.unmul.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '015',
                'name'        => 'Unversitas Udayana',
                'username'    => 'biro_015',
                'email'       => 'muliantara@unud.ac.id',
                'koordinator' => [
                    ['nama' => 'Agus Muliantara', 'email' => 'muliantara@unud.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '016',
                'name'        => 'Universitas Negeri Malang',
                'username'    => 'biro_016',
                'email'       => 'syaiful.hamzah.fmipa@um.ac.id',
                'koordinator' => [
                    ['nama' => 'Syaiful Hamzah Nasution', 'email' => 'syaiful.hamzah.fmipa@um.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '017',
                'name'        => 'Universitas Dian Nuswantoro',
                'username'    => 'biro_017',
                'email'       => 'ayu.pertiwi@dsn.dinus.ac.id',
                'koordinator' => [
                    ['nama' => 'Ayu Pertiwi', 'email' => 'ayu.pertiwi@dsn.dinus.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '018',
                'name'        => 'Politeknik Caltex Riau',
                'username'    => 'biro_018',
                'email'       => 'ardie@pcr.ac.id',
                'koordinator' => [
                    ['nama' => 'Ardianto Wibowo', 'email' => 'ardie@pcr.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '019',
                'name'        => 'Universitas Katolik Parahyangan',
                'username'    => 'biro_019',
                'email'       => 'cecilia.nugraheni@gmail.com',
                'koordinator' => [
                    ['nama' => 'Cecilia Nugraheni', 'email' => 'cecilia.nugraheni@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '020',
                'name'        => 'Institut Teknologi Sumatera',
                'username'    => 'biro_020',
                'email'       => 'wayan.wisesa@if.itera.ac.id',
                'koordinator' => [
                    ['nama' => 'I Wayan Wiprayoga Wisesa', 'email' => 'wayan.wisesa@if.itera.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '021',
                'name'        => 'Universitas Lambung Mangkurat',
                'username'    => 'biro_021',
                'email'       => 'andreyanrb@gmail.com',
                'koordinator' => [
                    ['nama' => 'Andreyan Rizky Baskara', 'email' => 'andreyanrb@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '022',
                'name'        => 'Universitas Tanjungpura',
                'username'    => 'biro_022',
                'email'       => 'haried@informatika.untan.ac.id',
                'koordinator' => [
                    ['nama' => 'Haried Novriando', 'email' => 'haried@informatika.untan.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '023',
                'name'        => 'Universitas Sumatera Utara',
                'username'    => 'biro_023',
                'email'       => 'elvi.zamzami@gmail.com',
                'koordinator' => [
                    ['nama' => 'Elviawaty Muisa Zamzami', 'email' => 'elvi.zamzami@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '024',
                'name'        => 'Universitas Pembangunan Jaya',
                'username'    => 'biro_024',
                'email'       => 'safitri.jaya@upj.ac.id',
                'koordinator' => [
                    ['nama' => 'Safitri Jaya S.Kom. M.Kom', 'email' => 'safitri.jaya@upj.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '025',
                'name'        => 'Universitas Kristen Satya Wacana',
                'username'    => 'biro_025',
                'email'       => 'kristoko@gmail.com',
                'koordinator' => [
                    ['nama' => 'Kristoko', 'email' => 'kristoko@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '026',
                'name'        => 'Universitas Maritim Raja Ali Haji',
                'username'    => 'biro_026',
                'email'       => 'nurul.hayaty@umrah.ac.id',
                'koordinator' => [
                    ['nama' => 'Nurul Hayati', 'email' => 'nurul.hayaty@umrah.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '027',
                'name'        => 'Universitas Paramadina',
                'username'    => 'biro_027',
                'email'       => 'rahmad.syalevi@paramadina.ac.id',
                'koordinator' => [
                    ['nama' => 'Rahmad Syalevi', 'email' => 'rahmad.syalevi@paramadina.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '028',
                'name'        => 'Universitas Katolik Widya Mandira',
                'username'    => 'biro_028',
                'email'       => 'emilianameol@gmail.com',
                'koordinator' => [
                    ['nama' => 'Emiliana Meolbatak', 'email' => 'emilianameol@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '029',
                'name'        => 'Politeknik Negeri Batam',
                'username'    => 'biro_029',
                'email'       => 'hamim@polibatam.ac.id',
                'koordinator' => [
                    ['nama' => 'Ahmad Hamim Thohari', 'email' => 'hamim@polibatam.ac.id'],
                    ['nama' => 'Mir’atul Khusna Mufida', 'email' => 'vda@polibatam.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '030',
                'name'        => 'Politeknik Negeri Malang',
                'username'    => 'biro_030',
                'email'       => 'agung.pramudhita@polinema.ac.id',
                'koordinator' => [
                    ['nama' => 'Agung Nugroho Pramudhita', 'email' => 'agung.pramudhita@polinema.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '031',
                'name'        => 'Universitas Katolik Soegijapranata',
                'username'    => 'biro_031',
                'email'       => 'erdhi@unika.ac.id',
                'koordinator' => [
                    ['nama' => 'Erdhi Widiarto Nugroho', 'email' => 'erdhi@unika.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '032',
                'name'        => 'Universitas Muhammadiyah Surakarta',
                'username'    => 'biro_032',
                'email'       => 'irma.yuliana@ums.ac.id',
                'koordinator' => [
                    ['nama' => 'Irma Yuliana', 'email' => 'irma.yuliana@ums.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '033',
                'name'        => 'Politeknik Negeri Jakarta',
                'username'    => 'biro_033',
                'email'       => 'hata.maulana@tik.pnj.ac.id',
                'koordinator' => [
                    ['nama' => 'Hata Maulana', 'email' => 'hata.maulana@tik.pnj.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '034',
                'name'        => 'Universitas Gajah Mada',
                'username'    => 'biro_034',
                'email'       => 'wahyo@mail.ugm.ac.id',
                'koordinator' => [
                    ['nama' => 'Wahyono', 'email' => 'wahyo@mail.ugm.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '035',
                'name'        => 'Universitas Negeri Yogyakarta',
                'username'    => 'biro_035',
                'email'       => 'handaru@uny.ac.id',
                'koordinator' => [
                    ['nama' => 'Handaru Jati', 'email' => 'handaru@uny.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '036',
                'name'        => 'Politeknik Negeri Bandung',
                'username'    => 'biro_036',
                'email'       => 'widhiyasana@polban.ac.id',
                'koordinator' => [
                    ['nama' => 'Yudi Widhiyasana', 'email' => 'widhiyasana@polban.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '037',
                'name'        => 'Universitas Hasanudin',
                'username'    => 'biro_037',
                'email'       => 'amil_ai@yahoo.com',
                'koordinator' => [
                    ['nama' => 'Amil Ahmad Ilham', 'email' => 'amil_ai@yahoo.com'],
                ],
            ],
            [
                'kode_biro'   => '038',
                'name'        => 'UIN Alauddin Makassar',
                'username'    => 'biro_038',
                'email'       => 'firmansyah.ibrahim@uin-alauddin.ac.id',
                'koordinator' => [
                    ['nama' => 'Firmansyah Ibrahim', 'email' => 'firmansyah.ibrahim@uin-alauddin.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '039',
                'name'        => 'Universitas Pasundan',
                'username'    => 'biro_039',
                'email'       => 'pbasari@unpas.ac.id',
                'koordinator' => [
                    ['nama' => 'Ayi Purbasari', 'email' => 'pbasari@unpas.ac.id'],
                    ['nama' => 'Mellia Liyanthy', 'email' => 'liyanthy@unpas.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '040',
                'name'        => 'Universitas Telkom',
                'username'    => 'biro_040',
                'email'       => 'ema.rachmawati22@gmail.com',
                'koordinator' => [
                    ['nama' => 'Ema Rachmawati', 'email' => 'ema.rachmawati22@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '041',
                'name'        => 'Universitas Sanata Dharma',
                'username'    => 'biro_041',
                'email'       => 'rosa.usd@gmail.com',
                'koordinator' => [
                    ['nama' => 'Prima Rosa', 'email' => 'rosa.usd@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '042',
                'name'        => 'Universitas Andalas',
                'username'    => 'biro_042',
                'email'       => 'narwen68@gmail.com',
                'koordinator' => [
                    ['nama' => 'Narwen', 'email' => 'narwen68@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '043',
                'name'        => 'Universitas Islam Nusantara',
                'username'    => 'biro_043',
                'email'       => 'anggersaloko@uninus.ac.id',
                'koordinator' => [
                    ['nama' => 'Angger Saloko', 'email' => 'anggersaloko@uninus.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '044',
                'name'        => 'Universitas Sains Al-Qur’an Wonosobo',
                'username'    => 'biro_044',
                'email'       => 'asmarajati@unsiq.ac.id',
                'koordinator' => [
                    ['nama' => 'Dian Asmarajati', 'email' => 'asmarajati@unsiq.ac.id'],
                    ['nama' => 'Muhamad Fuat Asnawi', 'email' => 'fuatasnawi@unsiq.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '045',
                'name'        => 'Universitas Bina Nusantara',
                'username'    => 'biro_045',
                'email'       => 'etanuar@binus.edu',
                'koordinator' => [
                    ['nama' => 'Evawaty Tanuar', 'email' => 'etanuar@binus.edu'],
                    ['nama' => 'Felix Jingga', 'email' => 'fejingga@binus.edu'],
                ],
            ],
            [
                'kode_biro'   => '046',
                'name'        => 'Universitas Komputer Indonesia',
                'username'    => 'biro_046',
                'email'       => 'adam@email.unikom.ac.id',
                'koordinator' => [
                    ['nama' => 'Adam Mukharil Bachtiar', 'email' => 'adam@email.unikom.ac.id'],
                    ['nama' => 'Dian Dharmayanti', 'email' => 'dian.dharmayanti@email.unikom.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '047',
                'name'        => 'Universitas Singaperbangsa Karawang',
                'username'    => 'biro_047',
                'email'       => 'budi.arif@staff.unsika.ac.id',
                'koordinator' => [
                    ['nama' => 'Budi Arif Dermawan', 'email' => 'budi.arif@staff.unsika.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '048',
                'name'        => 'Universitas Pendidikan Indonesia Kampus Purwakarta',
                'username'    => 'biro_048',
                'email'       => 'hikmariz@upi.edu',
                'koordinator' => [
                    ['nama' => 'Rizki Hikmawan', 'email' => 'hikmariz@upi.edu'],
                ],
            ],
            [
                'kode_biro'   => '049',
                'name'        => 'Universitas President',
                'username'    => 'biro_049',
                'email'       => 'wansen@president.ac.id',
                'koordinator' => [
                    ['nama' => 'Tjong Wan Sen', 'email' => 'wansen@president.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '050',
                'name'        => 'STIKOM PGRI Banyuwangi',
                'username'    => 'biro_050',
                'email'       => 'yoyonstikom@gmail.com',
                'koordinator' => [
                    ['nama' => 'Yoyon Arie Budi', 'email' => 'yoyonstikom@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '051',
                'name'        => 'Universitas Bumigora',
                'username'    => 'biro_051',
                'email'       => 'augustin.kartarina@gmail.com',
                'koordinator' => [
                    ['nama' => 'Kartarina Augustine', 'email' => 'augustin.kartarina@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '052',
                'name'        => 'Universitas Pendidikan Indonesia',
                'username'    => 'biro_052',
                'email'       => 'rasim@upi.edu',
                'koordinator' => [
                    ['nama' => 'Rasim', 'email' => 'rasim@upi.edu'],
                ],
            ],
            [
                'kode_biro'   => '053',
                'name'        => 'Universitas Padjadjaran',
                'username'    => 'biro_053',
                'email'       => 'mira.suryani@unpad.ac.id',
                'koordinator' => [
                    ['nama' => 'Mira Suryani', 'email' => 'mira.suryani@unpad.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '054',
                'name'        => 'Universitas Katolik De La Salle Manado',
                'username'    => 'biro_054',
                'email'       => 'tsuwanto@unikadelasalle.ac.id',
                'koordinator' => [
                    ['nama' => 'Thomas Christian Suwanto', 'email' => 'tsuwanto@unikadelasalle.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '055',
                'name'        => 'Universitas Ahmad Dahlan',
                'username'    => 'biro_055',
                'email'       => 'dewi.ismi@tif.uad.ac.id',
                'koordinator' => [
                    ['nama' => 'Dewi Pramudi Ismi', 'email' => 'dewi.ismi@tif.uad.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '056',
                'name'        => 'Universitas Negeri Makassar',
                'username'    => 'biro_056',
                'email'       => 'dyah_vitalocca@unm.ac.id',
                'koordinator' => [
                    ['nama' => 'Dyah Vitalocca', 'email' => 'dyah_vitalocca@unm.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '057',
                'name'        => 'Universitas Pendidikan Muhammadiyah Sorong',
                'username'    => 'biro_057',
                'email'       => 'mataharimanaf@yahoo.co.id',
                'koordinator' => [
                    ['nama' => 'Matahari', 'email' => 'mataharimanaf@yahoo.co.id'],
                ],
            ],
            [
                'kode_biro'   => '058',
                'name'        => 'Universitas Al-Azhar Indonesia',
                'username'    => 'biro_058',
                'email'       => 'arif.supriyanto@if.uai.ac.id',
                'koordinator' => [
                    ['nama' => 'Arif Supriyanto', 'email' => 'arif.supriyanto@if.uai.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '059',
                'name'        => 'Politeknik Negeri Indramayu',
                'username'    => 'biro_059',
                'email'       => 'shumaru@polindra.ac.id',
                'koordinator' => [
                    ['nama' => 'A. Sumarudin', 'email' => 'shumaru@polindra.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '060',
                'name'        => 'Universitas Nahdlatul Ulama Sunan Giri Bojonegoro',
                'username'    => 'biro_060',
                'email'       => 'hastie.audytra@gmail.com',
                'koordinator' => [
                    ['nama' => 'Hastie Audytra', 'email' => 'hastie.audytra@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '061',
                'name'        => 'Institut Bisnis dan Informatika Kwik Kian Gie',
                'username'    => 'biro_061',
                'email'       => 'richard.vinc@kwikkiangie.ac.id',
                'koordinator' => [
                    ['nama' => 'Richard Vinc Nicolay Santoso', 'email' => 'richard.vinc@kwikkiangie.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '062',
                'name'        => 'Universitas Internasional Semen Indonesia',
                'username'    => 'biro_062',
                'email'       => 'yohanes.riskajaya@uisi.ac.id',
                'koordinator' => [
                    ['nama' => 'Yohanes Indra Riskajaya', 'email' => 'yohanes.riskajaya@uisi.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '063',
                'name'        => 'Politeknik Negeri Madiun',
                'username'    => 'biro_063',
                'email'       => 'lutfiyah17@pnm.ac.id',
                'koordinator' => [
                    ['nama' => 'Lutfiyah Dwi Setia', 'email' => 'lutfiyah17@pnm.ac.id'],
                    ['nama' => 'Susilo Veri Yulianto', 'email' => 'susilo@pnm.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '064',
                'name'        => 'Universitas Kristen Petra',
                'username'    => 'biro_064',
                'email'       => 'bebras@petra.ac.id',
                'koordinator' => [
                    ['nama' => 'Lily Puspa Dewi', 'email' => 'bebras@petra.ac.id'],
                    ['nama' => 'Agustinus N.', 'email' => 'bebras@petra.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '065',
                'name'        => 'Universitas Muhammadiyah Magelang',
                'username'    => 'biro_065',
                'email'       => 'pristi.sukmasetya@ummgl.ac.id',
                'koordinator' => [
                    ['nama' => 'Pristi Sukmasetya', 'email' => 'pristi.sukmasetya@ummgl.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '066',
                'name'        => 'Universitas Surabaya',
                'username'    => 'biro_066',
                'email'       => 'monica@staff.ubaya.ac.id',
                'koordinator' => [
                    ['nama' => 'Monica Widiasri', 'email' => 'monica@staff.ubaya.ac.id'],
                    ['nama' => 'Vincentius Riandaru Prasetyo', 'email' => 'vincent@staff.ubaya.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '067',
                'name'        => 'Universitas Katolik Indonesia Atma Jaya',
                'username'    => 'biro_067',
                'email'       => 'denny.jean@atmajaya.ac.id',
                'koordinator' => [
                    ['nama' => 'Denny Jean Cross Sihombing', 'email' => 'denny.jean@atmajaya.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '068',
                'name'        => 'Universitas Ciputra Surabaya',
                'username'    => 'biro_068',
                'email'       => 'stephanus.eko@ciputra.ac.id',
                'koordinator' => [
                    ['nama' => 'Stephanus Eko Wahyudi', 'email' => 'stephanus.eko@ciputra.ac.id'],
                    ['nama' => 'Laura Mahendratta Tjahjono', 'email' => 'laura@ciputra.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '069',
                'name'        => 'Universitas Katolik Widya Mandala Surabaya',
                'username'    => 'biro_069',
                'email'       => 'vielpi@ukwms.ac.id',
                'koordinator' => [
                    ['nama' => 'Dr. V. Luluk Prijambodo, M.Pd.', 'email' => 'vielpi@ukwms.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '070',
                'name'        => 'Universitas Kristen Duta Wacana',
                'username'    => 'biro_070',
                'email'       => 'dito@ukdw.ac.id',
                'koordinator' => [
                    ['nama' => 'Restyandito', 'email' => 'dito@ukdw.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '071',
                'name'        => 'Politeknik Manufaktur Negeri Bangka Belitung',
                'username'    => 'biro_071',
                'email'       => 'paruliansilalahi1964@gmail.com',
                'koordinator' => [
                    ['nama' => 'Parulian Silalahi', 'email' => 'paruliansilalahi1964@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '072',
                'name'        => 'Universitas Kristen Krida Wacana',
                'username'    => 'biro_072',
                'email'       => 'rita.wiryasaputra@ukrida.ac.id',
                'koordinator' => [
                    ['nama' => 'Rita Wiryasaputra', 'email' => 'rita.wiryasaputra@ukrida.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '073',
                'name'        => 'Institut Agama Islam Negeri Salatiga',
                'username'    => 'biro_073',
                'email'       => 'munajatstain@gmail.com',
                'koordinator' => [
                    ['nama' => 'Munajat', 'email' => 'munajatstain@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '074',
                'name'        => 'Universitas Muhammadiyah Purworejo',
                'username'    => 'biro_074',
                'email'       => 'ikeypasa@umpwr.ac.id',
                'koordinator' => [
                    ['nama' => 'Ike Yunia Pasa', 'email' => 'ikeypasa@umpwr.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '075',
                'name'        => 'Institut Teknologi dan Bisnis Kalbis',
                'username'    => 'biro_075',
                'email'       => 'yulius.prabowo@kalbis.ac.id',
                'koordinator' => [
                    ['nama' => 'Yulius Denny Prabowo', 'email' => 'yulius.prabowo@kalbis.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '076',
                'name'        => 'Universitas Mercu Buana Jakarta',
                'username'    => 'biro_076',
                'email'       => 'desi.ramayanti@mercubuana.ac.id',
                'koordinator' => [
                    ['nama' => 'Desi Ramayanti', 'email' => 'desi.ramayanti@mercubuana.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '077',
                'name'        => 'UPN “Veteran” Jakarta',
                'username'    => 'biro_077',
                'email'       => 'erlykrisnanik@upnvj.ac.id',
                'koordinator' => [
                    ['nama' => 'Erly Krisnanik', 'email' => 'erlykrisnanik@upnvj.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '078',
                'name'        => 'Universitas Pertamina',
                'username'    => 'biro_078',
                'email'       => 'intanoktafiani.if@gmail.com',
                'koordinator' => [
                    ['nama' => 'Intan Oktafiani', 'email' => 'intanoktafiani.if@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '079',
                'name'        => 'Universitas Dr. Soetomo',
                'username'    => 'biro_079',
                'email'       => 'vega@unitomo.ac.id',
                'koordinator' => [
                    ['nama' => 'Anik Vega Vitianingsih, S.Kom., MT', 'email' => 'vega@unitomo.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '080',
                'name'        => 'Universitas Bakrie',
                'username'    => 'biro_080',
                'email'       => 'refyul.fatri@bakrie.ac.id',
                'koordinator' => [
                    ['nama' => 'Refyul Rey Fatri', 'email' => 'refyul.fatri@bakrie.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '081',
                'name'        => 'Universitas Bengkulu',
                'username'    => 'biro_081',
                'email'       => 'setiono.pgsd@unib.ac.id',
                'koordinator' => [
                    ['nama' => 'Panut Setiono', 'email' => 'setiono.pgsd@unib.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '082',
                'name'        => 'UIN Walisongo Semarang',
                'username'    => 'biro_082',
                'email'       => 'bebras@walisongo.ac.id',
                'koordinator' => [
                    ['nama' => 'Lulu Choirun Nisa', 'email' => 'bebras@walisongo.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '083',
                'name'        => 'Universitas Islam Sultan Agung Semarang',
                'username'    => 'biro_083',
                'email'       => 'cakmustafa@unissula.ac.id',
                'koordinator' => [
                    ['nama' => 'Mustafa, ST, MM, MKom.', 'email' => 'cakmustafa@unissula.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '084',
                'name'        => 'UIN Imam Bonjol Padang',
                'username'    => 'biro_084',
                'email'       => 'yasrulhuda@uinib.ac.id',
                'koordinator' => [
                    ['nama' => 'Yasrul Huda', 'email' => 'yasrulhuda@uinib.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '085',
                'name'        => 'Institut Pesantren Mathali’ul Falah Pati',
                'username'    => 'biro_085',
                'email'       => 'adzroie@ipmafa.ac.id',
                'koordinator' => [
                    ['nama' => 'Imam Adzro’i, M.Sc.', 'email' => 'adzroie@ipmafa.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '086',
                'name'        => 'UIN Sunan Gunung Djati Bandung',
                'username'    => 'biro_086',
                'email'       => 'bebras@uinsgd.ac.id',
                'koordinator' => [
                    ['nama' => 'Dian Sa’adillah Maylawati', 'email' => 'bebras@uinsgd.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '087',
                'name'        => 'IAIN Pekalongan',
                'username'    => 'biro_087',
                'email'       => 'santikalyadiahpramesti@iainpekalongan.ac.id',
                'koordinator' => [
                    ['nama' => 'Santika Lya Diah Pramesti, M.Pd.', 'email' => 'santikalyadiahpramesti@iainpekalongan.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '088',
                'name'        => 'UIN Sultan Aji Muhammad Idris Samarinda',
                'username'    => 'biro_088',
                'email'       => 'bebras.uinsi.samarinda@gmail.com',
                'koordinator' => [
                    ['nama' => 'Bambang Iswanto', 'email' => 'bebras.uinsi.samarinda@gmail.com'],
                ],
            ],
            [
                'kode_biro'   => '089',
                'name'        => 'Politeknik Jambi',
                'username'    => 'biro_089',
                'email'       => 'tanto@politeknikjambi.ac.id',
                'koordinator' => [
                    ['nama' => 'Tanto ST., M. Kom', 'email' => 'tanto@politeknikjambi.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '090',
                'name'        => 'Universitas Trisakti',
                'username'    => 'biro_090',
                'email'       => 'binti@trisakti.ac.id',
                'koordinator' => [
                    ['nama' => 'Binti Solihah', 'email' => 'binti@trisakti.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '091',
                'name'        => 'Institut Teknologi dan Bisnis Yadika Pasuruan',
                'username'    => 'biro_091',
                'email'       => 'erri@itbyadika.ac.id',
                'koordinator' => [
                    ['nama' => 'Erri Wahyu Puspitarini', 'email' => 'erri@itbyadika.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '092',
                'name'        => 'Universitas Widyagama Malang',
                'username'    => 'biro_092',
                'email'       => 'fitrimarisa@widyagama.ac.id',
                'koordinator' => [
                    ['nama' => 'Fitri Marisa', 'email' => 'fitrimarisa@widyagama.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '093',
                'name'        => 'UPN “Veteran” Jawa Timur',
                'username'    => 'biro_093',
                'email'       => 'seftin.fitri.si@upnjatim.ac.id',
                'koordinator' => [
                    ['nama' => 'Seftin Fitri Ana Wati, S.Kom., M.Kom', 'email' => 'seftin.fitri.si@upnjatim.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '094',
                'name'        => 'Universitas Budi Luhur',
                'username'    => 'biro_094',
                'email'       => 'mohammad.syafrullah@budiluhur.ac.id',
                'koordinator' => [
                    ['nama' => 'Mohammad Syafrullah', 'email' => 'mohammad.syafrullah@budiluhur.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '095',
                'name'        => 'Universitas Syiah Kuala',
                'username'    => 'biro_095',
                'email'       => 'yudha.nurdin@usk.ac.id',
                'koordinator' => [
                    ['nama' => 'Ir. Yudha Nurdin, S.T., M.T.', 'email' => 'yudha.nurdin@usk.ac.id'],
                ],
            ],
            [
                'kode_biro'   => '096',
                'name'        => 'Universitas Pignatelli Triputra',
                'username'    => 'biro_096',
                'email'       => 'moyohadi@gmail.com',
                'koordinator' => [
                    ['nama' => 'Moyo Hady Poernomo, S.Kom., M.Kom.', 'email' => 'moyohadi@gmail.com'],
                ],
            ],
        ];

        foreach ($biros as $biroData) {
            $user = User::updateOrCreate(
                ['kode_biro' => $biroData['kode_biro']],
                [
                    'name'     => $biroData['name'],
                    'username' => $biroData['username'],
                    'email'    => $biroData['email'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->roles()->syncWithoutDetaching([$biroRole->id]);

            // Sinkronisasi data koordinator biro
            $user->koordinatorBiro()->delete();
            foreach ($biroData['koordinator'] as $coord) {
                $user->koordinatorBiro()->create([
                    'nama'  => $coord['nama'],
                    'email' => $coord['email'],
                ]);
            }
        }
    }
}
