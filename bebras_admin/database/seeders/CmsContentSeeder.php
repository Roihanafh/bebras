<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\Setting;
use App\Models\Kegiatan;
use App\Models\TentangBebras;
use App\Models\TentangBebrasItem;
use App\Models\MenuSoal;
use App\Models\MenuSoalItem;
use App\Models\SoalBook;
use App\Models\SoalChallenge;
use App\Models\SoalChallengeOption;
use App\Models\Latihan;
use App\Models\Kontak;
use App\Models\KontakDetail;
use App\Models\MenuKegiatan;

class CmsContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === 1. Banners (Hero Carousel) ===
        Banner::create([
            'judul' => 'Bebras Challenge 2023',
            'deskripsi' => 'Ajang tahunan untuk mengasah kemampuan computational thinking siswa',
            'gambar' => 'img/banner1.jpg',
            'urutan' => 1,
        ]);
        Banner::create([
            'judul' => 'Workshop Guru',
            'deskripsi' => 'Meningkatkan kapasitas guru dalam pembelajaran computational thinking',
            'gambar' => 'img/banner2.jpg',
            'urutan' => 2,
        ]);
        Banner::create([
            'judul' => 'Kompetisi Nasional',
            'deskripsi' => 'Tunjukkan kemampuan problem solving terbaik dan raih penghargaan',
            'gambar' => 'img/banner3.jpg',
            'urutan' => 3,
        ]);
        Banner::create([
            'judul' => 'Tujuan Bebras',
            'deskripsi' => 'Mempromosikan informatika dan berpikir komputasi kepada para guru',
            'gambar' => 'img/banner4.jpg',
            'urutan' => 4,
        ]);

        // === 2. Settings (Global configuration) ===
        Setting::setByKey('home_cta_title', 'Bebras Indonesia Challenge 2024');
        Setting::setByKey('home_cta_description', 'Bebras Indonesia Challenge 2024 akan digelar pada 11-16 November 2024. Daftarkan diri Anda segera.');
        Setting::setByKey('home_cta_link', '#');
        Setting::setByKey('home_about_logo', 'img/logo.jpg');
        Setting::setByKey('home_about_content', '<p class="text-gray-600 mb-4 text-justify">Bebras pertama kali digelar di Lithuania (www.bebras.org), merupakan aktivitas ekstra kurikuler yang mengedukasi kemampuan problem solving dalam informatika dengan jumlah peserta terbanyak di dunia. Siswa peserta akan mengikuti kompetisi bebras di bawah supervisi guru, yang dapat mengintegrasikan tantangan tersebut dalam aktivitas mengajar guru. Kompetisi ini dilakukan setiap tahun secara online melalui komputer.</p><p class="text-gray-600 mb-6 text-justify">Yang dilombakan dalam kompetisi adalah sekumpulan soal yang disebut Bebras task. Bebras task disajikan dalam bentuk uraian persoalan yang dilengkapi dengan gambar yang menarik, sehingga siswa dapat lebih mudah memahami soal. Soal-soal tersebut dapat dijawab tanpa perlu belajar informatika terlebih dahulu, tapi soal tersebut sebetulnya terkait pada konsep tertentu dalam informatika dan computational thinking. Bebras task sekaligus menunjukkan aspek informatika dan computational thinking.</p>');

        // Soal page settings (used by FE views)
        Setting::setByKey('index_soal_footer_text', '<p>Soal-soal Bebras dirancang untuk mendorong siswa berpikir kritis dan kreatif dalam menyelesaikan permasalahan informatika. Bergabunglah dan jadilah bagian dari tantangan berpikir komputasional tingkat nasional!</p>');
        Setting::setByKey('sma_question_part_2', '<p>Perhatikan kondisi berikut sebelum menjawab pertanyaan di atas. Pilih jawaban yang paling tepat berdasarkan analisis algoritmis yang benar.</p>');

        // === 3. Kegiatans (Unified list) ===
        // NOTE: menu_kegiatan_id will be filled after MenuKegiatan records are created (section 8).
        // Here we seed the "kegiatan_utama" with null menu_kegiatan_id (beranda only).
        Kegiatan::create([
            'tipe'    => 'kegiatan_utama',
            'judul'   => 'Lokakarya Nasional',
            'deskripsi' => 'Berlangsung sekali setahun untuk koordinasi komite nasional (NBO Bebras Indonesia) dengan mitra (Bebras Biro), dan menetapkan soal-soal nasional.',
            'gambar'  => 'img/Lokakarya Nasional.jpeg',
            'urutan'  => 1,
            'status_validasi' => 'approved',
        ]);
        Kegiatan::create([
            'tipe'    => 'kegiatan_utama',
            'judul'   => 'Lokakarya untuk Guru',
            'deskripsi' => 'Workshop/lokakarya dilaksanakan oleh Bebras Biro untuk memberi bekal kepada guru agar para guru mampu memperkenalkan konsep berpikir komputasi.',
            'gambar'  => 'img/Lokakarya untuk Guru.jpeg',
            'urutan'  => 2,
            'status_validasi' => 'approved',
        ]);
        Kegiatan::create([
            'tipe'    => 'kegiatan_utama',
            'judul'   => 'Tantangan Berpikir Komputasional Bebras',
            'deskripsi' => 'Diselenggarakan sesuai jadwal yang ditetapkan komite internasional, biasanya minggu kedua atau ketiga November (disebut Bebras Week).',
            'gambar'  => 'img/Tantangan Berpikir.jpeg',
            'urutan'  => 3,
            'status_validasi' => 'approved',
        ]);

        // Workshop 2017 cards — menu_kegiatan_id will be set in section 8 after MenuKegiatan seeding
        $w2017Items = [
            ['judul' => 'Institut Pertanian Bogor, 25 Oktober 2017',        'deskripsi' => '"Workshop Computational Thinking and Bebras Challenge 2017" <br><span class="font-semibold">NBO Bebras Indonesia, Julio Adisantoso</span>',  'gambar' => 'img/workshop_a1.jpeg', 'kota' => 'Bogor',     'urutan' => 1],
            ['judul' => 'Universitas Dian Nuswantoro, 12 Oktober 2017',     'deskripsi' => 'Workshop Computational Thinking Guru-guru Semarang <br><span class="font-semibold">Dr. Inggriani</span>',                                    'gambar' => 'img/workshop_a2.jpg',  'kota' => 'Semarang',  'urutan' => 2],
            ['judul' => 'Institut Teknologi Sumatera, 23 September 2017',   'deskripsi' => 'Workshop Computational Thinking Initiative <br><span class="font-semibold">Dr. Inggriani</span>',                                               'gambar' => 'img/workshop_a3.jpg',  'kota' => 'Lampung',   'urutan' => 3],
            ['judul' => 'Universitas Kristen Maranatha, 22 September 2017', 'deskripsi' => 'Bebras Indonesia CT Challenge, Teacher Workshop <br><span class="font-semibold">Dr. Inggriani</span>',                                          'gambar' => 'img/workshop_a4.jpg',  'kota' => 'Bandung',   'urutan' => 4],
            ['judul' => 'Universitas Lambung Mangkurat, 18 Juli 2017',      'deskripsi' => 'Workshop Bebras CT & Competitive Programming <br><span class="font-semibold">Dr. Inggriani</span>',                                             'gambar' => 'img/workshop_a5.jpg',  'kota' => 'Samarinda', 'urutan' => 5],
            ['judul' => 'Universitas Udayana, 13 Juli 2017',                'deskripsi' => 'Kuliah Tamu Computational Thinking <br><span class="font-semibold">Prof. Dr. Valentina Dagiene</span>',                                         'gambar' => 'img/workshop_a6.jpg',  'kota' => 'Bali',      'urutan' => 6],
            ['judul' => 'Politeknik Caltex Riau, 6 Juli 2017',              'deskripsi' => 'Professor & Expert visit series 2017: Bebras CT <br><span class="font-semibold">Prof. Dr. Valentina Dagiene</span>',                            'gambar' => 'img/workshop_a7.jpg',  'kota' => 'Pekanbaru', 'urutan' => 7],
        ];
        foreach ($w2017Items as $item) {
            Kegiatan::create(array_merge($item, ['tipe' => 'workshop_2017', 'status_validasi' => 'approved']));
        }

        // === 4. Tentang Bebras Pages ===
        // dd_1
        $dd1 = TentangBebras::create([
            'slug' => 'dd_1',
            'judul' => 'Apa itu Berpikir Komputasional?',
            'konten' => 'Berpikir komputasional (Computational Thinking) adalah metode menyelesaikan persoalan dengan menerapkan teknik ilmu komputer (informatika). Tantangan Bebras menyajikan soal-soal yang mendorong siswa untuk berpikir kreatif dan kritis dalam menyelesaikan persoalan dengan menerapkan konsep-konsep berpikir komputasional.',
            'gambar' => 'img/brain.png',
            'urutan' => 1,
            'template' => 'dd_1'
        ]);

        // dd_2
        $dd2 = TentangBebras::create([
            'slug' => 'dd_2',
            'judul' => 'Apa itu Bebras?',
            'konten' => '<p class="text-justify text-md">Secara harfiah, “Bebras” adalah kata dalam bahasa Lithuania, yang berarti “berang-berang” dalam bahasa Indonesia. Prof. Valentina Dagiene dari Universitas Vilnius, Lithuania adalah yang mencetuskan gagasan Bebras Computational Thinking Challenge, yang saat ini diikuti oleh lebih dari 55 negara di dunia.</p><p class="text-justify text-md mt-4">Bebras adalah sebuah inisiatif internasional yang tujuannya adalah untuk mempromosikan Computational Thinking (Berpikir dengan landasan Komputasi atau Informatika), di kalangan guru dan murid mulai tingkat SD, serta untuk masyarakat luas.</p><p class="text-justify text-md mt-4">Cara untuk promosi adalah dengan menyelenggarakan kegiatan kompetisi secara daring (on line), yang disebut sebagai “Tantangan Bebras”. Tantangan Bebras bukan hanya sekedar untuk menang. Selain untuk berlomba, tantangan Bebras juga bertujuan agar siswa belajar Computational Thinking selama maupun setelah lomba.</p><p class="text-justify text-md mt-4">Di Indonesia, kompetisi dapat dilaksanakan di sekolah yang mempunyai cukup komputer, atau di universitas pembina.</p><p class="text-justify text-md mt-4">Selama Kompetisi, siswa harus memberikan solusi untuk persoalan yang disebut “Soal Bebras”. Soal-soal yang bertema komputasi/informatika ini dirancang semenarik mungkin, dan seharusnya dapat dijawab oleh siswa tanpa pengetahuan sebelumnya tentang komputasi atau informatika.</p><p class="text-justify text-md mt-4">Setiap soal Bebras mengandung aspek komputasi atau informatika dan dimaksudkan untuk menguji bakat peserta untuk berpikir komputasi atau informatika. Untuk menjawab soal-soal Bebras, secara alamiah, siswa dituntut untuk berpikir terkait dengan informasi, struktur diskrit, komputasi, pengolahan data, serta harus menggunakan konsep algoritmik.</p>',
            'gambar' => 'img/pilnas.png',
            'urutan' => 2,
            'template' => 'dd_2'
        ]);

        // dd_3
        $dd3 = TentangBebras::create([
            'slug' => 'dd_3',
            'judul' => 'Tujuan Kami',
            'konten' => '<p class="text-justify text-md">Tujuan utamanya adalah untuk mempromosikan informatika dan berpikir komputasi kepada para guru dan anak-anak muda khususnya, di kalangan pengambil keputusan di bidang pendidikan, dan masyarakat luas.</p><p class="text-justify text-md mt-4">Komputer dan perangkat teknologi lainnya saat ini menjadi penting untuk membuat masyarakat umum mengetahui komputasi atau informatika, tidak hanya sebagai teknologi, tetapi juga sebagai ilmu untuk mendidik mereka dan membuat pengalaman mereka dengan teknologi yang lebih baik.</p>',
            'gambar' => 'img/goal.png',
            'urutan' => 3,
            'template' => 'dd_3'
        ]);
        // dd_3 items
        $dd3->items()->createMany([
            ['tipe' => 'tujuan', 'icon' => '🌱', 'judul' => 'Menumbuhkan kreativitas & berpikir komputasi', 'deskripsi' => 'Mendorong cara berpikir terstruktur, eksploratif, dan berbasis data.', 'urutan' => 1],
            ['tipe' => 'tujuan', 'icon' => '💡', 'judul' => 'Pemahaman teknologi informasi', 'deskripsi' => 'Konsep dipetakan ke praktik agar lebih mudah diserap.', 'urutan' => 2],
            ['tipe' => 'tujuan', 'icon' => '🚀', 'judul' => 'Antusiasme dalam belajar', 'deskripsi' => 'Aktivitas berbasis proyek membuat siswa lebih semangat.', 'urutan' => 3],
            ['tipe' => 'tujuan', 'icon' => '🖥️', 'judul' => 'Literasi digital sejak dini', 'deskripsi' => 'Melibatkan siswa memanfaatkan komputer & aplikasi sejak sekolah dasar.', 'urutan' => 4],
            ['tipe' => 'tujuan', 'icon' => '📘', 'judul' => 'Manfaat TI untuk semua mata pelajaran', 'deskripsi' => 'Membantu memahami, menganalisis, dan mempresentasikan berbagai pelajaran.', 'urutan' => 5],
        ]);

        // dd_4
        $dd4 = TentangBebras::create([
            'slug' => 'dd_4',
            'judul' => 'Ruang lingkup kegiatan bebras di antaranya adalah:',
            'konten' => '',
            'urutan' => 4,
            'template' => 'dd_4'
        ]);
        // dd_4 items (using SVG paths as icons)
        $dd4->items()->createMany([
            ['tipe' => 'ruang_lingkup', 'icon' => 'M12 3v18M3 12h18', 'judul' => 'Menumbuhkan kreativitas, budaya informasi, algoritma & berpikir komputasi', 'deskripsi' => 'Mendorong cara berpikir terstruktur, eksploratif, dan berbasis data.', 'bg_color' => 'from-indigo-500 via-fuchsia-500 to-pink-500', 'urutan' => 1],
            ['tipe' => 'ruang_lingkup', 'icon' => 'M12 6v6l4 2', 'judul' => 'Memudahkan pemahaman mendalam teknologi informasi', 'deskripsi' => 'Materi dipetakan dari konsep ke praktik agar lebih mudah diserap.', 'bg_color' => 'from-emerald-500 via-teal-500 to-cyan-500', 'urutan' => 2],
            ['tipe' => 'ruang_lingkup', 'icon' => 'M5 12h14M12 5l7 7-7 7', 'judul' => 'Mendorong antusiasme penggunaan TI dalam belajar', 'deskripsi' => 'Tugas berbasis proyek & aplikasi nyata untuk meningkatkan motivasi.', 'bg_color' => 'from-amber-500 via-orange-500 to-red-500', 'urutan' => 3],
            ['tipe' => 'ruang_lingkup', 'icon' => 'M20 7l-8 10L4 12', 'judul' => 'Melibatkan anak sejak dini dengan TI, komputer & aplikasi', 'deskripsi' => 'Aktivitas hands-on di kelas & lab untuk membangun literasi digital awal.', 'bg_color' => 'from-sky-500 via-blue-500 to-indigo-500', 'urutan' => 4],
            ['tipe' => 'ruang_lingkup', 'icon' => 'M3 7h18M3 12h14M3 17h10', 'judul' => 'Menjelaskan manfaat TI untuk semua mata pelajaran', 'deskripsi' => 'Menunjukkan bagaimana TI membantu memahami, menganalisis, dan mempresentasikan materi pelajaran.', 'bg_color' => 'from-fuchsia-500 via-purple-500 to-violet-500', 'urutan' => 5],
        ]);

        // dd_5
        $dd5 = TentangBebras::create([
            'slug' => 'dd_5',
            'judul' => 'Kegiatan Bebras Indonesia',
            'konten' => '<p class="text-gray-700 dark:text-gray-300 text-justify mb-6">Kegiatan Bebras Indonesia terdiri dari beberapa agenda rutin dan tambahan untuk mendukung pengembangan berpikir komputasional bagi guru, siswa, dan masyarakat luas.</p>',
            'gambar' => 'img/logo.jpg',
            'urutan' => 5,
            'template' => 'dd_5'
        ]);
        // dd_5 items (lists & categories)
        $dd5->items()->createMany([
            ['tipe' => 'kegiatan_list', 'icon' => '📌', 'judul' => 'Lokakarya Nasional', 'deskripsi' => 'Dilaksanakan setahun sekali untuk koordinasi komite nasional dengan mitra, sekaligus menetapkan soal-soal nasional.', 'bg_color' => 'bg-[#F8FAE5]', 'urutan' => 1],
            ['tipe' => 'kegiatan_list', 'icon' => '👩‍🏫', 'judul' => 'Lokakarya untuk Guru', 'deskripsi' => 'Membekali guru agar dapat memperkenalkan konsep komputasi dan tantangan Bebras ke siswa.', 'bg_color' => 'bg-[#EAF4FC]', 'urutan' => 2],
            ['tipe' => 'kegiatan_list', 'icon' => '🧩', 'judul' => 'Tantangan Bebras', 'deskripsi' => 'Diselenggarakan sesuai jadwal komite internasional (Bebras Week), biasanya minggu kedua atau ketiga November.', 'bg_color' => 'bg-[#FFF2F2]', 'urutan' => 3],
            ['tipe' => 'kegiatan_list', 'icon' => '🔄', 'judul' => 'Kegiatan Tambahan', 'deskripsi' => 'Misalnya putaran kedua tingkat nasional, pengumpulan data, pengembangan makalah penelitian, dan lainnya.', 'bg_color' => 'bg-[#F0F4FF]', 'urutan' => 4],
            
            ['tipe' => 'kategori_tantangan', 'judul' => 'SiKecil', 'deskripsi' => 'Untuk siswa hingga kelas 3 SD/MI', 'bg_color' => 'from-amber-100 to-yellow-200', 'urutan' => 5],
            ['tipe' => 'kategori_tantangan', 'judul' => 'Siaga', 'deskripsi' => 'Untuk siswa kelas 4–6 SD/MI', 'bg_color' => 'from-green-100 to-emerald-200', 'urutan' => 6],
            ['tipe' => 'kategori_tantangan', 'judul' => 'Penggalang', 'deskripsi' => 'Untuk siswa SMP/MTs', 'bg_color' => 'from-sky-100 to-blue-200', 'urutan' => 7],
            ['tipe' => 'kategori_tantangan', 'judul' => 'Penegak', 'deskripsi' => 'Untuk siswa SMA/MA/SMK/MAK', 'bg_color' => 'from-pink-100 to-rose-200', 'urutan' => 8],
        ]);
        // Also we store the dd_5 footer text in Setting
        Setting::setByKey('dd5_footer_text', 'Banyak kegiatan tambahan yang dilaksanakan di antara kegiatan-kegiatan tersebut. Di tahun mendatang, Bebras Indonesia berencana untuk menyelenggarakan kegiatan-kegiatan yang dilakukan oleh negara anggota lainnya, misalnya tantangan putaran kedua di tingkat nasional, mengumpulkan data dan mengembangkan makalah penelitian, dan lain-lain.');

        // dd_6
        $dd6 = TentangBebras::create([
            'slug' => 'dd_6',
            'judul' => 'Sejarah Bebras Indonesia',
            'konten' => 'Perjalanan awal Bebras di Indonesia since tahun 2016',
            'urutan' => 6,
            'template' => 'dd_6'
        ]);
        $dd6->items()->createMany([
            ['tipe' => 'timeline', 'judul' => 'Februari 2016', 'deskripsi' => 'Setelah kunjungannya ke Indonesia, <strong>Prof. Valentina Dagienė</strong> (Vilnius University, Lithuania) penggagas Bebras Internasional, mengundang Indonesia menjadi <em>observer</em> pada Workshop Internasional Bebras bulan Mei 2016 di Bodrum, Turki.', 'urutan' => 1],
            ['tipe' => 'timeline', 'judul' => 'Mei 2016', 'deskripsi' => 'Indonesia mengirimkan <strong>Dr. Inggriani Liem</strong> (Pembina TOKI) dan <strong>Soripada Harahap</strong> (staf Direktorat Pembinaan SMA, Kemdikbud RI) sebagai wakil Indonesia pada Workshop Internasional Bebras di Bodrum, Turki.', 'urutan' => 2],
            ['tipe' => 'timeline', 'judul' => 'November 2016', 'deskripsi' => 'Untuk pertama kalinya, Indonesia berpartisipasi dalam <strong>Bebras Challenge</strong> sesuai jadwal Komite Internasional Bebras, pada bulan November 2016.', 'urutan' => 3],
        ]);

        // === 5. Soal Pages (menu_soal) ===
        // index-soal
        $indexSoal = MenuSoal::create([
            'nama_menu' => 'Apa itu Soal Bebras?',
            'slug' => 'index-soal',
            'judul' => 'Soal Bebras',
            'gambar' => 'img/pilnas.png',
            'body' => '<p>Soal Bebras berperan penting bagi siswa (peserta kompetisi) maupun guru (sebagai penyusun soal). Siswa <span class="font-semibold ">didorong</span> untuk berpikir tentang informatika, sedangkan guru harus berpikir tentang kaitan kehidupan sehari-hari dengan ilmu komputer. Soal yang kreatif dan menarik adalah tantangan utama dalam penyelenggaraan kompetisi Bebras.</p><p class="mt-4">Penyusun soal Bebras berusaha memilih soal yang menarik untuk memotivasi siswa dalam mengidentifikasi persoalan informatika dan berpikir lebih dalam tentang teknologi. Mereka juga ingin menyajikan sebanyak mungkin topik informatika dan pembelajaran komputer. Di bidang informatika, masih ada masalah silabus. Bahkan di sekolah-sekolah di beberapa negara, sampai saat ini belum ada kesepakatan bersama materi apa yang harus dimasukkan dalam silabus informatika yang terpadu, dengan memanfaatkan teknologi informasi.</p><p class="mt-4">Karena dirancang untuk siswa mulai kelas SD, Soal Bebras dibuat pendek dan harus mengandung konsep informatika seperti:</p>',
            'urutan' => 1,
        ]);
        $indexSoal->items()->createMany([
            ['tipe' => 'konsep', 'judul' => 'Sequential dan concurrent', 'urutan' => 1],
            ['tipe' => 'konsep', 'judul' => 'Struktur data seperti heaps, stacks, dan queues', 'urutan' => 2],
            ['tipe' => 'konsep', 'judul' => 'Pemodelan status (“states”), control flow, dan data flow', 'urutan' => 3],
            ['tipe' => 'konsep', 'judul' => 'Interaksi manusia-komputer', 'urutan' => 4],
            ['tipe' => 'konsep', 'judul' => 'Grafis; dll', 'urutan' => 5],

            ['tipe' => 'kriteria', 'judul' => 'Mewakili konsep informatika', 'urutan' => 1],
            ['tipe' => 'kriteria', 'judul' => 'Mudah dimengerti', 'urutan' => 2],
            ['tipe' => 'kriteria', 'judul' => 'Dapat diselesaikan dalam waktu maksimal 3 menit', 'urutan' => 3],
            ['tipe' => 'kriteria', 'judul' => 'Pendek, misalnya disajikan pada satu halaman layar', 'urutan' => 4],
            ['tipe' => 'kriteria', 'judul' => 'Dapat diselesaikan di komputer tanpa alat tambahan', 'urutan' => 5],
            ['tipe' => 'kriteria', 'judul' => 'Bebas dari sistem tertentu', 'urutan' => 6],
            ['tipe' => 'kriteria', 'judul' => 'Menarik dan / atau lucu', 'urutan' => 7],
        ]);
        Setting::setByKey('index_soal_footer_text', 'Supaya dapat dipecahkan dalam waktu 3 menit, setiap soal kompetisi Bebras fokus pada topik pembelajaran informatika yang kecil. Soal harus memahami prinsip-prinsip, ide-ide dan konsep-konsep yang terlibat dalam sistem informatika. Beberapa soal dibuat <span class="italic">interaktif</span>, di mana siswa berinteraksi dengan objek di layar untuk menyelesaikan soal. Soal-soal interaktif bernuansa permainan dan mudah dimengerti.');

        // pembahasan-soal
        MenuSoal::create([
            'nama_menu' => 'Pembahasan Soal',
            'slug' => 'pembahasan-soal',
            'judul' => '📚 Pembahasan Soal Bebras',
            'gambar' => 'img/bebras.png',
            'urutan' => 3,
        ]);
        // Seed pembahasan books
        SoalBook::create(['kategori' => 'sikecil', 'judul' => 'Buku Bebras SiKecil 2020', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2020-SiKecil-OK-Okt2024.pdf', 'cover_image' => 'img/buku2020-sikecil.jpg', 'urutan' => 1]);
        
        SoalBook::create(['kategori' => 'siaga', 'judul' => 'Buku Bebras Siaga 2016', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2019/10/Bebras-Challenge-2016_Siaga.pdf', 'cover_image' => 'img/buku2020-sd.jpg', 'urutan' => 1]);
        SoalBook::create(['kategori' => 'siaga', 'judul' => 'Buku Bebras Siaga 2017', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2018/07/BukuBebras2017_SD.pdf', 'cover_image' => 'img/buku2020-sd.jpg', 'urutan' => 2]);
        SoalBook::create(['kategori' => 'siaga', 'judul' => 'Buku Bebras Siaga 2018', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2019/09/BukuBebras2018%20SD%20v.5%20rev-1.pdf', 'cover_image' => 'img/buku2020-sd.jpg', 'urutan' => 3]);
        SoalBook::create(['kategori' => 'siaga', 'judul' => 'Buku Bebras Siaga 2019', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2019-SD-v.Okt_.2024.pdf', 'cover_image' => 'img/buku2020-sd.jpg', 'urutan' => 4]);
        SoalBook::create(['kategori' => 'siaga', 'judul' => 'Buku Bebras Siaga 2020', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2020-SD-OK-Okt2024.pdf', 'cover_image' => 'img/buku2020-sd.jpg', 'urutan' => 5]);

        SoalBook::create(['kategori' => 'penggalang', 'judul' => 'Buku Bebras Penggalang 2016', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2019/10/Bebras-Challenge-2016_Penggalang.pdf', 'cover_image' => 'img/buku2020-smp.jpg', 'urutan' => 1]);
        SoalBook::create(['kategori' => 'penggalang', 'judul' => 'Buku Bebras Penggalang 2017', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2018/07/BukuBebras2017_SMP.pdf', 'cover_image' => 'img/buku2020-smp.jpg', 'urutan' => 2]);
        SoalBook::create(['kategori' => 'penggalang', 'judul' => 'Buku Bebras Penggalang 2018', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2019/09/BukuBebras2018%20SMP%20v.5.pdf', 'cover_image' => 'img/buku2020-smp.jpg', 'urutan' => 3]);
        SoalBook::create(['kategori' => 'penggalang', 'judul' => 'Buku Bebras Penggalang 2019', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2019-SMP-v.Okt_.2024.pdf', 'cover_image' => 'img/buku2020-smp.jpg', 'urutan' => 4]);
        SoalBook::create(['kategori' => 'penggalang', 'judul' => 'Buku Bebras Penggalang 2020', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2020-SMP-OK-Okt2024.pdf', 'cover_image' => 'img/buku2020-smp.jpg', 'urutan' => 5]);

        SoalBook::create(['kategori' => 'penegak', 'judul' => 'Buku Bebras Penegak 2016', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2019/10/Bebras-Challenge-2016_Penegak.pdf', 'cover_image' => 'img/buku2020-sma.jpg', 'urutan' => 1]);
        SoalBook::create(['kategori' => 'penegak', 'judul' => 'Buku Bebras Penegak 2017', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2018/07/BukuBebras2017_SMA.pdf', 'cover_image' => 'img/buku2020-sma.jpg', 'urutan' => 2]);
        SoalBook::create(['kategori' => 'penegak', 'judul' => 'Buku Bebras Penegak 2018', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2019/09/BukuBebras2018%20SMA%20v.5.pdf', 'cover_image' => 'img/buku2020-sma.jpg', 'urutan' => 3]);
        SoalBook::create(['kategori' => 'penegak', 'judul' => 'Buku Bebras Penegak 2019', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2019-SMA-v.Okt_.2024.pdf', 'cover_image' => 'img/buku2020-sma.jpg', 'urutan' => 4]);
        SoalBook::create(['kategori' => 'penegak', 'judul' => 'Buku Bebras Penegak 2020', 'pdf_link' => 'https://bebras.or.id/v3/wp-content/uploads/2024/10/Bebras-Indonesia-Book-2020-SMA-OK-Okt2024.pdf', 'cover_image' => 'img/buku2020-sma.jpg', 'urutan' => 5]);

        // contoh-soal parent
        $contohSoal = MenuSoal::create([
            'nama_menu' => 'Contoh Soal',
            'slug' => 'contoh-soal',
            'judul' => 'Contoh Soal Bebras',
            'urutan' => 2,
        ]);

        // siaga-sd
        $sd = MenuSoal::create([
            'parent_id' => $contohSoal->id,
            'nama_menu' => 'Siaga Siswa SD',
            'slug' => 'siaga-sd',
            'judul' => 'Contoh Soal SIAGA untuk Siswa SD',
            'gambar' => 'img/b_countdown.jpg',
            'urutan' => 3,
        ]);
        $sdChal = SoalChallenge::create([
            'menu_soal_id' => $sd->id,
            'kategori_umur' => 'Kategori 11-12',
            'tingkat' => 'SD',
            'kesulitan' => 'Mudah',
            'kategori_materi' => 'ALG, STRUC',
            'judul' => 'Dress code untuk Berang-berang',
            'gambar_soal_1' => 'img/sd_1.jpg',
            'deskripsi_soal' => 'Berang-berang mempunyai sistem aturan berpakaian yang kompleks untuk menentukan penampilannya, yaitu kombinasi dari pakaian. Manfaatkan gambar yang diberikan untuk menentukan aturan berpakaian yang benar. <span class="font-semibold text-blue-700">Berang-berang yang mana yang tidak berpakaian sesuai aturan?</span>',
            'gambar_soal_2' => 'img/sd_2.png',
            'solusi' => '<span class="font-semibold text-red-600">Berang-berang kedua (jawaban B)</span> berpakaian tidak sesuai aturan. Dia seharusnya memakai <span class="text-blue-700 font-semibold">topi biru</span> and bukan topi merah.',
            'ini_informatika' => 'Soal ini adalah contoh penggunaan <span class="font-semibold">pohon keputusan</span> dan <span class="font-semibold">pengenalan pola</span>. Pohon keputusan berbentuk diagram bercabang untuk menggambarkan kemungkinan hasil dari sebuah aturan. Pada setiap simpul, Anda harus memilih cabang yang sesuai untuk mendapatkan hasil yang benar.'
        ]);
        $sdChal->options()->createMany([
            ['label' => 'A', 'gambar' => 'img/sd_a.png', 'urutan' => 1],
            ['label' => 'B', 'gambar' => 'img/sd_b.png', 'urutan' => 2],
            ['label' => 'C', 'gambar' => 'img/sd_c.png', 'urutan' => 3],
            ['label' => 'D', 'gambar' => 'img/sd_d.png', 'urutan' => 4],
        ]);

        // penggalang-smp
        $smp = MenuSoal::create([
            'parent_id' => $contohSoal->id,
            'nama_menu' => 'Penggalang Siswa SMP',
            'slug' => 'penggalang-smp',
            'judul' => 'Contoh Soal Penggalang untuk Siswa SMP',
            'gambar' => 'img/b_countdown.jpg',
            'urutan' => 4,
        ]);
        $smpChal = SoalChallenge::create([
            'menu_soal_id' => $smp->id,
            'kategori_umur' => 'Kategori Usia 15-16',
            'tingkat' => 'SMP',
            'kesulitan' => 'Menengah',
            'kategori_materi' => 'STRUC, DOC',
            'judul' => 'Teman',
            'gambar_soal_1' => 'img/smp_1.png',
            'deskripsi_soal' => 'Lucia dan teman-temannya terdaftar di sebuah jaringan media sosial, yang digambarkan sebagai “jaringan” berikut:',
            'gambar_soal_2' => 'img/smp_2.png',
            'solusi' => 'Struktur yang digunakan untuk menggambarkan relasi pertemanan dari Lucia disebut <span class="font-semibold text-blue-700">graf</span>. Node menyatakan orang. Edge (garis) menyatakan relasi teman. Graf sederhana sering dipakai untuk menggambarkan <span class="italic">jaringan sosial</span>.',
            'ini_informatika' => 'Mengelola akses ke informasi pribadi sangat penting saat ini. Ketika seseorang mengunggah foto pribadi ke Internet, ia harus berpikir hati-hati tentang siapa yang mungkin melihat gambar. Karena sangat sulit untuk benar-benar mengontrol siapa yang bisa melihat gambar, maka <span class="font-semibold text-red-600">yang terbaik adalah tidak pernah meng-upload gambar ke internet</span> kecuali gambar tersebut memang pantas dipajang di ruang publik, misalnya di sekolah atau halte bus. Program komputer dapat menganalisis graf untuk berbagai keperluan, contohnya: Menganalisis jaringan sosial, Aplikasi GPS, Mencari jalur terpendek antara dua tempat.'
        ]);
        $smpChal->options()->createMany([
            ['label' => 'A', 'teks' => 'Dana, Michael, Eve', 'urutan' => 1],
            ['label' => 'B', 'teks' => 'Dana, Eve, Monica', 'urutan' => 2],
            ['label' => 'C', 'teks' => 'Michael, Eve, Jacob', 'urutan' => 3],
            ['label' => 'D', 'teks' => 'Michael, Peter, Monica', 'urutan' => 4],
        ]);
        Setting::setByKey('smp_question_part_2', 'Sebuah garis berarti pertemanan antara dua orang. Contohnya Monica adalah teman Lucia tetapi Alex bukan teman Lucia. Aturan yang berlaku adalah: <ul class="list-disc pl-6 text-gray-700 space-y-1"><li>Jika seseorang berbagi foto dengan temannya, maka temannya itu dapat mengomentarinya.</li><li>Jika seseorang memberi komentar pada sebuah foto, maka semua teman-temannya dapat melihat komentar dan foto tersebut.</li></ul><p class="mt-4">Lucia mengunggah sebuah foto. Dengan siapa dia harus berbagi agar Jacob tidak dapat melihatnya?</p>');

        // penegak-sma
        $sma = MenuSoal::create([
            'parent_id' => $contohSoal->id,
            'nama_menu' => 'Penegak Siswa SMA',
            'slug' => 'penegak-sma',
            'judul' => 'Contoh Soal Penegak untuk Siswa SMA',
            'gambar' => 'img/b_countdown.jpg',
            'urutan' => 5,
        ]);
        $smaChal = SoalChallenge::create([
            'menu_soal_id' => $sma->id,
            'kategori_umur' => 'Kategori 17-18',
            'tingkat' => 'SMA',
            'kesulitan' => 'Menengah',
            'kategori_materi' => 'ALG, INF',
            'judul' => 'Lipatan Kertas',
            'gambar_soal_1' => 'img/sma_1.jpg',
            'deskripsi_soal' => 'Berang-berang mengembangkan suatu “bahasa” untuk melipat kertas. Bahasa ini dapat digunakan untuk menjelaskan bagaimana setiap lembaran kertas dapat dilipat dengan sisi-sisi lurus. Salah satu perintah dalam bahasa ini adalah fold. <p class="mt-4"><span class="font-semibold text-blue-700">e = folda(a,b) artinya</span> anda melipat sisi a selembar kertas agar menempel pada sisi b. Dengan perintah ini, Anda membuat sisi baru, yaitu sebuah garis yang membentuk lipatan, yang dinamakan <span class="font-bold">e</span>. Contoh:</p>',
            'gambar_soal_2' => 'img/sma_2.png',
            'solusi' => '<span class="font-semibold text-green-600">Jawaban yang benar adalah A.</span><br>Gambar berikut menjelaskan eksekusi pelipatan tahap demi tahap:<br><br><div class="flex justify-center"><img src="/img/sma_3.png" alt="Tahap Pelipatan" class="rounded-xl shadow-lg max-w-full sm:max-w-md bg-white p-1 sm:p-2"></div>',
            'ini_informatika' => 'Soal ini berkaitan dengan <span class="font-semibold text-blue-700">informatika</span>, yaitu konsep <span class="italic">fungsi</span> yang sangat penting dalam pemrograman. <ul class="space-y-3 sm:space-y-4 text-gray-700 text-sm sm:text-base mt-4"><li class="flex items-start gap-3 p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition"><span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold">1</span><span><span class="font-semibold">Fungsi</span> dipanggil melalui perintah → memulai serangkaian aktivitas.</span></li><li class="flex items-start gap-3 p-3 rounded-lg bg-green-50 hover:bg-green-100 transition"><span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-green-600 text-white text-xs font-bold">2</span><span><span class="font-semibold">Parameter</span> (contoh: dua sisi kertas) → menjadi input fungsi.</span></li><li class="flex items-start gap-3 p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition"><span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-purple-600 text-white text-xs font-bold">3</span><span><span class="font-semibold">Output</span> → hasil pemrosesan, yaitu bentuk lipatan.</span></li></ul><p class="mt-4">Dengan demikian, siswa belajar bahwa fungsi dalam pemrograman bekerja seperti “mesin”: menerima input, memproses, lalu menghasilkan output.</p>'
        ]);
        $smaChal->options()->createMany([
            ['label' => 'A', 'gambar' => 'img/sma_a.png', 'urutan' => 1],
            ['label' => 'B', 'gambar' => 'img/sma_b.png', 'urutan' => 2],
            ['label' => 'C', 'gambar' => 'img/sma_c.png', 'urutan' => 3],
            ['label' => 'D', 'gambar' => 'img/sma_d.png', 'urutan' => 4],
        ]);
        Setting::setByKey('sma_question_part_2', '<span class="font-semibold text-blue-700"> Harap dicatat bahwa</span> kertas ada di meja selama pelipatan, dan panjang sisi b adalah dua kali panjang sisi a. Bagaimana tampak bentuk kertas (a, b, c, d) setelah menjalankan ketiga perintah di atas? <p class="font-semibold mt-4">e = fold(c, a); f = fold(c, d); g = fold(a, f)</p>');

        // === 6. Latihan ===
        Latihan::create([
            'nama' => 'Olympia.id',
            'deskripsi' => 'Silakan unduh panduan latihan bagi akun baru.<br>Olympia powered by <span class="font-semibold">GDP Labs</span>.',
            'link' => 'https://olympia.id',
            'gambar' => 'latihan/gdp-logo.png' // path to store or static
        ]);
        Latihan::create([
            'nama' => 'latihan.bebras.or.id',
            'deskripsi' => 'Platform latihan resmi <span class="font-semibold">Bebras Indonesia</span>.',
            'link' => 'https://latihan.bebras.or.id',
            'gambar' => 'latihan/logo.jpg'
        ]);
        // Copy the physical assets to public storage path for latihan so they display via storage/ url
        // We will do this in the script later or directly use public path for seeds to be safe
        // Let's seed actual public folder paths or copy files to storage/latihan/
        $frontendImgPath = base_path('../bebras_frontend/public/img');
        @mkdir(storage_path('app/public/latihan'), 0777, true);
        @copy($frontendImgPath . '/gdp-logo.png', storage_path('app/public/latihan/gdp-logo.png'));
        @copy($frontendImgPath . '/logo.jpg', storage_path('app/public/latihan/logo.jpg'));

        // === 7. Kontak ===
        $k1 = Kontak::create([
            'nama' => 'Dr. Inggriani Liem',
            'institusi' => 'STEI Institut Teknologi Bandung',
            'alamat' => 'Jl. Ganesha 10, Bandung 40135, Indonesia'
        ]);
        $k1->details()->createMany([
            ['tipe' => 'email', 'nilai' => 'iliem@stei.itb.ac.id'],
            ['tipe' => 'url', 'nilai' => 'https://stei.itb.ac.id'],
        ]);

        $k2 = Kontak::create([
            'nama' => 'Dr. Suryana Setiawan',
            'institusi' => 'Fasilkom Universitas Indonesia',
            'alamat' => 'Kampus UI, Depok 16424, Indonesia'
        ]);
        $k2->details()->createMany([
            ['tipe' => 'email', 'nilai' => 'suryana@cs.ui.ac.id'],
            ['tipe' => 'url', 'nilai' => 'https://cs.ui.ac.id'],
        ]);
        $k3 = Kontak::create([
            'nama' => 'Bebras Biro',
            'institusi' => '-',
            'alamat' => '-'
        ]);

        $k3->details()->createMany([
            ['tipe' => 'email', 'nilai' => 'mail@bebras.or.id'],
            ['tipe' => 'url', 'nilai' => 'http://bebras.or.id'],
        ]);

        // === 8. Menu Kegiatan (Hierarchical Dropdown) ===
        $m1 = MenuKegiatan::create([
            'nama_menu' => 'Workshop',
            'slug' => 'workshop',
            'judul' => 'Workshop Bebras Indonesia',
            'urutan' => 1,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m1->id,
            'nama_menu' => '2017',
            'slug' => 'workshop-2017',
            'judul' => 'Workshop 2017',
            'body' => '<p>Kegiatan Workshop Bebras 2017 diselenggarakan di berbagai kota di Indonesia untuk melatih guru-guru memperkenalkan konsep berpikir komputasi.</p>',
            'urutan' => 1,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m1->id,
            'nama_menu' => '2016',
            'slug' => 'workshop-2016',
            'judul' => 'Workshop 2016',
            'body' => '<p>Awal mula inisiasi sosialisasi bebras dan lokakarya computational thinking pada tahun 2016.</p>',
            'urutan' => 2,
        ]);

        $m2 = MenuKegiatan::create([
            'nama_menu' => 'Bebras Challenge',
            'slug' => 'bebras-challenge',
            'judul' => 'Tantangan Bebras Indonesia',
            'urutan' => 2,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m2->id,
            'nama_menu' => '2024',
            'slug' => 'challenge-2024',
            'judul' => 'Bebras Challenge 2024',
            'body' => '<p>Informasi pelaksanaan dan panduan Bebras Indonesia Challenge 2024.</p>',
            'urutan' => 1,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m2->id,
            'nama_menu' => '2023',
            'slug' => 'challenge-2023',
            'judul' => 'Bebras Challenge 2023',
            'body' => '<p>Dokumentasi tantangan Bebras Indonesia Challenge 2023.</p>',
            'urutan' => 2,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m2->id,
            'nama_menu' => '2022',
            'slug' => 'challenge-2022',
            'judul' => 'Bebras Challenge 2022',
            'body' => '<p>Dokumentasi tantangan Bebras Indonesia Challenge 2022.</p>',
            'urutan' => 3,
        ]);

        MenuKegiatan::create([
            'nama_menu' => 'Statistik Bebras Indonesia Challenge',
            'slug' => 'statistik-bebras-indonesia-challenge',
            'judul' => 'Statistik Bebras Indonesia Challenge',
            'body' => '<p>Berikut statistik sebaran peserta dan sekolah yang berpartisipasi dalam Bebras Challenge nasional.</p>',
            'urutan' => 3,
        ]);

        $m4 = MenuKegiatan::create([
            'nama_menu' => 'Pengumuman Hasil',
            'slug' => 'pengumuman-hasil',
            'judul' => 'Pengumuman Hasil',
            'urutan' => 4,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m4->id,
            'nama_menu' => '2024',
            'slug' => 'pengumuman-2024',
            'judul' => 'Pengumuman Hasil 2024',
            'body' => '<p>Daftar pemenang dan statistik perolehan skor peserta Bebras Challenge 2024.</p>',
            'urutan' => 1,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m4->id,
            'nama_menu' => '2023',
            'slug' => 'pengumuman-2023',
            'judul' => 'Pengumuman Hasil 2023',
            'body' => '<p>Daftar pemenang dan statistik perolehan skor peserta Bebras Challenge 2023.</p>',
            'urutan' => 2,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m4->id,
            'nama_menu' => '2022',
            'slug' => 'pengumuman-2022',
            'judul' => 'Pengumuman Hasil 2022',
            'body' => '<p>Daftar pemenang dan statistik perolehan skor peserta Bebras Challenge 2022.</p>',
            'urutan' => 3,
        ]);

        $m5 = MenuKegiatan::create([
            'nama_menu' => 'CT Challenge 2023 For Teachers',
            'slug' => 'ct-challenge-2023-for-teachers',
            'judul' => 'CT Challenge 2023 For Teachers',
            'urutan' => 5,
        ]);
        MenuKegiatan::create([
            'parent_id' => $m5->id,
            'nama_menu' => 'Pengumuman Hasil',
            'slug' => 'ct-challenge-pengumuman',
            'judul' => 'Pengumuman Hasil CT Challenge 2023 For Teachers',
            'body' => '<p>Daftar pemenang penghargaan nasional CT Challenge 2023 kategori Guru.</p>',
            'urutan' => 1,
        ]);

        MenuKegiatan::create([
            'nama_menu' => 'Gerakan Pandai',
            'slug'      => 'gerakan-pandai',
            'url'       => 'https://pandai.bebras.or.id/',
            'urutan'    => 6,
        ]);

        // Link existing workshop_2017 kegiatans to menu_kegiatan slug='workshop-2017'
        $menuWorkshop2017 = MenuKegiatan::where('slug', 'workshop-2017')->first();
        if ($menuWorkshop2017) {
            Kegiatan::where('tipe', 'workshop_2017')
                ->update(['menu_kegiatan_id' => $menuWorkshop2017->id]);
        }
    }
}
