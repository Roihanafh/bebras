@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Statistik Cards --}}
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-warning h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-25"
                             style="width:52px;height:52px;flex-shrink:0;">
                            <i class="bx bx-time-five fs-4 text-warning"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Menunggu Persetujuan</p>
                            <h4 class="mb-0 fw-bold">{{ $totalPending }}</h4>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-25"
                             style="width:52px;height:52px;flex-shrink:0;">
                            <i class="bx bx-check-circle fs-4 text-success"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Disetujui</p>
                            <h4 class="mb-0 fw-bold">{{ $totalApproved }}</h4>
                            <span class="badge bg-success">Approved</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-danger h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-25"
                             style="width:52px;height:52px;flex-shrink:0;">
                            <i class="bx bx-x-circle fs-4 text-danger"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Ditolak</p>
                            <h4 class="mb-0 fw-bold">{{ $totalRejected }}</h4>
                            <span class="badge bg-danger">Rejected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Berita --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Berita Saya</h5>
                        <a href="{{ route('berita.create') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i> Tambah Berita
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-berita" class="table table-striped table-borderless border-bottom">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Judul</th>
                                        <th>Menu Kegiatan</th>
                                        <th>Status</th>
                                        <th>Catatan Admin</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
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
$(document).ready(function () {
    $('#table-berita').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('berita.list') }}",
        columns: [
            { data: 'DT_RowIndex',      name: 'DT_RowIndex',      orderable: false, searchable: false },
            { data: 'judul',            name: 'judul' },
            { data: 'menu_kegiatan',    name: 'menu_kegiatan',    orderable: false, searchable: false },
            { data: 'status_validasi',  name: 'status_validasi',  orderable: false, searchable: false },
            { data: 'catatan_admin',    name: 'catatan_admin',    orderable: false, searchable: false },
            { data: 'tanggal',          name: 'tanggal',          orderable: false, searchable: false },
            { data: 'aksi',             name: 'aksi',             orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });

    setTimeout(function () { $('.alert').alert('close'); }, 4000);
});
</script>
@endpush
