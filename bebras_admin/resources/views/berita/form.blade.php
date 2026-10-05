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
                        <h5 class="mb-0">{{ isset($berita) ? 'Edit Berita' : 'Tambah Berita' }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="{{ isset($berita) ? route('berita.update', $berita->id) : route('berita.store') }}"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($berita)) @method('PUT') @endif

                            {{-- Menu Kegiatan --}}
                            <div class="mb-3">
                                <label class="form-label">Menu Kegiatan <span class="text-danger">*</span></label>
                                <select name="menu_kegiatan_id" class="form-select" required>
                                    <option value="">-- Pilih Menu Kegiatan --</option>
                                    @foreach($menuList as $menu)
                                        <option value="{{ $menu->id }}"
                                            {{ old('menu_kegiatan_id', $berita->menu_kegiatan_id ?? '') == $menu->id ? 'selected' : '' }}>
                                            @if($menu->parent_id)
                                                &nbsp;&nbsp;&nbsp;↳ {{ $menu->parent?->nama_menu }} / {{ $menu->nama_menu }}
                                            @else
                                                {{ $menu->nama_menu }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Judul --}}
                            <div class="mb-3">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control"
                                       value="{{ old('judul', $berita->judul ?? '') }}" required>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control tinymce-editor"
                                          rows="5">{{ old('deskripsi', $berita->deskripsi ?? '') }}</textarea>
                            </div>

                            {{-- Gambar --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Gambar
                                    <small class="text-muted">(opsional, maks. 2 MB — jpg/png/webp)</small>
                                </label>
                                @if(isset($berita) && $berita->gambar)
                                    @php
                                        $currentImg = str_starts_with($berita->gambar, 'img/')
                                            ? asset($berita->gambar)
                                            : asset('storage/' . $berita->gambar);
                                    @endphp
                                    <div class="mb-2">
                                        <img src="{{ $currentImg }}" alt="Gambar saat ini"
                                             class="img-thumbnail" style="max-height:120px;">
                                        <p class="text-muted small mt-1">
                                            Gambar saat ini. Kosongkan jika tidak ingin mengganti.
                                        </p>
                                    </div>
                                @endif
                                <input type="file" name="gambar" class="form-control"
                                       accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            {{-- Kota --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Kota <small class="text-muted">(opsional)</small>
                                </label>
                                <input type="text" name="kota" class="form-control"
                                       value="{{ old('kota', $berita->kota ?? '') }}"
                                       placeholder="Contoh: Jakarta">
                            </div>

                            {{-- Tanggal Lokasi --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Tanggal <small class="text-muted">(opsional)</small>
                                </label>
                                <input type="date" name="tanggal_lokasi" class="form-control"
                                       value="{{ old('tanggal_lokasi', isset($berita) && $berita->tanggal_lokasi ? \Carbon\Carbon::parse($berita->tanggal_lokasi)->format('Y-m-d') : '') }}">
                            </div>

                            {{-- Speaker --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Speaker <small class="text-muted">(opsional)</small>
                                </label>
                                <input type="text" name="speaker" class="form-control"
                                       value="{{ old('speaker', $berita->speaker ?? '') }}"
                                       placeholder="Nama pembicara / narasumber">
                            </div>

                            {{-- Urutan --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Urutan <small class="text-muted">(opsional)</small>
                                </label>
                                <input type="number" name="urutan" class="form-control"
                                       value="{{ old('urutan', $berita->urutan ?? 0) }}" min="0">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Simpan
                                </button>
                                <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary">Batal</a>
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
