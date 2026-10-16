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
                            <li class="biro-list-item mb-4 pb-2 border-b border-gray-100 last:border-b-0" id="biro-item-{{ $biro->id }}">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <span class="font-semibold text-gray-800 cursor-pointer hover:text-[#087E9B] transition-colors btn-toggle-biro" data-biro-id="{{ $biro->id }}">
                                            {{ $biro->name }}
                                        </span>
                                        @if($biro->koordinatorBiro && $biro->koordinatorBiro->count() > 0)
                                            <span class="text-gray-600">
                                                @foreach($biro->koordinatorBiro as $koordinator)
                                                    <span class="text-gray-400 mx-1">|</span>
                                                    <a href="mailto:{{ $koordinator->email }}" title="{{ $koordinator->email }}">{{ $koordinator->nama }}</a>
                                                @endforeach
                                            </span>
                                        @endif
                                    </div>
                                    <button type="button" class="btn-toggle-biro not-prose inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-50 text-[#087E9B] hover:bg-[#087E9B] hover:text-white transition-all cursor-pointer shadow-xs" data-biro-id="{{ $biro->id }}" title="Klik untuk melihat anggota biro">
                                        <i class="fa-solid fa-users text-[11px]"></i>
                                        <span>Anggota</span>
                                        <span class="bg-white text-[#087E9B] px-1.5 py-0.2 rounded-full text-[10px] font-bold border border-cyan-100">{{ $biro->anggotaBiro->count() }}</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 biro-chevron-{{ $biro->id }}"></i>
                                    </button>
                                </div>

                                {{-- Accordion Anggota Biro --}}
                                <div class="anggota-accordion-panel not-prose hidden mt-3 pt-2 pb-1 pl-3 md:pl-4 border-l-2 border-[#00B8D4]" id="anggota-panel-{{ $biro->id }}">
                                    <div class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-2.5 flex items-center gap-1.5">
                                        <i class="fa-solid fa-id-badge text-[#087E9B]"></i>
                                        <span>Anggota {{ $biro->name }}</span>
                                    </div>
                                    <div class="space-y-1.5">
                                        @forelse($biro->anggotaBiro as $anggota)
                                            <div class="rounded-xl border border-gray-200 bg-gray-50/80 px-3.5 py-2.5 flex items-center justify-between hover:bg-cyan-50/40 hover:border-cyan-200 transition-colors shadow-xs">
                                                <div class="font-semibold text-gray-800 text-sm md:text-base">
                                                    {{ $anggota->no_anggota ? $anggota->no_anggota . ' — ' : '' }}{{ $anggota->nama }}
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-xs text-gray-500 italic">
                                                Belum ada anggota yang disetujui untuk biro ini.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-toggle-biro').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                // Hindari trigger jika klik pada link koordinator
                if (e.target.tagName.toLowerCase() === 'a') return;

                const biroId = this.getAttribute('data-biro-id');
                const panel = document.getElementById('anggota-panel-' + biroId);
                const chevrons = document.querySelectorAll('.biro-chevron-' + biroId);

                if (panel) {
                    const isHidden = panel.classList.contains('hidden');
                    if (isHidden) {
                        panel.classList.remove('hidden');
                        chevrons.forEach(c => c.classList.add('rotate-180'));
                    } else {
                        panel.classList.add('hidden');
                        chevrons.forEach(c => c.classList.remove('rotate-180'));
                    }
                }
            });
        });
    });
</script>
@endpush
