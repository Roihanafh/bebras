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
                                            <div class="btn-open-member-modal rounded-xl border border-gray-200 bg-gray-50/80 px-3.5 py-2.5 flex items-center justify-between hover:bg-cyan-50/80 hover:border-cyan-300 transition-all cursor-pointer shadow-xs group"
                                                 data-id="{{ $anggota->id }}"
                                                 data-nama="{{ $anggota->nama }}"
                                                 data-id-member="{{ $anggota->formatted_id_member }}"
                                                 data-biro="{{ $anggota->nama_biro }}"
                                                 data-exp="{{ $anggota->formatted_expiry_date }}"
                                                 data-download-url="{{ route('tentangBebras.anggota.card.download', $anggota->id) }}"
                                                 data-card-url="{{ route('tentangBebras.anggota.card', $anggota->id) }}"
                                                 title="Klik untuk melihat Kartu Member {{ $anggota->nama }}">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-7 h-7 rounded-lg bg-cyan-100/80 text-[#087E9B] flex items-center justify-center text-xs font-bold group-hover:bg-[#087E9B] group-hover:text-white transition-colors">
                                                        <i class="fa-solid fa-id-card"></i>
                                                    </div>
                                                    <div class="font-semibold text-gray-800 text-sm md:text-base group-hover:text-[#087E9B] transition-colors">
                                                        {{ $anggota->no_anggota ? $anggota->no_anggota . ' — ' : '' }}{{ $anggota->nama }}
                                                    </div>
                                                </div>
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-[#087E9B] border border-cyan-200 group-hover:bg-[#087E9B] group-hover:text-white group-hover:border-transparent transition-all shadow-xs">
                                                    <i class="fa-solid fa-address-card text-[11px]"></i>
                                                    <span>Kartu Member</span>
                                                </div>
                                                <!-- Hidden SVG QR Code for instant modal display -->
                                                <div class="hidden member-qr-source">
                                                    {!! $anggota->qr_code_svg !!}
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

    <!-- Modal Kartu Member -->
    <div id="member-card-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true">
        <div id="member-card-modal-content" class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-cyan-100 overflow-hidden transform scale-95 transition-transform duration-300">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-[#063B5C] via-[#087E9B] to-[#00B8D4] text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-cyan-200">
                        <i class="fa-solid fa-address-card text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base md:text-lg leading-tight">Kartu Tanda Anggota Biro Bebras</h3>
                        <p class="text-xs text-cyan-100" id="modal-card-subtitle">Pratinjau Kartu Member Resmi</p>
                    </div>
                </div>
                <button type="button" id="btn-close-modal" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer" title="Tutup Modal">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body: Card Preview -->
            <div class="p-4 sm:p-6 md:p-8 bg-gray-50 flex flex-col items-center justify-center">
                <div class="w-full max-w-[650px] shadow-2xl rounded-2xl overflow-hidden border border-cyan-200">
                    <div class="relative w-full aspect-[1016/638] bg-cover bg-center select-none"
                         style="background-image: url('{{ asset('img/membership_card_template.png') }}');">

                        <!-- QR Code -->
                        <div class="absolute top-[10.3%] right-[3.74%] w-[21.0%] h-[33.5%] bg-white p-[1.5%] rounded-md flex items-center justify-center shadow-xs">
                            <div id="modal-card-qr" class="w-full h-full flex items-center justify-center">
                                <!-- QR SVG injected here -->
                            </div>
                        </div>

                        <!-- ID Member (Below QR Code) -->
                        <div id="modal-card-id" class="absolute top-[44.5%] right-[3.74%] w-[21.0%] text-center text-white font-black tracking-wider text-[11px] sm:text-[14px] md:text-[17px] drop-shadow-sm font-sans">
                            <!-- ID injected here -->
                        </div>

                        <!-- Nama Anggota -->
                        <div id="modal-card-nama" class="absolute top-[55.5%] right-[3.74%] max-w-[65%] text-right text-black font-black uppercase text-[14px] sm:text-[20px] md:text-[28px] leading-tight tracking-tight font-sans drop-shadow-xs">
                            <!-- Nama injected here -->
                        </div>

                        <!-- Nama Biro (Below pre-printed 'BIRO BEBRAS') -->
                        <div id="modal-card-biro" class="absolute top-[78.5%] right-[3.74%] max-w-[65%] text-right text-white font-bold uppercase text-[9px] sm:text-[13px] md:text-[18px] leading-tight tracking-wide font-sans drop-shadow-xs">
                            <!-- Biro injected here -->
                        </div>

                        <!-- Expiry Date -->
                        <div id="modal-card-exp" class="absolute top-[90.8%] right-[3.74%] max-w-[40%] text-right text-black font-semibold text-[7px] sm:text-[9px] md:text-[12px] font-sans">
                            <!-- Expiry injected here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer: Action Buttons -->
            <div class="px-6 py-4 bg-white border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-gray-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Status: <strong class="text-emerald-700">Terdaftar & Aktif</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="#" id="modal-download-btn"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#063B5C] to-[#087E9B] text-white text-xs md:text-sm font-bold hover:from-[#052e49] hover:to-[#06657c] transition-all shadow-md hover:shadow-lg transform active:scale-95">
                        <i class="fa-solid fa-file-arrow-down"></i>
                        <span>Download Kartu (PDF)</span>
                    </a>
                    <a href="#" id="modal-view-btn" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-cyan-50 text-[#087E9B] hover:bg-cyan-100 text-xs md:text-sm font-semibold transition-colors">
                        <i class="fa-solid fa-up-right-from-square text-xs"></i>
                        <span>Buka Halaman</span>
                    </a>
                    <button type="button" id="btn-cancel-modal"
                            class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs md:text-sm font-semibold transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

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
        // Toggle Accordion Biro
        document.querySelectorAll('.btn-toggle-biro').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
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

        // Modal Kartu Member
        const modal = document.getElementById('member-card-modal');
        const modalContent = document.getElementById('member-card-modal-content');
        const btnClose = document.getElementById('btn-close-modal');
        const btnCancel = document.getElementById('btn-cancel-modal');

        const cardQr = document.getElementById('modal-card-qr');
        const cardId = document.getElementById('modal-card-id');
        const cardNama = document.getElementById('modal-card-nama');
        const cardBiro = document.getElementById('modal-card-biro');
        const cardExp = document.getElementById('modal-card-exp');
        const cardSubtitle = document.getElementById('modal-card-subtitle');
        const downloadBtn = document.getElementById('modal-download-btn');
        const viewBtn = document.getElementById('modal-view-btn');

        function openModal(data, qrHtml) {
            cardNama.textContent = data.nama || '';
            cardId.textContent = data.idMember || '';
            cardBiro.textContent = data.biro || '';
            cardExp.textContent = data.exp || '';
            cardSubtitle.textContent = (data.biro || '') + ' • ID: ' + (data.idMember || '');

            downloadBtn.setAttribute('href', data.downloadUrl || '#');
            viewBtn.setAttribute('href', data.cardUrl || '#');

            cardQr.innerHTML = qrHtml || '';

            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            });
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 250);
        }

        document.querySelectorAll('.btn-open-member-modal').forEach(function (item) {
            item.addEventListener('click', function () {
                const qrSource = this.querySelector('.member-qr-source');
                const qrHtml = qrSource ? qrSource.innerHTML : '';

                const data = {
                    nama: this.getAttribute('data-nama'),
                    idMember: this.getAttribute('data-id-member'),
                    biro: this.getAttribute('data-biro'),
                    exp: this.getAttribute('data-exp'),
                    downloadUrl: this.getAttribute('data-download-url'),
                    cardUrl: this.getAttribute('data-card-url')
                };

                openModal(data, qrHtml);
            });
        });

        if (btnClose) btnClose.addEventListener('click', closeModal);
        if (btnCancel) btnCancel.addEventListener('click', closeModal);

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endpush

