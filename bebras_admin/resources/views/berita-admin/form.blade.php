@extends('app')

@section('content')
<div class="col-12">
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

            @if(isset($data))
                {{-- Status validasi saat ini (read-only) --}}
                <div class="alert d-flex align-items-center gap-2
                    {{ $data->status_validasi === 'approved' ? 'alert-success' :
                       ($data->status_validasi === 'rejected' ? 'alert-danger' : 'alert-warning') }}">
                    <i class="bx {{ $data->status_validasi === 'approved' ? 'bx-check-circle' :
                                    ($data->status_validasi === 'rejected' ? 'bx-x-circle' : 'bx-time') }} fs-5"></i>
                    <div>
                        <strong>Status Validasi:</strong>
                        @if($data->status_validasi === 'approved')
                            <span class="badge bg-success ms-1">Approved</span>
                        @elseif($data->status_validasi === 'rejected')
                            <span class="badge bg-danger ms-1">Rejected</span>
                        @else
                            <span class="badge bg-warning text-dark ms-1">Pending</span>
                        @endif
                        <small class="text-muted ms-2">
                            — Ubah status melalui tombol Approve / Reject di halaman daftar berita.
                        </small>
                    </div>
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center gap-2">
                    <i class="bx bx-info-circle fs-5"></i>
                    <div>
                        <strong>Informasi:</strong> Berita yang ditambahkan oleh Admin akan otomatis berstatus <span class="badge bg-success">Approved</span> dan langsung tampil di halaman depan.
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($data) ? 'Edit Berita' : 'Tambah Berita' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="{{ isset($data) ? route('admin.berita.update', $data->id) : route('admin.berita.store') }}"
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($data))
                            @method('PUT')
                        @endif



                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control"
                                   value="{{ old('judul', $data->judul ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control tinymce-editor"
                                      rows="6">{{ old('deskripsi', $data->deskripsi ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kota <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="kota" class="form-control"
                                   value="{{ old('kota', $data->kota ?? '') }}"
                                   placeholder="Contoh: Jakarta">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal &amp; Lokasi <small class="text-muted">(opsional)</small></label>
                            @php
                                $tglValue = old('tanggal_lokasi', isset($data) && $data->tanggal_lokasi ? $data->tanggal_lokasi : '');
                                try {
                                    $tglValue = $tglValue ? \Carbon\Carbon::parse($tglValue)->format('Y-m-d') : '';
                                } catch (\Exception $e) {
                                    $tglValue = '';
                                }
                            @endphp
                            <input type="date" name="tanggal_lokasi" class="form-control"
                                   value="{{ $tglValue }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Speaker <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="speaker" class="form-control"
                                   value="{{ old('speaker', $data->speaker ?? '') }}"
                                   placeholder="Nama pembicara / narasumber">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control"
                                   value="{{ old('urutan', $data->urutan ?? 0) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Sampul / Cover <small class="text-muted">(opsional, maks 2MB — jpg/png/webp - tampil di daftar berita)</small></label>
                            @if(isset($data) && $data->gambar)
                                @php
                                    $currentImg = str_starts_with($data->gambar, 'img/')
                                        ? asset($data->gambar)
                                        : asset('storage/' . $data->gambar);
                                @endphp
                                <div class="mb-2">
                                    <img src="{{ $currentImg }}" alt="Gambar Sampul saat ini"
                                         class="img-thumbnail" style="max-height:140px;">
                                    <p class="text-muted small mt-1">Gambar sampul saat ini. Kosongkan input jika tidak ingin mengganti.</p>
                                </div>
                            @endif
                            <input type="file" name="gambar" class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                        </div>

                        {{-- Galeri Foto Berita (Carousel di Atas Deskripsi) --}}
                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-bold">
                                <i class="bx bx-images me-1 text-primary"></i> Galeri Foto Berita (Carousel)
                            </label>
                            <p class="text-muted small mb-2">
                                Foto-foto dokumentasi ini akan ditampilkan dalam bentuk <strong>slider / carousel</strong> tepat di atas deskripsi berita. Dapat memilih lebih dari satu foto sekaligus.
                            </p>

                            @if(isset($data) && $data->gambarBerita->isNotEmpty())
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-semibold">Foto Galeri Tersimpan:</label>
                                    <div class="row g-2">
                                        @foreach($data->gambarBerita as $gb)
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
                                                        <input class="form-check-input me-1" type="checkbox" name="hapus_gambar_berita[]" value="{{ $gb->id }}" id="hapus_gb_{{ $gb->id }}">
                                                        <label class="form-check-label text-danger small" for="hapus_gb_{{ $gb->id }}">
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
                                {{ isset($data) && $data->gambarBerita->isNotEmpty() ? 'Tambah Foto Galeri Baru:' : 'Upload Foto Galeri (bisa banyak foto):' }}
                            </label>
                            <input type="file" name="gambar_berita[]" class="form-control"
                                   accept="image/jpeg,image/png,image/webp" multiple>
                            <small class="text-muted">Tahan tombol Ctrl (atau Cmd di Mac) untuk memilih beberapa foto sekaligus. Maks 2MB per foto.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> {{ isset($data) ? 'Simpan Perubahan' : 'Simpan Berita' }}
                            </button>
                            <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
