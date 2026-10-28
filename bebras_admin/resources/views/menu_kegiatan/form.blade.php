@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        <div class="row justify-content-center">
            <div class="col-md-9">

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error!</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ── FORM UTAMA MENU ── --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} Menu Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="{{ isset($data) ? route('menu_kegiatan.update', $data->id) : route('menu_kegiatan.store') }}"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($data)) @method('PUT') @endif

                            <div class="row g-3">

                                {{-- Parent Menu --}}
                                <div class="col-md-6">
                                    <label for="parent_id" class="form-label">Parent Menu</label>
                                    <select id="parent_id" name="parent_id"
                                            class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">-- Tidak Ada (Menu Induk) --</option>
                                        @foreach ($menuList as $menu)
                                            <option value="{{ $menu->id }}"
                                                data-template="{{ $menu->resolveTemplate() }}"
                                                data-template-name="{{ match($menu->resolveTemplate()) {
                                                    'pengumuman_hasil' => 'Pengumuman Hasil',
                                                    'bebras_challenge' => 'Bebras Challenge',
                                                    'workshop'         => 'Workshop',
                                                    'statistik'        => 'Statistik',
                                                    default            => 'Tanpa Template',
                                                } }}"
                                                {{ old('parent_id', $data->parent_id ?? '') == $menu->id ? 'selected' : '' }}>
                                                {{ $menu->nama_menu }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Biarkan kosong untuk menu tingkat pertama (muncul di dropdown Kegiatan).</small>
                                </div>

                                {{-- Template --}}
                                <div class="col-md-6" id="template-field">
                                    <label for="template" class="form-label">Template</label>
                                    <select id="template" name="template"
                                            class="form-select @error('template') is-invalid @enderror">
                                        <option value="" {{ old('template', $data->template ?? '') === '' ? 'selected' : '' }}>-- Tanpa Template --</option>
                                        <option value="bebras_challenge" {{ old('template', $data->template ?? '') === 'bebras_challenge' ? 'selected' : '' }}>Bebras Challenge</option>
                                        <option value="workshop" {{ old('template', $data->template ?? '') === 'workshop' ? 'selected' : '' }}>Workshop</option>
                                        <option value="pengumuman_hasil" {{ old('template', $data->template ?? '') === 'pengumuman_hasil' ? 'selected' : '' }}>Pengumuman Hasil</option>
                                        <option value="statistik" {{ old('template', $data->template ?? '') === 'statistik' ? 'selected' : '' }}>Statistik</option>
                                    </select>
                                    @error('template')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Template tampilan halaman kegiatan di frontend (hanya untuk menu induk).</small>
                                </div>

                                {{-- Pesan Template Diwarisi --}}
                                <div class="col-md-6" id="template-inherited-msg" style="display:none;">
                                    <label class="form-label">Template</label>
                                    <div class="alert alert-info py-2 mb-0">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Template diwarisi dari parent: <strong id="inherited-template-label">-</strong>
                                    </div>
                                </div>

                                {{-- Nama Menu --}}
                                <div class="col-md-6">
                                    <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                    <input type="text" id="nama_menu" name="nama_menu"
                                           class="form-control @error('nama_menu') is-invalid @enderror"
                                           value="{{ old('nama_menu', $data->nama_menu ?? '') }}"
                                           placeholder="Contoh: Workshop 2017" required>
                                    @error('nama_menu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Slug --}}
                                <div class="col-md-6">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" id="slug" name="slug"
                                           value="{{ old('slug', $data->slug ?? '') }}"
                                           class="form-control @error('slug') is-invalid @enderror"
                                           placeholder="Otomatis dari Nama Menu">
                                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Urutan --}}
                                <div class="col-md-6">
                                    <label for="urutan" class="form-label">Urutan <span class="text-danger">*</span></label>
                                    <input type="number" id="urutan" name="urutan"
                                           value="{{ old('urutan', $data->urutan ?? 0) }}"
                                           class="form-control @error('urutan') is-invalid @enderror"
                                           min="0" required>
                                    @error('urutan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- URL Eksternal (opsional) --}}
                                <div class="col-md-12">
                                    <label for="url" class="form-label">URL Eksternal <small class="text-muted">(opsional – isi jika menu ini menuju situs lain)</small></label>
                                    <input type="url" id="url" name="url"
                                           class="form-control @error('url') is-invalid @enderror"
                                           value="{{ old('url', $data->url ?? '') }}"
                                           placeholder="https://contoh.com">
                                    @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Judul Konten --}}
                                <div class="col-md-12">
                                    <label class="form-label">Judul Konten <small class="text-muted">(judul halaman yang ditampilkan)</small></label>
                                    <input type="text" name="judul" class="form-control"
                                           value="{{ old('judul', $data->judul ?? '') }}"
                                           placeholder="Contoh: Workshop Bebras 2017">
                                </div>

                                {{-- Body Konten --}}
                                <div class="col-md-12">
                                    <label class="form-label">Deskripsi / Body Konten</label>
                                    <textarea name="body" class="form-control tinymce-editor"
                                              rows="5">{{ old('body', $data->body ?? '') }}</textarea>
                                    <small class="text-muted">Konten ini ditampilkan di atas daftar kartu kegiatan pada halaman frontend.</small>
                                </div>

                                {{-- ── UPLOAD CSV PENGUMUMAN HASIL (Khusus Template Pengumuman Hasil) ── --}}
                                <div class="col-md-12" id="field-pengumuman-hasil" style="display:none;">
                                    <div class="card border border-primary bg-light mb-0">
                                        <div class="card-header bg-label-primary py-2 d-flex align-items-center gap-2">
                                            <i class="bx bx-file text-primary fs-5"></i>
                                            <strong class="text-primary">Upload File CSV / Excel Hasil Tantangan</strong>
                                        </div>
                                        <div class="card-body pt-3 pb-2">
                                            <p class="text-muted small mb-3">
                                                Silakan unggah file CSV atau Excel untuk masing-masing kategori peserta (Si Kecil, Siaga, Penggalang, Penegak). Format yang didukung: <code>.csv</code>, <code>.xlsx</code>, <code>.xls</code>. File ini akan disimpan di storage dan ditampilkan di frontend.
                                            </p>
                                            @php
                                                $filesMap = isset($data) ? $data->pengumumanHasilFiles->keyBy('kategori') : collect();
                                                $kategoriConfig = [
                                                    'sikecil'    => ['label' => 'Si Kecil', 'color' => 'success', 'desc' => 'Tingkat SD Kelas 1-3'],
                                                    'siaga'      => ['label' => 'Siaga', 'color' => 'info', 'desc' => 'Tingkat SD Kelas 4-6'],
                                                    'penggalang' => ['label' => 'Penggalang', 'color' => 'warning', 'desc' => 'Tingkat SMP'],
                                                    'penegak'    => ['label' => 'Penegak', 'color' => 'danger', 'desc' => 'Tingkat SMA / SMK'],
                                                ];
                                            @endphp
                                            <div class="row g-3">
                                                @foreach($kategoriConfig as $key => $cfg)
                                                    @php $currentFile = $filesMap->get($key); @endphp
                                                    <div class="col-md-6">
                                                        <div class="p-3 bg-white rounded border">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <label class="form-label fw-bold mb-0 text-{{ $cfg['color'] }}">
                                                                    <i class="bx bx-spreadsheet me-1"></i> {{ $cfg['label'] }}
                                                                </label>
                                                                <small class="text-muted">{{ $cfg['desc'] }}</small>
                                                            </div>
                                                            <input type="file" name="csv_{{ $key }}" id="csv_{{ $key }}"
                                                                   class="form-control form-control-sm @error('csv_'.$key) is-invalid @enderror"
                                                                   accept=".csv, .xlsx, .xls">
                                                            @error('csv_'.$key)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            @if($currentFile)
                                                                <div class="mt-2 p-2 bg-light rounded d-flex align-items-center justify-content-between">
                                                                    <div class="text-truncate me-2 small">
                                                                        <i class="bx bxs-file-doc text-success me-1"></i>
                                                                        <span class="fw-semibold text-dark">{{ $currentFile->original_name ?? basename($currentFile->file_path) }}</span>
                                                                        @if($currentFile->file_size)
                                                                            <span class="badge bg-secondary ms-1">{{ number_format($currentFile->file_size / 1024, 1) }} KB</span>
                                                                        @endif
                                                                    </div>
                                                                    <a href="{{ asset('storage/' . $currentFile->file_path) }}" target="_blank" download class="btn btn-xs btn-outline-primary flex-shrink-0">
                                                                        <i class="bx bx-download"></i> Unduh
                                                                    </a>
                                                                </div>
                                                                <small class="text-muted d-block mt-1 fst-italic">Kosongkan jika tidak ingin mengganti file ini.</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Gambar --}}
                                <div class="col-md-12" id="field-gambar">
                                    <label class="form-label">Gambar (opsional)</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-grow-1">
                                            <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                                            <small class="text-muted">Maks 4 MB (jpg, png, webp)</small>
                                        </div>
                                        <div>
                                            @if(isset($data) && $data->gambar)
                                                <div class="border rounded overflow-hidden"
                                                     style="width:120px;height:120px;display:flex;justify-content:center;align-items:center;">
                                                    <img src="{{ Storage::url($data->gambar) }}" alt="Preview"
                                                         class="img-fluid" style="max-width:100%;max-height:100%;">
                                                </div>
                                                <p class="mt-1 text-center text-muted small">Preview</p>
                                            @else
                                                <div class="border rounded bg-light d-flex justify-content-center align-items-center"
                                                     style="width:120px;height:120px;">
                                                    <i class="bx bx-image-add fs-2 text-secondary"></i>
                                                </div>
                                                <p class="mt-1 text-center text-muted small">Belum ada gambar</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol --}}
                                <div class="d-flex justify-content-between mt-2">
                                    <a href="{{ route('menu_kegiatan.index') }}" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── PANEL: DAFTAR KEGIATAN (item kartu) — hanya tampil saat EDIT --}}
                @if(isset($data))
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">🎯 Daftar Kegiatan (Kartu)</h5>
                        <a href="{{ route('kegiatan.create') }}?menu_kegiatan_id={{ $data->id }}"
                           class="btn btn-sm btn-primary">
                            <i class="bx bx-plus me-1"></i> Tambah Kegiatan
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2 mb-3">
                            <small>Kegiatan di bawah ini ditampilkan sebagai <strong>kartu grid</strong> pada halaman
                            <code>/kegiatan/{{ $data->slug }}</code> di Frontend.</small>
                        </div>

                        @if($data->kegiatans->isEmpty())
                            <div class="alert alert-warning mb-0">
                                Belum ada kegiatan untuk menu ini.
                                <a href="{{ route('kegiatan.create') }}?menu_kegiatan_id={{ $data->id }}">Tambah kegiatan →</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Gambar</th>
                                            <th>Judul</th>
                                            <th>Kota</th>
                                            <th>Urutan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->kegiatans as $i => $kg)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                @if($kg->gambar)
                                                    @php $imgUrl = str_starts_with($kg->gambar, 'img/') ? asset($kg->gambar) : asset('storage/' . $kg->gambar); @endphp
                                                    <img src="{{ $imgUrl }}" width="60" height="45"
                                                         style="object-fit:cover;border-radius:4px;">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $kg->judul }}</td>
                                            <td>{{ $kg->kota ?? '-' }}</td>
                                            <td>{{ $kg->urutan }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('kegiatan.edit', $kg->id) }}"
                                                       class="btn btn-sm btn-warning">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <form action="{{ route('kegiatan.destroy', $kg->id) }}"
                                                          method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const namaMenuInput = document.getElementById('nama_menu');
        const slugInput     = document.getElementById('slug');
        let isManuallyEdited = slugInput.value.trim() !== '';

        namaMenuInput.addEventListener('input', function () {
            if (!isManuallyEdited) slugInput.value = generateSlug(this.value);
        });
        slugInput.addEventListener('input', function () { isManuallyEdited = true; });

        function generateSlug(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        function getEffectiveTemplateInfo() {
            const parentSelect = document.getElementById('parent_id');
            const parentId     = parentSelect ? parentSelect.value : '';
            if (parentId) {
                const selectedOption = parentSelect.querySelector(`option[value="${parentId}"]`);
                return {
                    template: selectedOption ? (selectedOption.dataset.template || '') : '',
                    label: selectedOption ? (selectedOption.dataset.templateName || 'Tanpa Template') : 'Tanpa Template',
                    isInherited: true
                };
            }
            const templateSelect = document.getElementById('template');
            const tplVal = templateSelect ? templateSelect.value : '';
            const tplLabels = {
                'pengumuman_hasil': 'Pengumuman Hasil',
                'bebras_challenge': 'Bebras Challenge',
                'workshop': 'Workshop',
                'statistik': 'Statistik',
            };
            return {
                template: tplVal,
                label: tplLabels[tplVal] || 'Tanpa Template',
                isInherited: false
            };
        }

        // ── Toggle field Template, CSV Hasil, & Gambar berdasarkan template yang aktif ──
        function updateFormState() {
            const parentId             = document.getElementById('parent_id').value;
            const templateField        = document.getElementById('template-field');
            const inheritedMsg         = document.getElementById('template-inherited-msg');
            const inheritedLabel       = document.getElementById('inherited-template-label');
            const fieldGambar          = document.getElementById('field-gambar');
            const fieldPengumuman      = document.getElementById('field-pengumuman-hasil');
            const gambarInput          = document.getElementById('gambar');

            const info = getEffectiveTemplateInfo();

            // 1. Tampilkan / sembunyikan dropdown template tergantung parent
            if (parentId) {
                templateField.style.display  = 'none';
                if (inheritedMsg) {
                    inheritedMsg.style.display = 'block';
                    if (inheritedLabel) inheritedLabel.textContent = info.label;
                }
            } else {
                templateField.style.display  = 'block';
                if (inheritedMsg) inheritedMsg.style.display = 'none';
            }

            // 2. Cek template efektif (apakah pengumuman_hasil)
            if (info.template === 'pengumuman_hasil') {
                if (fieldPengumuman) {
                    fieldPengumuman.style.display = 'block';
                    fieldPengumuman.querySelectorAll('input').forEach(el => el.disabled = false);
                }
                if (fieldGambar) {
                    fieldGambar.style.display = 'none';
                    if (gambarInput) {
                        gambarInput.disabled = true;
                        gambarInput.value = '';
                    }
                }
            } else {
                if (fieldPengumuman) {
                    fieldPengumuman.style.display = 'none';
                    fieldPengumuman.querySelectorAll('input').forEach(el => el.disabled = true);
                }
                if (fieldGambar) {
                    fieldGambar.style.display = 'block';
                    if (gambarInput) {
                        gambarInput.disabled = false;
                    }
                }
            }
        }

        document.getElementById('parent_id').addEventListener('change', updateFormState);
        document.getElementById('template').addEventListener('change', updateFormState);
        updateFormState(); // terapkan state awal saat halaman dimuat
    });
</script>
@endpush
