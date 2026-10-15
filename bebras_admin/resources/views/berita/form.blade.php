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
                                    Gambar Sampul / Cover
                                    <small class="text-muted">(opsional, maks. 2 MB — jpg/png/webp - tampil di daftar berita)</small>
                                </label>
                                @if(isset($berita) && $berita->gambar)
                                    @php
                                        $currentImg = str_starts_with($berita->gambar, 'img/')
                                            ? asset($berita->gambar)
                                            : asset('storage/' . $berita->gambar);
                                    @endphp
                                    <div class="mb-2">
                                        <img src="{{ $currentImg }}" alt="Gambar sampul saat ini"
                                             class="img-thumbnail" style="max-height:120px;">
                                        <p class="text-muted small mt-1">
                                            Gambar sampul saat ini. Kosongkan jika tidak ingin mengganti.
                                        </p>
                                    </div>
                                @endif
                                <input type="file" name="gambar" class="form-control"
                                       accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            {{-- Galeri Foto Berita (Carousel di Atas Deskripsi) --}}
                            <div class="mb-3 p-3 bg-light rounded border">
                                <label class="form-label fw-bold">
                                    <i class="bx bx-images me-1 text-primary"></i> Galeri Foto Berita (Carousel)
                                </label>
                                <p class="text-muted small mb-2">
                                    Foto-foto dokumentasi ini akan ditampilkan dalam bentuk <strong>slider / carousel</strong> tepat di atas deskripsi berita. Dapat memilih lebih dari satu foto sekaligus.
                                </p>

                                @if(isset($berita) && $berita->gambarBerita->isNotEmpty())
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-semibold">Foto Galeri Tersimpan:</label>
                                        <div class="row g-2">
                                            @foreach($berita->gambarBerita as $gb)
                                                @php
                                                    $gbUrl = str_starts_with($gb->gambar, 'img/')
                                                        ? asset($gb->gambar)
                                                        : asset('storage/' . $gb->gambar);
                                                @endphp
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <div class="card h-100 p-1 border shadow-none text-center">
                                                        <div style="height: 100px; overflow: hidden;" class="rounded bg-dark d-flex align-items-center justify-content-center">
                                                            <img src="{{ $gbUrl }}" alt="Foto Galeri" class="img-fluid" style="max-height: 100px; object-fit: contain;">
                                                        </div>
                                                        <div class="form-check mt-1 d-flex justify-content-center">
                                                            <input class="form-check-input me-1" type="checkbox" name="hapus_gambar_berita[]" value="{{ $gb->id }}" id="hapus_biro_gb_{{ $gb->id }}">
                                                            <label class="form-check-label text-danger small" for="hapus_biro_gb_{{ $gb->id }}">
                                                                Hapus
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted d-block mt-1">Centang "Hapus" pada foto yang ingin dihapus, lalu klik Simpan.</small>
                                    </div>
                                @endif

                                <label class="form-label small text-muted">
                                    {{ isset($berita) && $berita->gambarBerita->isNotEmpty() ? 'Tambah Foto Galeri Baru:' : 'Upload Foto Galeri (bisa banyak foto):' }}
                                </label>
                                <input type="file" name="gambar_berita[]" class="form-control"
                                       accept="image/jpeg,image/png,image/webp" multiple>
                                <small class="text-muted">Tahan tombol Ctrl (atau Cmd di Mac) untuk memilih beberapa foto sekaligus. Maks 2MB per foto.</small>
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
