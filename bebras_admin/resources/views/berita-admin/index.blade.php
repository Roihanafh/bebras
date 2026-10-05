@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        <div class="row">
            <div class="col-md-12">

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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manajemen Berita</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-berita-admin" class="table table-striped table-borderless border-bottom">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Judul</th>
                                        <th>Nama Biro</th>
                                        <th>Status</th>
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
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#table-berita-admin').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.berita.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'judul', name: 'judul' },
            { data: 'nama_biro', name: 'nama_biro', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            { data: 'tanggal', name: 'tanggal', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        order: [[4, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });

    setTimeout(function() { $('.alert').alert('close'); }, 4000);

    // SweetAlert2 — konfirmasi approve / reject / hapus
    $(document).on('click', '.btn-aksi-berita', function () {
        const btn    = $(this);
        const form   = btn.closest('.form-aksi-berita');
        const type   = btn.data('type');
        const msg    = btn.data('msg');

        const config = {
            approve: {
                title: 'Setujui Berita',
                icon: 'question',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Ya, Setujui',
            },
            reject: {
                title: 'Tolak Berita',
                icon: 'warning',
                confirmButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak',
            },
            delete: {
                title: 'Hapus Berita',
                icon: 'warning',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
            },
        }[type] ?? {
            title: 'Konfirmasi',
            icon: 'question',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya',
        };

        Swal.fire({
            title: config.title,
            text: msg,
            icon: config.icon,
            showCancelButton: true,
            confirmButtonColor: config.confirmButtonColor,
            cancelButtonColor: '#adb5bd',
            confirmButtonText: config.confirmButtonText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form[0].submit();
            }
        });
    });
});
</script>
@endpush
