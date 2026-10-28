@extends('app')

@section('title', $menu->judul ?? $menu->nama_menu)

@section('content')
<section class="w-full px-4 py-8 md:py-12 bg-gradient-to-br from-[#F7FBFF] via-white to-[#EAF4FC] min-h-screen">
    <div class="max-w-6xl mx-auto">

        {{-- Hero Header --}}
        <header class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#063B5C] via-[#087E9B] to-[#00CAFF] px-6 py-10 md:px-12 md:py-14 text-center shadow-xl mb-8">
            <div class="absolute -left-16 -top-20 h-56 w-56 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute -bottom-28 -right-10 h-64 w-64 rounded-full bg-[#F7C948]/25 pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md border border-white/20 px-4 py-1 rounded-full">
                    <i class="fa-solid fa-bullhorn text-xs text-[#F7C948]"></i>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-100">Pengumuman Hasil</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-white drop-shadow-sm">
                    {{ $menu->judul ?? $menu->nama_menu }}
                </h1>
                @if($menu->body)
                    <div class="text-sky-50 text-sm md:text-base prose prose-invert max-w-none leading-relaxed pt-2">
                        {!! $menu->body !!}
                    </div>
                @endif
            </div>
        </header>

        @php
            $slug = $menu->slug;
            $excelGroups = [];
            $categoryLabels = [
                'sikecil'    => 'Si Kecil',
                'siaga'      => 'Siaga',
                'penggalang' => 'Penggalang',
                'penegak'    => 'Penegak',
            ];

            // 1. Ambil file CSV / Excel yang diunggah dari admin melalui database
            if ($menu->pengumumanHasilFiles && $menu->pengumumanHasilFiles->isNotEmpty()) {
                $filesByCat = $menu->pengumumanHasilFiles->keyBy('kategori');
                foreach ($categoryLabels as $key => $label) {
                    if (isset($filesByCat[$key])) {
                        $f = $filesByCat[$key];
                        $excelGroups[$label] = [
                            'url'      => asset('storage/' . $f->file_path),
                            'filename' => $f->original_name ?? basename($f->file_path),
                        ];
                    }
                }
            }

            // 2. Fallback: file statis dari folder public jika belum ada file di DB (backward compatibility)
            if (empty($excelGroups)) {
                $yearMap = [
                    'pengumuman-2025' => '2025',
                    'pengumuman-2024' => '2024',
                    'pengumuman-2023' => '2023',
                    'pengumuman-2022' => '2022',
                ];
                if (isset($yearMap[$slug])) {
                    $yr = $yearMap[$slug];
                    $candidates = [
                        'Si Kecil'   => "Hasil-BC{$yr}-SiKecil.xlsx",
                        'Siaga'      => "Hasil-BC{$yr}-Siaga.xlsx",
                        'Penggalang' => "Hasil-BC{$yr}-Penggalang.xlsx",
                        'Penegak'    => "Hasil-BC{$yr}-Penegak.xlsx",
                    ];
                    foreach ($candidates as $label => $fname) {
                        if (file_exists(public_path($fname))) {
                            $excelGroups[$label] = [
                                'url'      => asset($fname),
                                'filename' => $fname,
                            ];
                        }
                    }
                }
                if ($slug === 'ct-challenge-pengumuman') {
                    $fname = 'BebrasChallengeforTeacher2023.xlsx';
                    if (file_exists(public_path($fname))) {
                        $excelGroups['Pengumuman Hasil'] = [
                            'url'      => asset($fname),
                            'filename' => $fname,
                        ];
                    }
                }
            }
            $hasExcel = !empty($excelGroups);
        @endphp

        @if($hasExcel)
            @php $groupKeys = array_keys($excelGroups); @endphp
            <div class="space-y-6 mb-6">
                @if(count($excelGroups) > 1)
                <div class="flex flex-wrap gap-2 mb-3" id="excel-tabs">
                    @foreach($groupKeys as $i => $label)
                        <button
                            onclick="switchTab({{ $i }})"
                            id="tab-btn-{{ $i }}"
                            class="tab-btn px-5 py-2 rounded-xl text-sm font-semibold border transition-all duration-200 {{ $i === 0 ? 'bg-[#063B5C] text-white border-[#063B5C] shadow-md' : 'bg-white text-[#063B5C] border-[#087E9B]/30 hover:bg-[#E6F7FF]' }}">
                            <i class="fa-solid fa-table-cells-large mr-1.5"></i>
                            {{ $label }}
                        </button>
                    @endforeach
                    <button
                        onclick="switchTab('all')"
                        id="tab-btn-all"
                        class="tab-btn px-5 py-2 rounded-xl text-sm font-semibold border transition-all duration-200 bg-white text-[#063B5C] border-[#087E9B]/30 hover:bg-[#E6F7FF]">
                        <i class="fa-solid fa-layer-group mr-1.5"></i>
                        All
                    </button>
                </div>
                @endif

                @foreach($excelGroups as $label => $fileData)
                    @php
                        $i = array_search($label, $groupKeys);
                        $fileUrl  = is_array($fileData) ? $fileData['url'] : asset($fileData);
                        $fileName = is_array($fileData) ? $fileData['filename'] : $fileData;
                    @endphp
                    <div id="excel-panel-{{ $i }}"
                         class="excel-panel bg-white rounded-2xl shadow-lg border border-cyan-100 overflow-hidden {{ $i !== 0 ? 'hidden' : '' }}">

                        <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3 bg-gradient-to-r from-[#063B5C] to-[#087E9B]">
                            <div class="flex items-center gap-3 text-white">
                                <i class="fa-solid fa-file-excel text-emerald-300 text-lg"></i>
                                <span class="font-bold text-sm tracking-wide">{{ $label }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="relative hidden sm:block">
                                    <input type="text" id="search-{{ $i }}" placeholder="Cari {{ $label }}..."
                                        oninput="filterTable({{ $i }}, this.value)"
                                        class="text-xs pl-7 pr-3 py-1.5 rounded-lg border border-white/30 bg-white/15 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 w-44">
                                    <i class="fa-solid fa-magnifying-glass absolute left-2 top-1/2 -translate-y-1/2 text-white/60 text-xs"></i>
                                </div>
                                <a href="{{ $fileUrl }}" download="{{ $fileName }}"
                                   class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-white text-xs font-semibold rounded-lg transition-colors">
                                    <i class="fa-solid fa-download"></i> Unduh File
                                </a>
                            </div>
                        </div>

                        <div id="loading-{{ $i }}" class="flex flex-col items-center justify-center py-20 text-slate-400 gap-3">
                            <div class="w-8 h-8 border-4 border-[#00CAFF] border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-sm font-medium">Memuat data&#8230;</span>
                        </div>

                        <div id="table-wrap-{{ $i }}" class="hidden overflow-x-auto" style="max-height:600px;overflow-y:auto;">
                            <table id="excel-table-{{ $i }}" class="w-full border-collapse text-xs" data-src="{{ $fileUrl }}">
                            </table>
                        </div>

                        <div id="row-info-{{ $i }}" class="hidden text-right text-xs text-slate-400 px-4 py-2 border-t border-slate-100"></div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($menu->kegiatans->isNotEmpty())
            <div class="space-y-8 mt-8">
                @foreach($menu->kegiatans as $index => $kegiatan)
                    @php
                        $gambarUrl = null;
                        if ($kegiatan->gambar) {
                            $gambarUrl = str_starts_with($kegiatan->gambar, 'img/')
                                ? asset($kegiatan->gambar)
                                : asset('storage/' . $kegiatan->gambar);
                        }
                    @endphp
                    <div class="bg-white rounded-2xl border border-cyan-100 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#063B5C] to-[#087E9B] text-white px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-contract text-sky-200"></i>
                                <h3 class="font-bold text-sm md:text-base text-white tracking-wide">{{ $kegiatan->judul }}</h3>
                            </div>
                            <span class="text-xs font-semibold bg-white/20 text-white px-3 py-1 rounded-full uppercase tracking-wider">Hasil Resmi</span>
                        </div>
                        <div class="p-6 md:p-10">
                            @if($gambarUrl)
                                <div class="overflow-hidden rounded-xl mb-6 bg-[#E6F7FF]/50 p-3 border border-cyan-100 flex justify-center">
                                    <img src="{{ $gambarUrl }}" alt="{{ $kegiatan->judul }}" class="max-w-full h-auto max-h-[420px] object-contain rounded-lg">
                                </div>
                            @endif
                            <div class="prose max-w-none text-slate-600 leading-relaxed text-base embed-iframe-container">
                                {!! $kegiatan->deskripsi !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(!$hasExcel && $menu->kegiatans->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-cyan-100 shadow-sm max-w-2xl mx-auto">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#E6F7FF] flex items-center justify-center text-[#087E9B]">
                    <i class="fa-regular fa-clipboard text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Belum Ada Pengumuman Hasil</h3>
                <p class="text-sm text-slate-500 mt-1">Hasil pengumuman tantangan Bebras akan ditampilkan di halaman ini.</p>
            </div>
        @endif

    </div>
</section>

<style>
[id^="excel-table-"] thead tr th {
    background: linear-gradient(135deg,#063B5C 0%,#087E9B 100%);
    color:#fff; font-weight:700; font-size:11px; padding:8px 12px;
    text-align:center; position:sticky; top:0; z-index:10;
    border-right:1px solid rgba(255,255,255,.15); white-space:nowrap; letter-spacing:.03em;
}
[id^="excel-table-"] thead tr th:first-child { min-width:36px; background:#063B5C; }
[id^="excel-table-"] tbody tr td {
    padding:5px 12px; font-size:11.5px;
    border-bottom:1px solid #e8f4fb; border-right:1px solid #e8f4fb;
    color:#1e293b; vertical-align:middle;
}
[id^="excel-table-"] tbody tr td:first-child {
    text-align:center; font-weight:600; color:#087E9B;
    background:#f0f9ff; font-size:10.5px; border-right:2px solid #bae6fd; min-width:36px;
}
[id^="excel-table-"] tbody tr:nth-child(even) td { background:#f7fbff; }
[id^="excel-table-"] tbody tr:nth-child(even) td:first-child { background:#e0f2fe; }
[id^="excel-table-"] tbody tr:hover td { background:#dbeafe !important; }
[id^="excel-table-"] tbody tr:hover td:first-child { background:#bfdbfe !important; }
[id^="excel-table-"] tbody tr.row-highlight td { background:#fef9c3 !important; }
.embed-iframe-container iframe {
    width:100% !important; min-height:600px !important;
    border-radius:16px; border:1px solid #bae6fd;
    margin-top:1.25rem; margin-bottom:1.25rem;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@if($hasExcel ?? false)
<script>
(function(){
    var panelKeys = @json(array_keys($excelGroups ?? []));

    function esc(v){
        if(v==null) return '';
        return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function renderSheet(tableEl, ws, infoEl){
        if(!ws || !ws['!ref']) {
            tableEl.innerHTML = '<tbody><tr><td class="text-center py-6 text-slate-400">File kosong atau data tidak terbaca.</td></tr></tbody>';
            if(infoEl) infoEl.textContent = '0 baris data';
            return;
        }
        var range = XLSX.utils.decode_range(ws['!ref']||'A1');

        // 1. Cari batas kolom terakhir (maxCol) yang memiliki teks header atau data
        var maxCol = -1;
        for(var C = range.s.c; C <= range.e.c; C++){
            var addr = XLSX.utils.encode_cell({r: range.s.r, c: C});
            var cell = ws[addr];
            var val = cell ? XLSX.utils.format_cell(cell).trim() : '';
            if(val){
                maxCol = C;
            }
        }

        // Jika tidak ada header di baris pertama, cari batas kolom dari baris-baris data awal
        if(maxCol === -1){
            for(var R = range.s.r; R <= Math.min(range.e.r, range.s.r + 20); R++){
                for(var C = range.s.c; C <= range.e.c; C++){
                    var addr = XLSX.utils.encode_cell({r: R, c: C});
                    var cell = ws[addr];
                    var val = cell ? XLSX.utils.format_cell(cell).trim() : '';
                    if(val && C > maxCol){
                        maxCol = C;
                    }
                }
            }
        }

        if(maxCol === -1){
            tableEl.innerHTML = '<tbody><tr><td class="text-center py-6 text-slate-400">File tidak memiliki data.</td></tr></tbody>';
            if(infoEl) infoEl.textContent = '0 baris data';
            return;
        }

        // 2. Kumpulkan kolom-kolom valid yang tidak kosong
        var validCols = [];
        for(var C = range.s.c; C <= maxCol; C++){
            var headerAddr = XLSX.utils.encode_cell({r: range.s.r, c: C});
            var headerCell = ws[headerAddr];
            var headerVal = headerCell ? XLSX.utils.format_cell(headerCell).trim() : '';

            if(headerVal){
                validCols.push({ col: C, header: headerVal });
            } else {
                // Jika header kosong, cek apakah ada data di baris bawahnya
                var hasColData = false;
                for(var R = range.s.r + 1; R <= Math.min(range.e.r, range.s.r + 50); R++){
                    var addr = XLSX.utils.encode_cell({r: R, c: C});
                    var cell = ws[addr];
                    if(cell && XLSX.utils.format_cell(cell).trim()){
                        hasColData = true;
                        break;
                    }
                }
                if(hasColData){
                    validCols.push({ col: C, header: '' });
                }
            }
        }

        if(validCols.length === 0){
            tableEl.innerHTML = '<tbody><tr><td class="text-center py-6 text-slate-400">File tidak memiliki data.</td></tr></tbody>';
            if(infoEl) infoEl.textContent = '0 baris data';
            return;
        }

        // 3. Render Header (thead)
        var html = '<thead><tr><th>#</th>';
        for(var i = 0; i < validCols.length; i++){
            html += '<th>' + esc(validCols[i].header) + '</th>';
        }
        html += '</tr></thead><tbody>';

        // 4. Render Baris (tbody)
        var cnt = 0;
        for(var R = range.s.r + 1; R <= range.e.r; R++){
            var rowHtml = '';
            var hasRowData = false;
            for(var i = 0; i < validCols.length; i++){
                var addr = XLSX.utils.encode_cell({r: R, c: validCols[i].col});
                var cell = ws[addr];
                var val = cell ? XLSX.utils.format_cell(cell) : '';
                if(val && val.trim()){
                    hasRowData = true;
                }
                rowHtml += '<td>' + esc(val) + '</td>';
            }
            if(hasRowData){
                cnt++;
                html += '<tr><td>' + cnt + '</td>' + rowHtml + '</tr>';
            }
        }
        html += '</tbody>';
        tableEl.innerHTML = html;
        if(infoEl) infoEl.textContent = 'Menampilkan ' + cnt.toLocaleString('id-ID') + ' baris data';
    }

    async function loadExcel(idx){
        var tbl = document.getElementById('excel-table-' + idx);
        var wrap = document.getElementById('table-wrap-' + idx);
        var load = document.getElementById('loading-' + idx);
        var info = document.getElementById('row-info-' + idx);
        if(!tbl || tbl.dataset.loaded === '1') return;
        try {
            var r = await fetch(tbl.dataset.src);
            if(!r.ok) throw new Error('HTTP ' + r.status);
            var buf = await r.arrayBuffer();
            var wb = XLSX.read(buf, {type:'array'});
            var ws = wb.Sheets[wb.SheetNames[0]];
            renderSheet(tbl, ws, info);
            tbl.dataset.loaded = '1';
            load.classList.add('hidden');
            wrap.classList.remove('hidden');
            if(info) info.classList.remove('hidden');
        } catch(e) {
            load.innerHTML = '<div class="text-center py-10 text-slate-400"><i class="fa-solid fa-circle-exclamation text-3xl text-rose-400 mb-2 block"></i><p class="text-sm">Gagal memuat file CSV / Excel.</p><p class="text-xs">' + esc(e.message) + '</p></div>';
        }
    }

    window.switchTab = function(target){
        var isAll = (target === 'all');

        for(var i=0; i<panelKeys.length; i++){
            var btn = document.getElementById('tab-btn-' + i);
            if(btn){
                if(!isAll && i === target){
                    btn.classList.add('bg-[#063B5C]','text-white','border-[#063B5C]','shadow-md');
                    btn.classList.remove('bg-white','text-[#063B5C]','border-[#087E9B]/30');
                } else {
                    btn.classList.remove('bg-[#063B5C]','text-white','border-[#063B5C]','shadow-md');
                    btn.classList.add('bg-white','text-[#063B5C]','border-[#087E9B]/30');
                }
            }
        }

        var btnAll = document.getElementById('tab-btn-all');
        if(btnAll){
            if(isAll){
                btnAll.classList.add('bg-[#063B5C]','text-white','border-[#063B5C]','shadow-md');
                btnAll.classList.remove('bg-white','text-[#063B5C]','border-[#087E9B]/30');
            } else {
                btnAll.classList.remove('bg-[#063B5C]','text-white','border-[#063B5C]','shadow-md');
                btnAll.classList.add('bg-white','text-[#063B5C]','border-[#087E9B]/30');
            }
        }

        for(var i=0; i<panelKeys.length; i++){
            var pan = document.getElementById('excel-panel-' + i);
            if(pan){
                if(isAll || i === target){
                    pan.classList.remove('hidden');
                    loadExcel(i);
                } else {
                    pan.classList.add('hidden');
                }
            }
        }
    };

    window.filterTable = function(idx, q){
        var tbl = document.getElementById('excel-table-' + idx); if(!tbl) return;
        q = q.toLowerCase().trim();
        var rows = tbl.querySelectorAll('tbody tr'); var shown = 0;
        rows.forEach(function(row){
            var match = !q || row.textContent.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
            row.classList.toggle('row-highlight', !!(q && match));
            if(match) shown++;
        });
        var info = document.getElementById('row-info-' + idx);
        if(info) info.textContent = q ? 'Ditemukan ' + shown.toLocaleString('id-ID') + ' baris yang cocok' : 'Menampilkan ' + shown.toLocaleString('id-ID') + ' baris data';
    };

    document.addEventListener('DOMContentLoaded', function(){ loadExcel(0); });
})();
</script>
@endif
@endpush
