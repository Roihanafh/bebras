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
                                <div class="text-gray-600 text-sm mb-3 line-clamp-vertical break-words">
                                    {!! $berita->deskripsi !!}
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
                <button onclick="closeDetailModal()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center transition">
                    <i class="fas fa-times"></i>
                </button>
                <span id="modalKota" class="inline-block text-xs font-semibold uppercase bg-white/20 px-2.5 py-1 rounded-full mb-2 hidden"></span>
                <h3 id="modalJudul" class="text-xl md:text-2xl font-bold leading-tight"></h3>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <div id="modalImageContainer" class="mb-4 hidden">
                    <img id="modalGambar" src="" alt="" class="w-full max-h-80 object-cover rounded-xl shadow">
                </div>

                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4 pb-4 border-b">
                    <div id="modalTanggalContainer" class="hidden flex items-center gap-1.5 font-medium">
                        <span>📅</span> <span id="modalTanggal"></span>
                    </div>
                    <div id="modalSpeakerContainer" class="hidden flex items-center gap-1.5 font-bold text-gray-800">
                        <span>🎤</span> <span id="modalSpeaker"></span>
                    </div>
                </div>

                <div id="modalDeskripsi" class="prose max-w-none text-gray-700 leading-relaxed text-sm md:text-base"></div>
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
    function openDetailModal(data) {
        document.getElementById('modalJudul').innerText = data.judul || '';

        const modalKota = document.getElementById('modalKota');
        if (data.kota) {
            modalKota.innerText = data.kota;
            modalKota.classList.remove('hidden');
        } else {
            modalKota.classList.add('hidden');
        }

        const modalImageContainer = document.getElementById('modalImageContainer');
        const modalGambar = document.getElementById('modalGambar');
        if (data.gambar) {
            modalGambar.src = data.gambar;
            modalImageContainer.classList.remove('hidden');
        } else {
            modalImageContainer.classList.add('hidden');
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

        document.getElementById('modalDeskripsi').innerHTML = data.deskripsi || '';

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        }
    });
</script>
@endpush
@endsection
