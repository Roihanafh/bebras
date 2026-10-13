@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ isset($data) ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                              action="{{ isset($data) ? route('admin.pengumuman.update', $data->id) : route('admin.pengumuman.store') }}"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($data)) @method('PUT') @endif

                            <div class="mb-3">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control"
                                       value="{{ old('judul', $data->judul ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control tinymce-editor" rows="6">{{ old('deskripsi', $data->deskripsi ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gambar <small class="text-muted">(opsional, maks. 2 MB)</small></label>
                                @if(isset($data) && $data->gambar)
                                    @php
                                        $currentImg = str_starts_with($data->gambar, 'img/')
                                            ? asset($data->gambar)
                                            : asset('storage/' . $data->gambar);
                                    @endphp
                                    <div class="mb-2">
                                        <img src="{{ $currentImg }}" alt="Gambar saat ini" class="img-thumbnail" style="max-height:140px;">
                                        <p class="text-muted small mt-1">Kosongkan input jika tidak ingin mengganti.</p>
                                    </div>
                                @endif
                                <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kota <small class="text-muted">(opsional)</small></label>
                                    <input type="text" name="kota" class="form-control" value="{{ old('kota', $data->kota ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal <small class="text-muted">(opsional)</small></label>
                                    <input type="date" name="tanggal_lokasi" class="form-control"
                                           value="{{ old('tanggal_lokasi', isset($data) && $data->tanggal_lokasi ? \Carbon\Carbon::parse($data->tanggal_lokasi)->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Speaker <small class="text-muted">(opsional)</small></label>
                                    <input type="text" name="speaker" class="form-control" value="{{ old('speaker', $data->speaker ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="urutan" class="form-control" min="0" value="{{ old('urutan', $data->urutan ?? 0) }}">
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_popup" value="1" class="form-check-input" id="is-popup"
                                       {{ old('is_popup', $data->is_popup ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is-popup">Tampilkan sebagai popup saat web dibuka</label>
                                <div class="form-text">Mengaktifkan ini akan menonaktifkan popup pengumuman lain.</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan</button>
                                <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-secondary">Batal</a>
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
