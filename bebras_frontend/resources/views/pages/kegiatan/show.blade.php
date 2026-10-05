@extends('app')

@section('title', $menu->judul ?? $menu->nama_menu)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-6 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-2">
                    {{ $menu->judul ?? $menu->nama_menu }}
                </h1>
                @if($menu->body)
                    <div class="text-gray-600 mt-2 prose max-w-none">
                        {!! $menu->body !!}
                    </div>
                @endif
            </div>
            @if($menu->gambar)
                <img src="{{ asset('storage/' . $menu->gambar) }}"
                     alt="{{ $menu->nama_menu }}"
                     class="w-20 h-20 object-contain mx-auto md:mx-0 mt-4 md:mt-0">
            @else
                <img src="{{ asset('img/done.png') }}" alt="Bebras"
                     class="w-16 h-16 mx-auto md:mx-0 mt-4 md:mt-0">
            @endif
        </div>

        {{-- Kegiatan Cards Grid --}}
        @if($menu->kegiatans->isNotEmpty())
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($menu->kegiatans as $kegiatan)
                    @php
                        $colorMap = [
                            'Bogor'     => 'blue',
                            'Semarang'  => 'green',
                            'Lampung'   => 'yellow',
                            'Bandung'   => 'pink',
                            'Samarinda' => 'purple',
                            'Bali'      => 'red',
                            'Pekanbaru' => 'indigo',
                            'Jakarta'   => 'teal',
                        ];
                        $color = $colorMap[$kegiatan->kota] ?? 'blue';

                        $gambarUrl = null;
                        if ($kegiatan->gambar) {
                            $gambarUrl = str_starts_with($kegiatan->gambar, 'img/') ? asset($kegiatan->gambar) : asset('storage/' . $kegiatan->gambar);
                        }

                        $tglFormatted = null;
                        if ($kegiatan->tanggal_lokasi) {
                            try {
                                $tglFormatted = \Carbon\Carbon::parse($kegiatan->tanggal_lokasi)->translatedFormat('d F Y');
                            } catch (\Exception $e) {
                                $tglFormatted = $kegiatan->tanggal_lokasi;
                            }
                        }

                        $cardData = json_encode([
                            'judul' => $kegiatan->judul,
                            'gambar' => $gambarUrl,
                            'kota' => $kegiatan->kota,
                            'tanggal' => $tglFormatted,
                            'speaker' => $kegiatan->speaker,
                            'deskripsi' => $kegiatan->deskripsi,
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                    @endphp

                    <div class="group bg-white rounded-xl shadow hover:shadow-2xl transition duration-300 overflow-hidden border border-gray-100 cursor-pointer flex flex-col justify-between"
                         onclick='openDetailModal({!! $cardData !!})'>

                        <div>
                            {{-- Gambar atau Placeholder --}}
                            @if($gambarUrl)
                                <div class="overflow-hidden">
                                    <img src="{{ $gambarUrl }}"
                                         alt="{{ $kegiatan->judul }}"
                                         class="w-full h-48 object-cover transform group-hover:scale-105 transition duration-500">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="p-5">
                                {{-- Badge kota (hanya jika ada) --}}
                                @if($kegiatan->kota)
                                    <span class="text-xs font-semibold text-{{ $color }}-600 bg-{{ $color }}-100 px-2.5 py-1 rounded-full inline-block">
                                        {{ $kegiatan->kota }}
                                    </span>
                                @endif

                                <h5 class="mt-3 text-lg font-bold text-gray-800 line-clamp-2 leading-snug">
                                    {{ $kegiatan->judul }}
                                </h5>

                                @if($tglFormatted)
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 font-medium">
                                        <span>📅</span> <span>{{ $tglFormatted }}</span>
                                    </p>
                                @endif

                                @if($kegiatan->speaker)
                                    <p class="text-xs font-bold text-gray-700 mt-1 flex items-center gap-1">
                                        <span>🎤</span> <span>{{ $kegiatan->speaker }}</span>
                                    </p>
                                @endif

                                <div class="text-gray-600 text-sm mt-2 leading-relaxed line-clamp-vertical break-words [word-break:break-all]">
                                    {!! $kegiatan->deskripsi !!}
                                </div>
                            </div>
                        </div>

                        <div class="px-5 pb-4 pt-0">
                            <span class="text-xs font-semibold text-bebrasDarkBlue group-hover:text-bebrasBlue inline-flex items-center gap-1 transition">
                                Lihat selengkapnya <i class="fas fa-arrow-right text-[10px] transform group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-lg font-medium">Belum ada kegiatan untuk halaman ini.</p>
                <p class="text-sm mt-1">Tambahkan kegiatan melalui halaman admin.</p>
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
