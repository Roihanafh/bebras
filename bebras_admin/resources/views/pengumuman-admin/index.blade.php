@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Manajemen Pengumuman</h5>
                <a href="{{ route('admin.pengumuman.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Tambah Pengumuman
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-pengumuman-admin" class="table table-striped table-borderless border-bottom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Popup</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>
@endsection

@push('js')
<script>
$(function () {
    $('#table-pengumuman-admin').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.pengumuman.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'judul', name: 'judul' },
            { data: 'status_popup', name: 'status_popup', orderable: false, searchable: false },
            { data: 'tanggal', name: 'tanggal', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        order: [[3, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });

    setTimeout(function () { $('.alert').alert('close'); }, 4000);
});
</script>
@endpush
