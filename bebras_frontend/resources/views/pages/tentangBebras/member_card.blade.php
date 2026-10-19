@extends('app')

@section('title', 'Kartu Tanda Anggota - ' . $anggota->nama)

@section('content')
<section class="w-full px-4 py-10 md:py-16 bg-gradient-to-br from-[#F7FBFF] via-white to-[#EAF4FC] min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb / Back Navigation -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('tentangBebras.dd_7') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#087E9B] hover:text-[#063B5C] transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar Biro Bebras</span>
            </a>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>Anggota Resmi Terverifikasi</span>
            </div>
        </div>

        <!-- Header Card Info -->
        <div class="rounded-3xl bg-white border border-cyan-100 shadow-xl overflow-hidden p-6 md:p-10 mb-8">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-50 text-[#087E9B] mb-3">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Kartu Tanda Anggota Biro Bebras</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">
                    {{ $anggota->nama }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $anggota->nama_biro }} &bull; ID Member: <span class="font-mono font-bold text-[#087E9B]">{{ $anggota->formatted_id_member }}</span>
                </p>
            </div>

            <!-- Member Card Visual Preview -->
            <div class="w-full max-w-[720px] mx-auto shadow-2xl rounded-2xl overflow-hidden border border-cyan-200">
                <div class="relative w-full aspect-[1016/638] bg-cover bg-center select-none"
                     style="background-image: url('{{ asset('img/membership_card_template.png') }}');">

                    <!-- QR Code -->
                    <div class="absolute top-[10.3%] right-[3.74%] w-[21.0%] h-[33.5%] bg-white p-[1.5%] rounded-md flex items-center justify-center shadow-xs">
                        <div class="w-full h-full flex items-center justify-center">
                            {!! $anggota->qr_code_svg !!}
                        </div>
                    </div>

                    <!-- ID Member (Below QR Code) -->
                    <div class="absolute top-[44.5%] right-[3.74%] w-[21.0%] text-center text-white font-black tracking-wider text-[11px] sm:text-[14px] md:text-[18px] drop-shadow-sm font-sans">
                        {{ $anggota->formatted_id_member }}
                    </div>

                    <!-- Nama Anggota -->
                    <div class="absolute top-[55.5%] right-[3.74%] max-w-[65%] text-right text-black font-black uppercase text-[15px] sm:text-[22px] md:text-[30px] leading-tight tracking-tight font-sans drop-shadow-xs">
                        {{ $anggota->nama }}
                    </div>

                    <!-- Nama Biro (Below pre-printed 'BIRO BEBRAS') -->
                    <div class="absolute top-[78.5%] right-[3.74%] max-w-[65%] text-right text-white font-bold uppercase text-[10px] sm:text-[15px] md:text-[20px] leading-tight tracking-wide font-sans drop-shadow-xs">
                        {{ $anggota->nama_biro }}
                    </div>

                    <!-- Expiry Date -->
                    <div class="absolute top-[90.8%] right-[3.74%] max-w-[40%] text-right text-black font-semibold text-[7px] sm:text-[10px] md:text-[13px] font-sans">
                        {{ $anggota->formatted_expiry_date }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('tentangBebras.anggota.card.download', $anggota->id) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-[#063B5C] to-[#087E9B] text-white font-bold text-sm hover:from-[#052e49] hover:to-[#06657c] transition-all shadow-md hover:shadow-lg transform active:scale-95">
                    <i class="fa-solid fa-file-arrow-down text-base"></i>
                    <span>Download Kartu (PDF)</span>
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-gray-700 font-semibold text-sm border border-gray-200 hover:bg-gray-50 transition-all shadow-xs">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Kartu</span>
                </button>
            </div>
        </div>
    </div>
</section>
@endsection
