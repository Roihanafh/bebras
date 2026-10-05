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

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Berita</h5>
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="{{ route('admin.berita.update', $data->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Menu Kegiatan <span class="text-danger">*</span></label>
                            <select name="menu_kegiatan_id" class="form-select" required>
                                <option value="">-- Pilih Menu Kegiatan --</option>
                                @foreach($menuList as $menu)
                                    <option value="{{ $menu->id }}"
                                        {{ old('menu_kegiatan_id', $data->menu_kegiatan_id) == $menu->id ? 'selected' : '' }}>
                                        @if($menu->parent_id)
                                            &nbsp;&nbsp;&nbsp;↳ {{ $menu->parent?->nama_menu }} / {{ $menu->nama_menu }}
                                        @else
                                            {{ $menu->nama_menu }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control"
                                   value="{{ old('judul', $data->judul) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control tinymce-editor"
                                      rows="6">{{ old('deskripsi', $data->deskripsi) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kota <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="kota" class="form-control"
                                   value="{{ old('kota', $data->kota) }}"
                                   placeholder="Contoh: Jakarta">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal &amp; Lokasi <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="tanggal_lokasi" class="form-control"
                                   value="{{ old('tanggal_lokasi', $data->tanggal_lokasi) }}"
                                   placeholder="Contoh: 15 Maret 2017, Hotel Santika Jakarta">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Speaker <small class="text-muted">(opsional)</small></label>
                            <input type="text" name="speaker" class="form-control"
                                   value="{{ old('speaker', $data->speaker) }}"
                                   placeholder="Nama pembicara / narasumber">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control"
                                   value="{{ old('urutan', $data->urutan ?? 0) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar <small class="text-muted">(opsional, maks 2MB — jpg/png/webp)</small></label>
                            @if($data->gambar)
                                @php
                                    $currentImg = str_starts_with($data->gambar, 'img/')
                                        ? asset($data->gambar)
                                        : asset('storage/' . $data->gambar);
                                @endphp
                                <div class="mb-2">
                                    <img src="{{ $currentImg }}" alt="Gambar saat ini"
                                         class="img-thumbnail" style="max-height:140px;">
                                    <p class="text-muted small mt-1">Gambar saat ini. Kosongkan input jika tidak ingin mengganti.</p>
                                </div>
                            @endif
                            <input type="file" name="gambar" class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
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
