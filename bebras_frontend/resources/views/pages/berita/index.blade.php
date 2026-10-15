@extends('app')

@section('title', 'Berita')
@section('content')
    <div class="w-full px-4 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-6xl mx-auto">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center border-b pb-6 mb-8 gap-6">
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                    Berita
                </h1>
            </div>

            @if ($beritas->isEmpty())
                <!-- Pesan kosong -->
                <p class="text-gray-500 text-center py-8">Belum ada berita yang tersedia</p>
            @else
                <!-- Daftar Berita -->
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($beritas as $berita)
                        @php
                            $gambarUrl = null;
                            if (!empty($berita->gambar)) {
                                $gambarUrl = str_starts_with($berita->gambar, 'img/') ? asset($berita->gambar) : asset('storage/' . $berita->gambar);
                            }

                            $galeri = [];
                            if (isset($berita->gambarBerita) && $berita->gambarBerita->isNotEmpty()) {
                                foreach ($berita->gambarBerita as $gb) {
                                    $galeri[] = str_starts_with($gb->gambar, 'img/')
                                        ? asset($gb->gambar)
                                        : asset('storage/' . $gb->gambar);
                                }
                            }

                            $tglFormatted = null;
                            if ($berita->tanggal_lokasi) {
                                try {
                                    $tglFormatted = \Carbon\Carbon::parse($berita->tanggal_lokasi)->translatedFormat('d F Y');
                                } catch (\Exception $e) {
                                    $tglFormatted = $berita->tanggal_lokasi;
                                }
                            }

                            $cardData = json_encode([
                                'judul' => $berita->judul,
                                'gambar' => $gambarUrl,
                                'galeri' => $galeri,
                                'kota' => $berita->kota,
                                'tanggal' => $tglFormatted,
                                'speaker' => $berita->speaker,
                                'deskripsi' => $berita->deskripsi,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp

                        <div class="group bg-gray-50 hover:bg-white rounded-xl shadow hover:shadow-xl transition duration-300 p-6 border border-gray-100 cursor-pointer flex flex-col justify-between h-full"
                             onclick='openDetailModal({!! $cardData !!})'>

                            <div>
                                <!-- Gambar atau Placeholder -->
                                @if ($gambarUrl)
                                    <img src="{{ $gambarUrl }}"
                                         alt="{{ $berita->judul }}"
                                         class="w-full h-48 object-cover rounded-lg mb-4 transform group-hover:scale-[1.02] transition duration-300">
                                @else
                                    <div class="berita-placeholder w-full h-48 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                        <span class="text-gray-400 text-sm">Tidak ada gambar</span>
                                    </div>
                                @endif

                                <!-- Judul -->
                                <h2 class="text-xl font-bold text-gray-900 mb-2 leading-snug">{{ $berita->judul }}</h2>

                                <!-- Detail Info -->
                                <div class="space-y-1 mb-3">
                                    @if ($tglFormatted)
                                        <p class="text-gray-500 text-xs flex items-center gap-1 font-medium">
                                            <span>📅</span> <span class="font-semibold">Tanggal:</span> {{ $tglFormatted }}
                                        </p>
                                    @endif

                                    @if (!empty($berita->kota))
                                        <p class="text-gray-500 text-xs flex items-center gap-1">
                                            <span>📍</span> <span class="font-semibold">Kota:</span> {{ $berita->kota }}
                                        </p>
                                    @endif

                                    @if (!empty($berita->speaker))
                                        <p class="text-gray-500 text-xs flex items-center gap-1 font-bold">
                                            <span>🎤</span> <span class="font-semibold">Speaker:</span> {{ $berita->speaker }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Deskripsi -->
                                <div class="text-gray-600 text-sm mb-3 line-clamp-vertical break-words" style="max-height: 4.5rem; overflow: hidden;">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($berita->deskripsi), 180) }}
                                </div>
                            </div>

                            <div class="pt-2">
                                <span class="text-xs font-semibold text-bebrasDarkBlue group-hover:text-bebrasBlue inline-flex items-center gap-1 transition">
                                    Lihat selengkapnya <i class="fas fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    {{-- Modal Detail Pop-Up --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300" onclick="closeDetailModal()">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden transform transition-all my-8" onclick="event.stopPropagation()">
            {{-- Header Modal --}}
            <div class="relative bg-gradient-to-r from-bebrasDarkBlue to-bebrasBlue p-6 text-white">
                <button onclick="closeDetailModal()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center transition" aria-label="Tutup modal">
                    <i class="fas fa-times"></i>
                </button>
                <span id="modalKota" class="inline-block text-xs font-semibold uppercase bg-white/20 px-2.5 py-1 rounded-full mb-2 hidden"></span>
                <h3 id="modalJudul" class="text-xl md:text-2xl font-bold leading-tight"></h3>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4 pb-4 border-b">
                    <div id="modalTanggalContainer" class="hidden flex items-center gap-1.5 font-medium">
                        <span>📅</span> <span id="modalTanggal"></span>
                    </div>
                    <div id="modalSpeakerContainer" class="hidden flex items-center gap-1.5 font-bold text-gray-800">
                        <span>🎤</span> <span id="modalSpeaker"></span>
                    </div>
                </div>

                {{-- TEPAT DI ATAS DESKRIPSI: CAROUSEL GALERI BERITA (ANTI-STRETCH) --}}
                <div id="modalCarouselContainer" class="mb-6 hidden">
                    <div class="relative w-full h-64 sm:h-72 md:h-80 overflow-hidden rounded-xl bg-slate-950 flex items-center justify-center shadow-inner group">
                        {{-- Blurred ambient background to fill remaining aspect space without distortion --}}
                        <img id="carouselBgImg" src="" alt="" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-30 scale-110 pointer-events-none select-none">

                        {{-- Main foreground image with strict aspect ratio preservation (anti-stretch) --}}
                        <img id="carouselMainImg" src="" alt="" class="relative z-10 max-h-full max-w-full w-auto h-auto object-contain mx-auto transition-opacity duration-300 drop-shadow-md select-none">

                        {{-- Left arrow button --}}
                        <button type="button" id="carouselPrevBtn" onclick="prevCarouselSlide()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-sm transition focus:outline-none"
                                aria-label="Foto Sebelumnya">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>

                        {{-- Right arrow button --}}
                        <button type="button" id="carouselNextBtn" onclick="nextCarouselSlide()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center backdrop-blur-sm transition focus:outline-none"
                                aria-label="Foto Selanjutnya">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>

                        {{-- Counter badge --}}
                        <div id="carouselBadge" class="absolute top-3 right-3 z-20 px-2.5 py-1 rounded-full bg-black/60 text-white text-xs font-semibold backdrop-blur-sm select-none">
                            1 / 1
                        </div>

                        {{-- Dots indicator --}}
                        <div id="carouselDots" class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex gap-1.5 pointer-events-auto"></div>
                    </div>
                </div>

                <div id="modalDeskripsi" class="tiny-content prose max-w-none text-gray-700 leading-relaxed text-sm md:text-base"></div>
            </div>

            {{-- Footer Modal --}}
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                <button onclick="closeDetailModal()" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    let currentCarouselImages = [];
    let currentCarouselIndex = 0;
    let carouselAutoplayTimer = null;

    function startCarouselAutoplay() {
        stopCarouselAutoplay();
        if (currentCarouselImages.length > 1) {
            carouselAutoplayTimer = setInterval(() => {
                if (currentCarouselImages.length <= 1) return;
                currentCarouselIndex = (currentCarouselIndex + 1) % currentCarouselImages.length;
                renderCarouselSlide();
            }, 3500);
        }
    }

    function stopCarouselAutoplay() {
        if (carouselAutoplayTimer) {
            clearInterval(carouselAutoplayTimer);
            carouselAutoplayTimer = null;
        }
    }

    function resetCarouselAutoplay() {
        stopCarouselAutoplay();
        startCarouselAutoplay();
    }

    function initModalCarousel(images) {
        currentCarouselImages = images || [];
        currentCarouselIndex = 0;
        stopCarouselAutoplay();

        const container = document.getElementById('modalCarouselContainer');
        const prevBtn = document.getElementById('carouselPrevBtn');
        const nextBtn = document.getElementById('carouselNextBtn');
        const badge = document.getElementById('carouselBadge');
        const dotsContainer = document.getElementById('carouselDots');

        if (!currentCarouselImages || currentCarouselImages.length === 0) {
            container.classList.add('hidden');
            return;
        }

        container.classList.remove('hidden');

        // Pause on hover
        container.onmouseenter = stopCarouselAutoplay;
        container.onmouseleave = startCarouselAutoplay;

        // Jika hanya 1 gambar, sembunyikan kontrol navigasi
        if (currentCarouselImages.length === 1) {
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
            badge.classList.add('hidden');
            dotsContainer.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
            badge.classList.remove('hidden');
            dotsContainer.classList.remove('hidden');
            startCarouselAutoplay();
        }

        renderCarouselSlide();
    }

    function renderCarouselSlide() {
        if (currentCarouselImages.length === 0) return;

        const bgImg = document.getElementById('carouselBgImg');
        const mainImg = document.getElementById('carouselMainImg');
        const badge = document.getElementById('carouselBadge');
        const dotsContainer = document.getElementById('carouselDots');

        const currentUrl = currentCarouselImages[currentCarouselIndex];

        mainImg.style.opacity = '0.3';
        bgImg.src = currentUrl;
        mainImg.src = currentUrl;
        setTimeout(() => { mainImg.style.opacity = '1'; }, 50);

        badge.innerText = (currentCarouselIndex + 1) + ' / ' + currentCarouselImages.length;

        dotsContainer.innerHTML = '';
        currentCarouselImages.forEach((_, idx) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'h-2 rounded-full transition-all ' +
                (idx === currentCarouselIndex ? 'bg-white w-5' : 'bg-white/50 w-2 hover:bg-white/80');
            dot.onclick = () => {
                currentCarouselIndex = idx;
                renderCarouselSlide();
                resetCarouselAutoplay();
            };
            dotsContainer.appendChild(dot);
        });
    }

    function prevCarouselSlide() {
        if (currentCarouselImages.length <= 1) return;
        currentCarouselIndex = (currentCarouselIndex - 1 + currentCarouselImages.length) % currentCarouselImages.length;
        renderCarouselSlide();
        resetCarouselAutoplay();
    }

    function nextCarouselSlide() {
        if (currentCarouselImages.length <= 1) return;
        currentCarouselIndex = (currentCarouselIndex + 1) % currentCarouselImages.length;
        renderCarouselSlide();
        resetCarouselAutoplay();
    }

    function openDetailModal(data) {
        document.getElementById('modalJudul').innerText = data.judul || '';

        const modalKota = document.getElementById('modalKota');
        if (data.kota) {
            modalKota.innerText = data.kota;
            modalKota.classList.remove('hidden');
        } else {
            modalKota.classList.add('hidden');
        }

        const modalTanggalContainer = document.getElementById('modalTanggalContainer');
        if (data.tanggal) {
            document.getElementById('modalTanggal').innerText = data.tanggal;
            modalTanggalContainer.classList.remove('hidden');
        } else {
            modalTanggalContainer.classList.add('hidden');
        }

        const modalSpeakerContainer = document.getElementById('modalSpeakerContainer');
        if (data.speaker) {
            document.getElementById('modalSpeaker').innerText = data.speaker;
            modalSpeakerContainer.classList.remove('hidden');
        } else {
            modalSpeakerContainer.classList.add('hidden');
        }

        // Tampilkan galeri foto di carousel atas deskripsi
        const galeri = (data.galeri && data.galeri.length > 0)
            ? data.galeri
            : (data.gambar ? [data.gambar] : []);
        initModalCarousel(galeri);

        document.getElementById('modalDeskripsi').innerHTML = data.deskripsi || '';

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        stopCarouselAutoplay();
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        } else if (e.key === 'ArrowLeft') {
            prevCarouselSlide();
        } else if (e.key === 'ArrowRight') {
            nextCarouselSlide();
        }
    });
</script>
@endpush
@endsection
