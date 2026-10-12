@extends('app')

@section('title', 'Beranda - Bebras Indonesia')

@section('content')
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Carousel -->
        <div id="default-carousel" class="relative" data-carousel="static">
            {{-- Wrapper: tinggi otomatis via aspect-ratio CSS sesuai dimensi asli gambar 1920×770 --}}
            <div class="carousel-wrapper overflow-hidden relative rounded-lg m-2">
                @foreach ($banners as $index => $banner)
                <!-- Item {{ $index + 1 }} -->
                <div class="carousel-item duration-700 ease-in-out absolute inset-0 transition-all transform {{ $index === 0 ? 'opacity-1 z-10' : 'opacity-0' }}"
                    data-carousel-item>
                    <img src="{{ (strpos($banner->gambar, 'img/') === 0) ? asset($banner->gambar) : asset('storage/' . $banner->gambar) }}"
                        class="block w-full h-full object-contain bg-white"
                        alt="{{ $banner->judul }}">
                    <div class="absolute inset-0 flex items-end justify-center pb-8">
                        <div class="text-center text-white px-4 bg-black/40 rounded-lg py-3 mx-4 w-full max-w-2xl">
                            <h2 class="text-lg sm:text-2xl md:text-3xl font-bold mb-1">{{ $banner->judul }}</h2>
                            <p class="text-sm sm:text-base max-w-2xl">{{ $banner->deskripsi }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Slider indicators (dipindah ke dalam wrapper agar tdk overflow) -->
                <div class="flex absolute bottom-4 left-1/2 z-30 space-x-3 -translate-x-1/2">
                    @foreach ($banners as $index => $banner)
                    <button type="button" class="w-3 h-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" aria-label="Slide {{ $index + 1 }}"
                        data-carousel-slide-to="{{ $index }}"></button>
                    @endforeach
                </div>

                <!-- Slider controls (dipindah ke dalam wrapper agar absolute relative ke wrapper) -->
                <button type="button"
                    class="flex absolute top-0 left-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none"
                    data-carousel-prev>
                    <span
                        class="inline-flex justify-center items-center w-8 h-8 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white">
                        <i class="fas fa-chevron-left text-white"></i>
                        <span class="sr-only">Previous</span>
                    </span>
                </button>
                <button type="button"
                    class="flex absolute top-0 right-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none"
                    data-carousel-next>
                    <span
                        class="inline-flex justify-center items-center w-8 h-8 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white">
                        <i class="fas fa-chevron-right text-white"></i>
                        <span class="sr-only">Next</span>
                    </span>
                </button>
            </div>
        </div>

    </div>

    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex flex-row justify-between">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 flex items-center">
                            <div class="h-1 w-24 bg-bebrasBlue mr-3"></div>
                            Tentang Bebras
                        </h2>
                        <div class=" items-start mid:w-1/5 ms-5">
                            <img src="{{ (strpos($aboutLogo, 'img/') === 0) ? asset($aboutLogo) : asset('storage/' . $aboutLogo) }}" alt="" class="w-20 h-20">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row">
                        <div class="w-full md:w-4/5 pr-0 md:pr-8">
                            {!! $aboutContent !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-bebrasLightBlue rounded-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-12">Kegiatan Bebras Indonesia?</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 place-items-center">
                @foreach ($kegiatans as $kegiatan)
                <div class="bg-white w-80 p-6 rounded-xl shadow-md text-center card-hover">
                    <div class="w-full h-48 mb-4 overflow-hidden rounded-lg">
                        <img src="{{ (strpos($kegiatan->gambar, 'img/') === 0) ? asset($kegiatan->gambar) : asset('storage/' . $kegiatan->gambar) }}" alt="{{ $kegiatan->judul }}"
                            class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $kegiatan->judul }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ $kegiatan->deskripsi }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                    <div class="h-1 w-24 bg-bebrasBlue mr-3"></div>
                    Berita
                </h2>
                <a href="{{ route('berita') }}"
                    class="text-sm font-semibold text-bebrasBlue hover:text-[#F97A00] inline-flex items-center">
                    Lihat semua <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            @if ($beritas->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($beritas as $berita)
                        @php
                            $tglFormatted = null;
                            if ($berita->tanggal_lokasi) {
                                try {
                                    $tglFormatted = \Carbon\Carbon::parse($berita->tanggal_lokasi)->translatedFormat('d F Y');
                                } catch (\Exception $e) {
                                    $tglFormatted = $berita->tanggal_lokasi;
                                }
                            }

                            $gambarUrl = null;
                            if ($berita->gambar) {
                                $gambarUrl = str_starts_with($berita->gambar, 'img/')
                                    ? asset($berita->gambar)
                                    : asset('storage/' . $berita->gambar);
                            }

                            $beritaData = json_encode([
                                'judul' => $berita->judul,
                                'gambar' => $gambarUrl,
                                'kota' => $berita->kota,
                                'tanggal' => $tglFormatted,
                                'deskripsi' => $berita->deskripsi,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <article class="bg-white rounded-xl shadow-md overflow-hidden card-hover flex flex-col">
                            <div class="h-48 overflow-hidden">
                                @if ($gambarUrl)
                                    <img src="{{ $gambarUrl }}" alt="{{ $berita->judul }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-image text-3xl text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $berita->judul }}</h3>
                                <div class="space-y-1 mb-3">
                                    @if ($tglFormatted)
                                        <p class="text-gray-500 text-xs flex items-center gap-1 font-medium">
                                            <span>📅</span> {{ $tglFormatted }}
                                        </p>
                                    @endif
                                    @if ($berita->kota)
                                        <p class="text-gray-500 text-xs flex items-center gap-1">
                                            <span>📍</span> {{ $berita->kota }}
                                        </p>
                                    @endif
                                </div>
                                <p class="text-gray-600 text-sm">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($berita->deskripsi), 160) }}
                                </p>
                                <a href="{{ route('berita') }}"
                                    onclick='event.preventDefault(); openBeritaModal({!! $beritaData !!})'
                                    class="mt-auto pt-4 text-xs font-semibold text-bebrasDarkBlue hover:text-bebrasBlue inline-flex items-center gap-1">
                                    Lihat selengkapnya <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-600">Belum ada berita.</p>
            @endif
        </div>
    </section>

    <div id="beritaModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        onclick="closeBeritaModal()">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden my-8" onclick="event.stopPropagation()">
            <div class="relative bg-gradient-to-r from-bebrasDarkBlue to-bebrasBlue p-6 text-white">
                <button type="button" onclick="closeBeritaModal()"
                    class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
                <span id="beritaModalKota" class="inline-block text-xs font-semibold uppercase bg-white/20 px-2.5 py-1 rounded-full mb-2 hidden"></span>
                <h3 id="beritaModalJudul" class="text-xl md:text-2xl font-bold leading-tight pr-8"></h3>
            </div>
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <img id="beritaModalGambar" src="" alt="" class="w-full max-h-80 object-cover rounded-xl shadow mb-4 hidden">
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4 pb-4 border-b">
                    <span id="beritaModalTanggal" class="hidden items-center gap-1.5 font-medium"></span>
                </div>
                <div id="beritaModalDeskripsi" class="prose max-w-none text-gray-700 leading-relaxed text-sm md:text-base"></div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button type="button" onclick="closeBeritaModal()"
                    class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row items-center p-6 mt-4 rounded-md">

        <div class="w-full md:w-4/4 text-center md:text-left md:pl-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $ctaTitle }}</h1>
            <p class="mt-2 text-lg text-gray-600">{{ $ctaDescription }}</p>
            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-3">
                <a href="{{ $ctaLink }}"
                    class="border border-bebrasBlue text-bebrasBlue px-5 py-2 rounded-full text-sm font-semibold inline-flex items-center hover:text-[#F97A00] hover:border-[#F97A00] active:scale-95 active:text-[#F97A00] active:border-[#F97A00]">
                    <span>Info Lengkap</span>
                    <i class="fas fa-info-circle ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/script.js') }}"></script>
        <script>
            function openBeritaModal(data) {
                document.getElementById('beritaModalJudul').innerText = data.judul || '';
                document.getElementById('beritaModalDeskripsi').innerHTML = data.deskripsi || '';

                const kota = document.getElementById('beritaModalKota');
                kota.innerText = data.kota || '';
                kota.classList.toggle('hidden', !data.kota);

                const tanggal = document.getElementById('beritaModalTanggal');
                tanggal.innerHTML = data.tanggal ? '<span>📅</span> ' + data.tanggal : '';
                tanggal.classList.toggle('hidden', !data.tanggal);
                tanggal.classList.toggle('flex', Boolean(data.tanggal));

                const gambar = document.getElementById('beritaModalGambar');
                gambar.src = data.gambar || '';
                gambar.alt = data.judul || '';
                gambar.classList.toggle('hidden', !data.gambar);

                document.getElementById('beritaModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeBeritaModal() {
                document.getElementById('beritaModal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeBeritaModal();
                }
            });
        </script>
    @endpush

@endsection
