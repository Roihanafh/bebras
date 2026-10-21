@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        <div class="row justify-content-center">
            <div class="col-md-8">

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error!</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ isset($data) ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="{{ isset($data) ? route('kegiatan.update', $data->id) : route('kegiatan.store') }}"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($data)) @method('PUT') @endif

                            {{-- Simpan redirect_menu_id jika ada --}}
                            @if(request()->has('menu_kegiatan_id'))
                                <input type="hidden" name="redirect_menu_id" value="{{ request('menu_kegiatan_id') }}">
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Menu Kegiatan <span class="text-danger">*</span></label>
                                 <select name="menu_kegiatan_id" id="menu-kegiatan-select" class="form-select" required
                         data-template-url="{{ route('kegiatan.menu.template', ':id') }}"
                 >
                                     <option value="">-- Pilih Menu Kegiatan --</option>
                                     @foreach($menuList as $menu)
                                         <option value="{{ $menu->id }}"
                                             {{ old('menu_kegiatan_id', $data->menu_kegiatan_id ?? $defaultMenuId ?? '') == $menu->id ? 'selected' : '' }}>
                                             @if($menu->parent_id)
                                                 &nbsp;&nbsp;&nbsp;↳ {{ $menu->parent?->nama_menu }} / {{ $menu->nama_menu }}
                                             @else
                                                 {{ $menu->nama_menu }}
                                             @endif
                                         </option>
                                     @endforeach
                                 </select>
                                <small class="text-muted">Pilih menu kegiatan tempat kegiatan ini akan muncul sebagai kartu.</small>
                            </div>

                            @if(isset($isMenuNotLeaf) && $isMenuNotLeaf)
                                <div class="alert alert-warning py-2">
                                    <i class="bx bx-error-circle me-1"></i>
                                    Menu yang dipilih sudah memiliki sub-menu.
                                </div>
                            @endif

                            {{-- Alert untuk template Statistik --}}
                            <div id="alert-statistik" class="alert alert-warning py-3 mb-3" style="display:none;">
                                <i class="bx bx-error-circle me-2"></i>
                                <strong>Menu ini bertipe Statistik.</strong>
                                Data statistik tidak dikelola melalui form ini.
                                <a href="{{ route('statistik.index') }}" class="alert-link ms-2">
                                    &rarr; Kelola Data Statistik
                                </a>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control"
                                       value="{{ old('judul', $data->judul ?? '') }}" required>
                            </div>

                            <div id="field-deskripsi">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control tinymce-editor"
                                          rows="4">{{ old('deskripsi', $data->deskripsi ?? '') }}</textarea>
                            </div>
                            </div>

                            <div id="field-kota">
                            <div class="mb-3">
                                <label class="form-label">Kota <small class="text-muted">(opsional)</small></label>
                                <input type="text" name="kota" class="form-control"
                                       value="{{ old('kota', $data->kota ?? '') }}" placeholder="Contoh: Jakarta">
                            </div>
                            </div>

                            <div id="field-tanggal-lokasi">
                            <div class="mb-3">
                                <label class="form-label">Tanggal &amp; Lokasi <small class="text-muted">(opsional)</small></label>
                                <input type="text" name="tanggal_lokasi" class="form-control"
                                       value="{{ old('tanggal_lokasi', $data->tanggal_lokasi ?? '') }}"
                                       placeholder="Contoh: 15 Maret 2017, Hotel Santika Jakarta">
                            </div>
                            </div>

                            <div id="field-speaker">
                            <div class="mb-3">
                                <label class="form-label">Speaker <small class="text-muted">(opsional)</small></label>
                                <input type="text" name="speaker" class="form-control"
                                       value="{{ old('speaker', $data->speaker ?? '') }}"
                                       placeholder="Nama pembicara / narasumber">
                            </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                <input type="number" name="urutan" class="form-control"
                                       value="{{ old('urutan', $data->urutan ?? 1) }}" min="0" required>
                            </div>

                            <div id="field-gambar">
                            <div class="mb-3">
                                <label class="form-label">Gambar</label>
                                @if(isset($data) && $data->gambar)
                                    @php
                                        $currentImg = str_starts_with($data->gambar, 'img/')
                                            ? asset($data->gambar)
                                            : asset('storage/' . $data->gambar);
                                    @endphp
                                    <div class="mb-2">
                                        <img src="{{ $currentImg }}" alt="Gambar saat ini"
                                             class="img-thumbnail" style="max-height:120px;">
                                        <p class="text-muted small mt-1">Gambar saat ini. Kosongkan jika tidak ingin mengganti.</p>
                                    </div>
                                @endif
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                            </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Simpan
                                </button>
                                <a href="{{ route('kegiatan.index') }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>
@endsection

@push('js')
<script>
(function () {
    'use strict';

    /**
     * Terapkan visibilitas/state field sesuai template yang diterima dari endpoint AJAX.
     * @param {string|null} template  — 'bebras_challenge' | 'workshop' | 'pengumuman_hasil' | null
     */
    function applyTemplate(template) {
        const fieldKota          = document.getElementById('field-kota');
        const fieldTanggalLokasi = document.getElementById('field-tanggal-lokasi');
        const fieldSpeaker       = document.getElementById('field-speaker');
        const fieldGambar        = document.getElementById('field-gambar');
        const fieldDeskripsi     = document.getElementById('field-deskripsi');

        if (template === 'bebras_challenge') {
            // Sembunyikan alert statistik dan tampilkan tombol submit
            const alertStatistik = document.getElementById('alert-statistik');
            if (alertStatistik) { alertStatistik.style.display = 'none'; }
            const submitBtn = document.querySelector('[type="submit"]');
            if (submitBtn) { submitBtn.style.display = ''; }
            // Kembalikan field judul dan urutan ke state normal
            const fieldJudul = document.querySelector('[name="judul"]')?.closest('.mb-3');
            if (fieldJudul) {
                fieldJudul.style.display = '';
                fieldJudul.querySelectorAll('input').forEach(el => el.disabled = false);
            }
            const fieldUrutan = document.querySelector('[name="urutan"]')?.closest('.mb-3');
            if (fieldUrutan) {
                fieldUrutan.style.display = '';
                fieldUrutan.querySelectorAll('input').forEach(el => el.disabled = false);
            }

            // Inisialisasi TinyMCE pada #deskripsi jika belum aktif
            if (typeof tinymce !== 'undefined' && !tinymce.get('deskripsi')) {
                initTinyMCE('#deskripsi');
            }

            // Tampilkan field gambar
            fieldGambar.style.display = '';
            fieldGambar.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
            });

            // Sembunyikan, nonaktifkan, dan kosongkan kota / tanggal_lokasi / speaker
            [fieldKota, fieldTanggalLokasi, fieldSpeaker].forEach(function (field) {
                field.style.display = 'none';
                field.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.disabled = true;
                    el.value = '';
                });
            });

        } else if (template === 'pengumuman_hasil') {
            // Sembunyikan alert statistik dan tampilkan tombol submit
            const alertStatistik = document.getElementById('alert-statistik');
            if (alertStatistik) { alertStatistik.style.display = 'none'; }
            const submitBtn = document.querySelector('[type="submit"]');
            if (submitBtn) { submitBtn.style.display = ''; }
            // Kembalikan field judul dan urutan ke state normal
            const fieldJudul = document.querySelector('[name="judul"]')?.closest('.mb-3');
            if (fieldJudul) {
                fieldJudul.style.display = '';
                fieldJudul.querySelectorAll('input').forEach(el => el.disabled = false);
            }
            const fieldUrutan = document.querySelector('[name="urutan"]')?.closest('.mb-3');
            if (fieldUrutan) {
                fieldUrutan.style.display = '';
                fieldUrutan.querySelectorAll('input').forEach(el => el.disabled = false);
            }

            // Inisialisasi TinyMCE pada #deskripsi jika belum aktif
            if (typeof tinymce !== 'undefined' && !tinymce.get('deskripsi')) {
                initTinyMCE('#deskripsi');
            }

            // Hilangkan field gambar khusus pada template pengumuman hasil
            fieldGambar.style.display = 'none';
            fieldGambar.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = true;
                el.value = '';
            });

            // Sembunyikan, nonaktifkan, dan kosongkan kota / tanggal_lokasi / speaker
            [fieldKota, fieldTanggalLokasi, fieldSpeaker].forEach(function (field) {
                field.style.display = 'none';
                field.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.disabled = true;
                    el.value = '';
                });
            });

        } else if (template === 'workshop') {
            // Sembunyikan alert statistik dan tampilkan tombol submit
            const alertStatistik = document.getElementById('alert-statistik');
            if (alertStatistik) { alertStatistik.style.display = 'none'; }
            const submitBtn = document.querySelector('[type="submit"]');
            if (submitBtn) { submitBtn.style.display = ''; }
            // Kembalikan field judul dan urutan ke state normal
            const fieldJudul = document.querySelector('[name="judul"]')?.closest('.mb-3');
            if (fieldJudul) {
                fieldJudul.style.display = '';
                fieldJudul.querySelectorAll('input').forEach(el => el.disabled = false);
            }
            const fieldUrutan = document.querySelector('[name="urutan"]')?.closest('.mb-3');
            if (fieldUrutan) {
                fieldUrutan.style.display = '';
                fieldUrutan.querySelectorAll('input').forEach(el => el.disabled = false);
            }

            // Destroy TinyMCE jika aktif — kembalikan ke textarea biasa
            if (typeof tinymce !== 'undefined' && tinymce.get('deskripsi')) {
                tinymce.get('deskripsi').remove();
            }

            // Tampilkan dan aktifkan semua field
            [fieldKota, fieldTanggalLokasi, fieldSpeaker, fieldGambar].forEach(function (field) {
                field.style.display = '';
                field.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.disabled = false;
                });
            });

        } else if (template === 'statistik') {
            // Tampilkan alert statistik
            const alertStatistik = document.getElementById('alert-statistik');
            if (alertStatistik) { alertStatistik.style.display = ''; }

            // Sembunyikan dan disable semua field konten
            const submitBtn = document.querySelector('[type="submit"]');
            const fieldJudul = document.querySelector('[name="judul"]')?.closest('.mb-3');
            [fieldDeskripsi, fieldKota, fieldTanggalLokasi, fieldSpeaker, fieldGambar].forEach(function (field) {
                if (!field) { return; }
                field.style.display = 'none';
                field.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.disabled = true;
                    el.value = '';
                });
            });
            // Sembunyikan field judul
            if (fieldJudul) {
                fieldJudul.style.display = 'none';
                fieldJudul.querySelectorAll('input').forEach(function (el) {
                    el.disabled = true;
                    el.value = '';
                });
            }
            // Sembunyikan field urutan
            const fieldUrutan = document.querySelector('[name="urutan"]')?.closest('.mb-3');
            if (fieldUrutan) {
                fieldUrutan.style.display = 'none';
                fieldUrutan.querySelectorAll('input').forEach(function (el) {
                    el.disabled = true;
                });
            }
            if (submitBtn) { submitBtn.style.display = 'none'; }

        } else {
            // null / fallback — sembunyikan alert statistik, tampilkan semua field, biarkan TinyMCE tetap berjalan
            const alertStatistik = document.getElementById('alert-statistik');
            if (alertStatistik) { alertStatistik.style.display = 'none'; }
            const submitBtn = document.querySelector('[type="submit"]');
            if (submitBtn) { submitBtn.style.display = ''; }
            // Kembalikan field judul dan urutan ke state normal
            const fieldJudul = document.querySelector('[name="judul"]')?.closest('.mb-3');
            if (fieldJudul) {
                fieldJudul.style.display = '';
                fieldJudul.querySelectorAll('input').forEach(el => el.disabled = false);
            }
            const fieldUrutan = document.querySelector('[name="urutan"]')?.closest('.mb-3');
            if (fieldUrutan) {
                fieldUrutan.style.display = '';
                fieldUrutan.querySelectorAll('input').forEach(el => el.disabled = false);
            }

            [fieldKota, fieldTanggalLokasi, fieldSpeaker, fieldGambar].forEach(function (field) {
                field.style.display = '';
                field.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.disabled = false;
                });
            });
        }
    }

    /**
     * Ambil template untuk menu yang dipilih via AJAX lalu terapkan.
     * @param {string} menuId
     * @param {string} baseUrl  — URL dengan placeholder ':id'
     */
    function fetchAndApplyTemplate(menuId, baseUrl) {
        if (!menuId) {
            applyTemplate(null);
            return;
        }
        const url = baseUrl.replace(':id', menuId);
        fetch(url)
            .then(function (response) {
                if (!response.ok) { throw new Error('HTTP ' + response.status); }
                return response.json();
            })
            .then(function (data) {
                applyTemplate(data.template ?? null);
            })
            .catch(function () {
                applyTemplate(null);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const select  = document.getElementById('menu-kegiatan-select');
        if (!select) { return; }

        const baseUrl = select.dataset.templateUrl;

        // Listener change untuk pemilihan menu baru
        select.addEventListener('change', function () {
            fetchAndApplyTemplate(this.value, baseUrl);
        });

        // Mode edit: dropdown sudah terisi → terapkan state field secara otomatis
        if (select.value) {
            fetchAndApplyTemplate(select.value, baseUrl);
        }
    });
})();
</script>
@endpush
