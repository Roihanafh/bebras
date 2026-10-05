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
                        <div class="bg-gray-50 rounded-xl shadow hover:shadow-lg transition p-6">

                            <!-- Gambar atau Placeholder -->
                            @if (!empty($berita->gambar))
                                <img src="{{ asset('storage/' . $berita->gambar) }}"
                                     alt="{{ $berita->judul }}"
                                     class="w-full h-48 object-cover rounded-lg mb-4">
                            @else
                                <div class="berita-placeholder w-full h-48 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                    <span class="text-gray-400 text-sm">Tidak ada gambar</span>
                                </div>
                            @endif

                            <!-- Judul -->
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $berita->judul }}</h2>

                            <!-- Deskripsi -->
                            <p class="text-gray-600 text-sm mb-3">{!! $berita->deskripsi !!}</p>

                            <!-- Tanggal Lokasi -->
                            @if ($berita->tanggal_lokasi)
                                <p class="text-gray-500 text-xs mb-1">
                                    <span class="font-semibold">Tanggal:</span>
                                    {{ \Carbon\Carbon::parse($berita->tanggal_lokasi)->translatedFormat('d F Y') }}
                                </p>
                            @endif

                            <!-- Kota (hanya jika tidak kosong) -->
                            @if (!empty($berita->kota))
                                <p class="text-gray-500 text-xs mb-1">
                                    <span class="font-semibold">Kota:</span> {{ $berita->kota }}
                                </p>
                            @endif

                            <!-- Speaker (hanya jika tidak kosong) -->
                            @if (!empty($berita->speaker))
                                <p class="text-gray-500 text-xs">
                                    <span class="font-semibold">Speaker:</span> {{ $berita->speaker }}
                                </p>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
@endsection
