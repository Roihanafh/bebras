@extends('app')

@section('title', 'Workshop 2017')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-6 mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4 md:mb-0">
                Workshop 2017
            </h1>
            <img src="{{ asset('img/done.png') }}" alt="Workshop Bebras" class="w-16 h-16 mx-auto md:mx-0 ">
        </div>

        <!-- Deskripsi -->
        <div class="mb-6 text-gray-700">
            <h4 class="text-lg md:text-xl font-semibold mb-3">
                Kegiatan Workshop Bebras 2017 adalah sebagai berikut:
            </h4>
        </div>

        <!-- Grid Kegiatan -->
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            @foreach ($workshops as $workshop)
            @php
                // Map city colors for badge
                $color = 'blue';
                if ($workshop->kota === 'Semarang') $color = 'green';
                elseif ($workshop->kota === 'Lampung') $color = 'yellow';
                elseif ($workshop->kota === 'Bandung') $color = 'pink';
                elseif ($workshop->kota === 'Samarinda') $color = 'purple';
                elseif ($workshop->kota === 'Bali') $color = 'red';
                elseif ($workshop->kota === 'Pekanbaru') $color = 'indigo';
            @endphp
            <!-- Card -->
            <div class="group bg-gray-50 rounded-xl shadow hover:shadow-2xl transition duration-300 overflow-hidden">
                <div class="overflow-hidden">
                    <img src="{{ (strpos($workshop->gambar, 'img/') === 0) ? asset($workshop->gambar) : asset('storage/' . $workshop->gambar) }}" alt="Workshop Bebras"
                        class="w-full h-48 object-cover transform group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5">
                    <span class="text-xs font-medium text-{{ $color }}-600 bg-{{ $color }}-100 px-2 py-1 rounded-full">{{ $workshop->kota }}</span>
                    <h5 class="mt-3 text-lg font-bold text-gray-800 line-clamp-2">
                        {{ $workshop->judul }}
                    </h5>
                    <div class="text-gray-600 text-sm mt-2 leading-relaxed line-clamp-vertical break-words">
                        {!! $workshop->deskripsi !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
