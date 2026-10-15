@extends('app')

@section('title', $page->judul)

@section('content')
    <section class="w-full px-4 py-10 md:py-16 bg-gradient-to-br from-[#F7FBFF] via-white to-[#EAF4FC]">
        <div class="max-w-6xl mx-auto">
            <header class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#063B5C] via-[#087E9B] to-[#00B8D4] px-6 py-10 md:px-12 md:py-14 text-center shadow-xl">
                <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-28 -left-10 h-64 w-64 rounded-full bg-[#F7C948]/20"></div>
                <div class="relative">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.25em] text-cyan-100">Bebras Indonesia</p>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight text-white">
                        {{ $page->judul }}
                    </h1>
                </div>
            </header>

            @if ($page->gambar)
                <div class="mx-auto -mt-8 max-w-4xl px-4 relative z-10">
                    <img src="{{ str_starts_with($page->gambar, 'img/') ? asset($page->gambar) : asset('storage/' . $page->gambar) }}"
                        alt="{{ $page->judul }}" class="w-full max-h-[28rem] object-cover rounded-2xl border-8 border-white shadow-2xl">
                </div>
            @endif

            <div class="mx-auto mt-10 max-w-4xl rounded-2xl border border-cyan-100 bg-white px-6 py-6 md:px-10 shadow-sm">
                <div class="mb-3 flex items-center gap-3 text-[#087E9B]">
                    <span class="h-2 w-10 rounded-full bg-[#F7C948]"></span>
                    <span class="text-sm font-bold uppercase tracking-widest">Koordinator Bebras Biro</span>
                </div>
                <div class="biro-content prose max-w-none text-base leading-relaxed text-gray-600 md:text-lg">
                    @if ($page->konten)
                        @php
                            $intro = preg_replace('/<ol.*<\/ol>/is', '', $page->konten);
                        @endphp
                        @if(trim(strip_tags($intro)))
                            <div class="mb-4">
                                {!! $intro !!}
                            </div>
                        @endif
                    @endif

                    <ol>
                        @forelse($biros as $biro)
                            <li>
                                <span class="font-semibold text-gray-800">{{ $biro->name }}</span>
                                @if($biro->koordinatorBiro && $biro->koordinatorBiro->count() > 0)
                                    @foreach($biro->koordinatorBiro as $koordinator)
                                        <span class="text-gray-400 mx-1">|</span>
                                        <a href="mailto:{{ $koordinator->email }}" title="{{ $koordinator->email }}">{{ $koordinator->nama }}</a>
                                    @endforeach
                                @endif
                            </li>
                        @empty
                            <li class="text-gray-500 italic">Belum ada biro terdaftar.</li>
                        @endforelse
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <style>
        .biro-content ol {
            list-style-type: decimal;
            padding-left: 1.75rem;
            margin-top: 0.75rem;
        }

        .biro-content ol li {
            padding-left: 0.35rem;
            margin-bottom: 0.75rem;
        }

        .biro-content a[href^="mailto:"] {
            color: #087e9b;
            font-weight: 700;
            text-decoration: none;
        }

        .biro-content a[href^="mailto:"]:hover {
            color: #f07c00;
            text-decoration: underline;
        }
    </style>
@endsection
