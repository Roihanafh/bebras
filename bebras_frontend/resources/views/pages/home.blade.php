@extends('app')

@section('title', 'Beranda - Bebras Indonesia')

@section('content')
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Carousel -->
        <div id="default-carousel" class="relative" data-carousel="static">
            {{-- Wrapper: tinggi otomatis via aspect-ratio CSS sesuai dimensi asli gambar 1920Ã—770 --}}
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

    <section class="py-12 rounded-lg overflow-hidden relative" style="background-color: #E6F7FF;">
        {{-- Decorative background: hex-dot grid + arc + diagonal lines + plus signs --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            {{-- Hex dot grid --}}
            <svg class="absolute inset-0 w-full h-full opacity-[0.06]" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="kegiatan-hex" x="0" y="0" width="40" height="46" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="8"  r="2.5" fill="#0099CC"/>
                        <circle cx="0"  cy="23" r="2.5" fill="#0099CC"/>
                        <circle cx="40" cy="23" r="2.5" fill="#0099CC"/>
                        <circle cx="20" cy="38" r="2.5" fill="#0099CC"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#kegiatan-hex)"/>
            </svg>
            {{-- Large arc top-right --}}
            <svg class="absolute -top-10 -right-10 opacity-10 w-56" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="200" cy="0" r="140" stroke="#00CAFF" stroke-width="18" fill="none"/>
            </svg>
            {{-- Small arc bottom-left --}}
            <svg class="absolute -bottom-8 -left-8 opacity-10 w-40" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="0" cy="200" r="110" stroke="#0099CC" stroke-width="14" fill="none"/>
            </svg>
            {{-- Diagonal hatching top-left --}}
            <svg class="absolute top-0 left-0 opacity-[0.06] w-48 h-48" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <line x1="0"   y1="40"  x2="40"  y2="0"   stroke="#0099CC" stroke-width="1.5"/>
                <line x1="0"   y1="80"  x2="80"  y2="0"   stroke="#0099CC" stroke-width="1.5"/>
                <line x1="0"   y1="120" x2="120" y2="0"   stroke="#0099CC" stroke-width="1.5"/>
                <line x1="0"   y1="160" x2="160" y2="0"   stroke="#0099CC" stroke-width="1.5"/>
                <line x1="0"   y1="200" x2="200" y2="0"   stroke="#0099CC" stroke-width="1.5"/>
                <line x1="40"  y1="200" x2="200" y2="40"  stroke="#0099CC" stroke-width="1.5"/>
                <line x1="80"  y1="200" x2="200" y2="80"  stroke="#0099CC" stroke-width="1.5"/>
            </svg>
            {{-- Plus signs cluster bottom-right --}}
            <svg class="absolute bottom-6 right-8 opacity-[0.15] w-28" viewBox="0 0 120 60" xmlns="http://www.w3.org/2000/svg">
                <line x1="10" y1="6"   x2="10" y2="18"  stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="4"  y1="12"  x2="16" y2="12"  stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="40" y1="6"   x2="40" y2="18"  stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="34" y1="12"  x2="46" y2="12"  stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="70" y1="6"   x2="70" y2="18"  stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="64" y1="12"  x2="76" y2="12"  stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="100" y1="6"  x2="100" y2="18" stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="94"  y1="12" x2="106" y2="12" stroke="#00CAFF" stroke-width="2" stroke-linecap="round"/>
                <line x1="25" y1="38"  x2="25" y2="50"  stroke="#0099CC" stroke-width="2" stroke-linecap="round"/>
                <line x1="19" y1="44"  x2="31" y2="44"  stroke="#0099CC" stroke-width="2" stroke-linecap="round"/>
                <line x1="55" y1="38"  x2="55" y2="50"  stroke="#0099CC" stroke-width="2" stroke-linecap="round"/>
                <line x1="49" y1="44"  x2="61" y2="44"  stroke="#0099CC" stroke-width="2" stroke-linecap="round"/>
                <line x1="85" y1="38"  x2="85" y2="50"  stroke="#0099CC" stroke-width="2" stroke-linecap="round"/>
                <line x1="79" y1="44"  x2="91" y2="44"  stroke="#0099CC" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

    @if ($pengumumans->isNotEmpty())
        <section class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                        <div class="h-1 w-24 bg-bebrasBlue mr-3"></div>
                        Pengumuman
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($pengumumans as $pengumuman)
                        @php
                            $pengumumanTanggal = $pengumuman->tanggal_lokasi
                                ? \Carbon\Carbon::parse($pengumuman->tanggal_lokasi)->translatedFormat('d F Y')
                                : null;
                            $pengumumanGambar = $pengumuman->gambar
                                ? (str_starts_with($pengumuman->gambar, 'img/')
                                    ? asset($pengumuman->gambar)
                                    : asset('storage/' . $pengumuman->gambar))
                                : null;

                            $pengumumanGaleri = [];
                            if (isset($pengumuman->gambarBerita) && $pengumuman->gambarBerita->isNotEmpty()) {
                                foreach ($pengumuman->gambarBerita as $gb) {
                                    $pengumumanGaleri[] = str_starts_with($gb->gambar, 'img/')
                                        ? asset($gb->gambar)
                                        : asset('storage/' . $gb->gambar);
                                }
                            }
                            if (empty($pengumumanGaleri) && $pengumumanGambar) {
                                $pengumumanGaleri = [$pengumumanGambar];
                            }

                            $pengumumanData = json_encode([
                                'id' => $pengumuman->id,
                                'judul' => $pengumuman->judul,
                                'gambar' => $pengumumanGambar,
                                'galeri' => $pengumumanGaleri,
                                'kota' => $pengumuman->kota,
                                'tanggal' => $pengumumanTanggal,
                                'deskripsi' => $pengumuman->deskripsi,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <article class="bg-white rounded-xl shadow-md overflow-hidden card-hover flex flex-col">
                            <div class="h-48 overflow-hidden">
                                @if ($pengumumanGambar)
                                    <img src="{{ $pengumumanGambar }}" alt="{{ $pengumuman->judul }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-blue-50 flex items-center justify-center">
                                        <i class="fas fa-bell text-3xl text-bebrasBlue"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $pengumuman->judul }}</h3>
                                @if ($pengumumanTanggal)
                                    <p class="text-gray-500 text-xs mb-3">{{ $pengumumanTanggal }}</p>
                                @endif
                                <p class="text-gray-600 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($pengumuman->deskripsi), 160) }}</p>
                                <button type="button" onclick='openPengumumanModal({!! $pengumumanData !!})'
                                    class="mt-auto pt-4 text-left text-xs font-semibold text-bebrasDarkBlue hover:text-bebrasBlue inline-flex items-center gap-1">
                                    Lihat selengkapnya <i class="fas fa-arrow-right text-[10px]"></i>
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

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

                            $beritaGaleri = [];
                            if (isset($berita->gambarBerita) && $berita->gambarBerita->isNotEmpty()) {
                                foreach ($berita->gambarBerita as $gb) {
                                    $beritaGaleri[] = str_starts_with($gb->gambar, 'img/')
                                        ? asset($gb->gambar)
                                        : asset('storage/' . $gb->gambar);
                                }
                            }
                            if (empty($beritaGaleri) && $gambarUrl) {
                                $beritaGaleri = [$gambarUrl];
                            }

                            $beritaData = json_encode([
                                'judul' => $berita->judul,
                                'gambar' => $gambarUrl,
                                'galeri' => $beritaGaleri,
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
                    class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center" aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
                <span id="beritaModalKota" class="inline-block text-xs font-semibold uppercase bg-white/20 px-2.5 py-1 rounded-full mb-2 hidden"></span>
                <h3 id="beritaModalJudul" class="text-xl md:text-2xl font-bold leading-tight pr-8"></h3>
            </div>
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4 pb-4 border-b">
                    <span id="beritaModalTanggal" class="hidden items-center gap-1.5 font-medium"></span>
                </div>

                {{-- CAROUSEL GALERI BERITA (ANTI-STRETCH & AUTOPLAY) --}}
                <div id="beritaModalCarouselContainer" class="mb-6 hidden">
                    <div id="beritaCarouselWrapper" class="relative w-full h-64 sm:h-72 md:h-80 overflow-hidden rounded-xl bg-slate-950 flex items-center justify-center shadow-inner group">
                        <img id="beritaCarouselBgImg" src="" alt="" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-30 scale-110 pointer-events-none select-none">
                        <img id="beritaCarouselMainImg" src="" alt="" class="relative z-10 max-h-full max-w-full w-auto h-auto object-contain mx-auto transition-opacity duration-300 drop-shadow-md select-none">

                        <button type="button" id="beritaCarouselPrevBtn" onclick="beritaCarouselController && beritaCarouselController.prev()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-sm transition focus:outline-none"
                                aria-label="Foto Sebelumnya">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>

                        <button type="button" id="beritaCarouselNextBtn" onclick="beritaCarouselController && beritaCarouselController.next()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-sm transition focus:outline-none"
                                aria-label="Foto Selanjutnya">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>

                        <div id="beritaCarouselBadge" class="absolute top-3 right-3 z-20 px-2.5 py-1 rounded-full bg-black/60 text-white text-xs font-semibold backdrop-blur-sm select-none">
                            1 / 1
                        </div>

                        <div id="beritaCarouselDots" class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-1.5 pointer-events-auto"></div>
                    </div>
                </div>

                <div id="beritaModalDeskripsi" class="tiny-content prose max-w-none text-gray-700 leading-relaxed text-sm md:text-base"></div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button type="button" onclick="closeBeritaModal()"
                    class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div id="pengumumanModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto bg-black/60 backdrop-blur-sm items-center justify-center p-4"
        onclick="closePengumumanModal()">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden my-8" onclick="event.stopPropagation()">
            <div class="relative bg-gradient-to-r from-bebrasDarkBlue to-bebrasBlue p-6 text-white">
                <button type="button" onclick="closePengumumanModal()"
                    class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center" aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
                <span class="inline-block text-xs font-semibold uppercase bg-white/20 px-2.5 py-1 rounded-full mb-2">Pengumuman</span>
                <h3 id="pengumumanModalJudul" class="text-xl md:text-2xl font-bold leading-tight pr-8"></h3>
            </div>
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4 pb-4 border-b">
                    <span id="pengumumanModalTanggal" class="hidden items-center gap-1.5 font-medium"></span>
                    <span id="pengumumanModalKota" class="hidden items-center gap-1.5"></span>
                </div>

                {{-- CAROUSEL GALERI PENGUMUMAN (ANTI-STRETCH & AUTOPLAY) --}}
                <div id="pengumumanModalCarouselContainer" class="mb-6 hidden">
                    <div id="pengumumanCarouselWrapper" class="relative w-full h-64 sm:h-72 md:h-80 overflow-hidden rounded-xl bg-slate-950 flex items-center justify-center shadow-inner group">
                        <img id="pengumumanCarouselBgImg" src="" alt="" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-30 scale-110 pointer-events-none select-none">
                        <img id="pengumumanCarouselMainImg" src="" alt="" class="relative z-10 max-h-full max-w-full w-auto h-auto object-contain mx-auto transition-opacity duration-300 drop-shadow-md select-none">

                        <button type="button" id="pengumumanCarouselPrevBtn" onclick="pengumumanCarouselController && pengumumanCarouselController.prev()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-sm transition focus:outline-none"
                                aria-label="Foto Sebelumnya">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>

                        <button type="button" id="pengumumanCarouselNextBtn" onclick="pengumumanCarouselController && pengumumanCarouselController.next()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-sm transition focus:outline-none"
                                aria-label="Foto Selanjutnya">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>

                        <div id="pengumumanCarouselBadge" class="absolute top-3 right-3 z-20 px-2.5 py-1 rounded-full bg-black/60 text-white text-xs font-semibold backdrop-blur-sm select-none">
                            1 / 1
                        </div>

                        <div id="pengumumanCarouselDots" class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-1.5 pointer-events-auto"></div>
                    </div>
                </div>

                <div id="pengumumanModalDeskripsi" class="tiny-content prose max-w-none text-gray-700 leading-relaxed text-sm md:text-base"></div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button type="button" onclick="closePengumumanModal()"
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
    {{-- ===== SECTION MITRA KAMI ===== --}}
    @if($mitras->isNotEmpty())
    <section class="py-14 bg-white rounded-xl">
        <div class="max-w-6xl mx-auto px-10 sm:px-14">

            {{-- Section Header: mascot image + bold title, centered --}}
            <div class="flex flex-col items-center mb-3">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <img src="{{ asset('img/logo.jpg') }}"
                         alt="Logo Bebras Indonesia"
                         class="w-12 h-12 rounded-md object-cover"
                         loading="lazy">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 tracking-tight">
                        Mitra Kami
                    </h2>
                </div>
                <p class="text-gray-500 text-sm">
                    Kolaborasi bersama untuk masa depan generasi digital Indonesia.
                </p>
            </div>

            {{-- Spacer --}}
            <div class="mt-10"></div>

            {{-- Mitra row: left arrow | items with vertical dividers | right arrow --}}
            <div class="flex items-center gap-2 sm:gap-4">

                {{-- Left nav arrow --}}
                <button type="button" id="mitraScrollLeft"
                        class="flex-shrink-0 w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm
                               flex items-center justify-center text-slate-500
                               hover:bg-slate-50 hover:text-[#00CAFF] hover:border-[#00CAFF]
                               transition-all duration-200 focus:outline-none"
                        aria-label="Geser kiri">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>

                {{-- Scrollable mitra track --}}
                <div id="mitraTrack"
                     class="flex-1 overflow-x-auto scrollbar-hide"
                     style="scrollbar-width: none; -ms-overflow-style: none;">
                    <div class="flex items-stretch min-w-max mx-auto" id="mitraList">
                        @foreach ($mitras as $index => $mitra)

                        {{-- Vertical divider before every item except the first --}}
                        @if($index > 0)
                        <div class="self-stretch flex items-center" aria-hidden="true">
                            <div class="w-px h-20 bg-slate-200/80 my-auto"></div>
                        </div>
                        @endif

                        {{-- Mitra item --}}
                        <div class="group flex flex-col items-center justify-center gap-4 px-10 py-2
                                    transition-all duration-300 cursor-default">

                            {{-- Circular badge --}}
                            <div class="flex-shrink-0
                                        w-[120px] h-[120px] rounded-full
                                        bg-white border-2 border-slate-200 shadow-sm
                                        flex items-center justify-center p-4
                                        transition-all duration-300 ease-in-out
                                        group-hover:scale-110
                                        group-hover:border-[#00C3FF]
                                        group-hover:ring-[5px] group-hover:ring-[#00C3FF]/20
                                        group-hover:shadow-md">

                                @if($mitra->logo)
                                    <img src="{{ asset('storage/' . $mitra->logo) }}"
                                         alt="{{ $mitra->nama }}"
                                         class="max-w-full max-h-full object-contain block">
                                @else
                                    {{-- Placeholder: neutral clean initials --}}
                                    <div class="w-full h-full rounded-full flex items-center justify-center bg-gray-200">
                                        <span class="text-slate-600 font-bold text-xl leading-none select-none">
                                            {{ strtoupper(substr(trim($mitra->nama), 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Mitra name --}}
                            <p class="text-sm text-center font-medium text-slate-700 leading-snug
                                      transition-colors duration-300
                                      group-hover:text-[#00CAFF] group-hover:font-semibold
                                      max-w-[130px]">
                                {{ $mitra->nama }}
                            </p>
                        </div>

                        @endforeach
                    </div>
                </div>

                {{-- Right nav arrow --}}
                <button type="button" id="mitraScrollRight"
                        class="flex-shrink-0 w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm
                               flex items-center justify-center text-slate-500
                               hover:bg-slate-50 hover:text-[#00CAFF] hover:border-[#00CAFF]
                               transition-all duration-200 focus:outline-none"
                        aria-label="Geser kanan">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
            </div>

        </div>
    </section>

    {{-- Mitra scroll script --}}
    <style>
        #mitraTrack::-webkit-scrollbar { display: none; }
    </style>
    <script>
        (function () {
            var track = document.getElementById('mitraTrack');
            document.getElementById('mitraScrollLeft').addEventListener('click', function () {
                track.scrollBy({ left: -200, behavior: 'smooth' });
            });
            document.getElementById('mitraScrollRight').addEventListener('click', function () {
                track.scrollBy({ left: 200, behavior: 'smooth' });
            });
        })();
    </script>
    @endif
    {{-- ===== END SECTION MITRA KAMI ===== --}}

    @push('scripts')
        <script src="{{ asset('js/script.js') }}"></script>
        <script>
            // Helper Carousel Controller dengan Autoplay
            function createModalCarousel(config) {
                let images = [];
                let currentIndex = 0;
                let autoplayTimer = null;
                const intervalMs = config.interval || 3500;

                function render() {
                    if (!images || images.length === 0) return;
                    const currentUrl = images[currentIndex];

                    config.mainImg.style.opacity = '0.3';
                    config.bgImg.src = currentUrl;
                    config.mainImg.src = currentUrl;
                    setTimeout(() => { config.mainImg.style.opacity = '1'; }, 50);

                    if (config.badge) {
                        config.badge.innerText = (currentIndex + 1) + ' / ' + images.length;
                    }

                    if (config.dots) {
                        config.dots.innerHTML = '';
                        images.forEach((_, idx) => {
                            const dot = document.createElement('button');
                            dot.type = 'button';
                            dot.className = 'h-2 rounded-full transition-all ' +
                                (idx === currentIndex ? 'bg-white w-5' : 'bg-white/50 w-2 hover:bg-white/80');
                            dot.onclick = () => {
                                currentIndex = idx;
                                render();
                                resetAutoplay();
                            };
                            config.dots.appendChild(dot);
                        });
                    }
                }

                function next() {
                    if (images.length <= 1) return;
                    currentIndex = (currentIndex + 1) % images.length;
                    render();
                }

                function prev() {
                    if (images.length <= 1) return;
                    currentIndex = (currentIndex - 1 + images.length) % images.length;
                    render();
                }

                function startAutoplay() {
                    stopAutoplay();
                    if (images.length > 1) {
                        autoplayTimer = setInterval(() => {
                            next();
                        }, intervalMs);
                    }
                }

                function stopAutoplay() {
                    if (autoplayTimer) {
                        clearInterval(autoplayTimer);
                        autoplayTimer = null;
                    }
                }

                function resetAutoplay() {
                    stopAutoplay();
                    startAutoplay();
                }

                if (config.wrapper) {
                    config.wrapper.addEventListener('mouseenter', stopAutoplay);
                    config.wrapper.addEventListener('mouseleave', startAutoplay);
                }

                function init(imgs) {
                    images = imgs || [];
                    currentIndex = 0;
                    stopAutoplay();

                    if (!images || images.length === 0) {
                        config.container.classList.add('hidden');
                        return;
                    }

                    config.container.classList.remove('hidden');

                    if (images.length === 1) {
                        if (config.prevBtn) config.prevBtn.classList.add('hidden');
                        if (config.nextBtn) config.nextBtn.classList.add('hidden');
                        if (config.badge) config.badge.classList.add('hidden');
                        if (config.dots) config.dots.classList.add('hidden');
                    } else {
                        if (config.prevBtn) config.prevBtn.classList.remove('hidden');
                        if (config.nextBtn) config.nextBtn.classList.remove('hidden');
                        if (config.badge) config.badge.classList.remove('hidden');
                        if (config.dots) config.dots.classList.remove('hidden');
                        startAutoplay();
                    }

                    render();
                }

                return {
                    init,
                    next: () => { next(); resetAutoplay(); },
                    prev: () => { prev(); resetAutoplay(); },
                    stop: stopAutoplay
                };
            }

            let beritaCarouselController = null;
            let pengumumanCarouselController = null;

            document.addEventListener('DOMContentLoaded', function () {
                beritaCarouselController = createModalCarousel({
                    container: document.getElementById('beritaModalCarouselContainer'),
                    wrapper: document.getElementById('beritaCarouselWrapper'),
                    bgImg: document.getElementById('beritaCarouselBgImg'),
                    mainImg: document.getElementById('beritaCarouselMainImg'),
                    prevBtn: document.getElementById('beritaCarouselPrevBtn'),
                    nextBtn: document.getElementById('beritaCarouselNextBtn'),
                    badge: document.getElementById('beritaCarouselBadge'),
                    dots: document.getElementById('beritaCarouselDots'),
                    interval: 3500
                });

                pengumumanCarouselController = createModalCarousel({
                    container: document.getElementById('pengumumanModalCarouselContainer'),
                    wrapper: document.getElementById('pengumumanCarouselWrapper'),
                    bgImg: document.getElementById('pengumumanCarouselBgImg'),
                    mainImg: document.getElementById('pengumumanCarouselMainImg'),
                    prevBtn: document.getElementById('pengumumanCarouselPrevBtn'),
                    nextBtn: document.getElementById('pengumumanCarouselNextBtn'),
                    badge: document.getElementById('pengumumanCarouselBadge'),
                    dots: document.getElementById('pengumumanCarouselDots'),
                    interval: 3500
                });
            });

            function openPengumumanModal(data) {
                document.getElementById('pengumumanModalJudul').innerText = data.judul || '';
                document.getElementById('pengumumanModalDeskripsi').innerHTML = data.deskripsi || '';

                const tanggal = document.getElementById('pengumumanModalTanggal');
                tanggal.innerHTML = data.tanggal ? '<span>📅</span> ' + data.tanggal : '';
                tanggal.classList.toggle('hidden', !data.tanggal);
                tanggal.classList.toggle('flex', Boolean(data.tanggal));

                const kota = document.getElementById('pengumumanModalKota');
                kota.innerHTML = data.kota ? '<span>📍</span> ' + data.kota : '';
                kota.classList.toggle('hidden', !data.kota);
                kota.classList.toggle('flex', Boolean(data.kota));

                const galeri = (data.galeri && data.galeri.length > 0)
                    ? data.galeri
                    : (data.gambar ? [data.gambar] : []);

                if (pengumumanCarouselController) {
                    pengumumanCarouselController.init(galeri);
                }

                document.getElementById('pengumumanModal').classList.remove('hidden');
                document.getElementById('pengumumanModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closePengumumanModal() {
                if (pengumumanCarouselController) {
                    pengumumanCarouselController.stop();
                }
                document.getElementById('pengumumanModal').classList.add('hidden');
                document.getElementById('pengumumanModal').classList.remove('flex');
                document.body.style.overflow = '';
            }

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

                const galeri = (data.galeri && data.galeri.length > 0)
                    ? data.galeri
                    : (data.gambar ? [data.gambar] : []);

                if (beritaCarouselController) {
                    beritaCarouselController.init(galeri);
                }

                document.getElementById('beritaModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeBeritaModal() {
                if (beritaCarouselController) {
                    beritaCarouselController.stop();
                }
                document.getElementById('beritaModal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeBeritaModal();
                    closePengumumanModal();
                } else if (event.key === 'ArrowLeft') {
                    if (!document.getElementById('beritaModal').classList.contains('hidden') && beritaCarouselController) {
                        beritaCarouselController.prev();
                    } else if (!document.getElementById('pengumumanModal').classList.contains('hidden') && pengumumanCarouselController) {
                        pengumumanCarouselController.prev();
                    }
                } else if (event.key === 'ArrowRight') {
                    if (!document.getElementById('beritaModal').classList.contains('hidden') && beritaCarouselController) {
                        beritaCarouselController.next();
                    } else if (!document.getElementById('pengumumanModal').classList.contains('hidden') && pengumumanCarouselController) {
                        pengumumanCarouselController.next();
                    }
                }
            });

            @php
                $popupData = null;
                if ($popupPengumuman) {
                    $popupImg = $popupPengumuman->gambar
                        ? (str_starts_with($popupPengumuman->gambar, 'img/')
                            ? asset($popupPengumuman->gambar)
                            : asset('storage/' . $popupPengumuman->gambar))
                        : null;

                    $popupGaleri = [];
                    if (isset($popupPengumuman->gambarBerita) && $popupPengumuman->gambarBerita->isNotEmpty()) {
                        foreach ($popupPengumuman->gambarBerita as $gb) {
                            $popupGaleri[] = str_starts_with($gb->gambar, 'img/')
                                ? asset($gb->gambar)
                                : asset('storage/' . $gb->gambar);
                        }
                    }
                    if (empty($popupGaleri) && $popupImg) {
                        $popupGaleri = [$popupImg];
                    }

                    $popupData = [
                        'id' => $popupPengumuman->id,
                        'judul' => $popupPengumuman->judul,
                        'gambar' => $popupImg,
                        'galeri' => $popupGaleri,
                        'kota' => $popupPengumuman->kota,
                        'tanggal' => $popupPengumuman->tanggal_lokasi
                            ? \Carbon\Carbon::parse($popupPengumuman->tanggal_lokasi)->translatedFormat('d F Y')
                            : null,
                        'deskripsi' => $popupPengumuman->deskripsi,
                    ];
                }
            @endphp
            @if ($popupData)
                const popupPengumuman = @json($popupData);
                const popupKey = 'bebras-popup-pengumuman-' + popupPengumuman.id;
                if (!sessionStorage.getItem(popupKey)) {
                    sessionStorage.setItem(popupKey, 'shown');
                    openPengumumanModal(popupPengumuman);
                }
            @endif
        </script>
    @endpush

@endsection
