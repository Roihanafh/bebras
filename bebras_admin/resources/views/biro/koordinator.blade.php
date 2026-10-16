@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        {{-- Info Card Biro Saya --}}
        <div class="card mb-4 border-start border-primary border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary">Akun Biro</span>
                            @if($user->kode_biro)
                                <span class="badge bg-label-info">Kode: {{ $user->kode_biro }}</span>
                            @endif
                        </div>
                        <h4 class="mb-1 text-primary fw-bold">{{ $user->name }}</h4>
                        <p class="text-muted small mb-0">
                            <i class="bx bx-envelope me-1"></i>{{ $user->email }}
                            <span class="mx-2">•</span>
                            <i class="bx bx-user me-1"></i>{{ $user->username }}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="text-muted d-block small">Total Koordinator</span>
                        <h3 class="mb-0 fw-bold text-dark" id="displayTotalKoordinator">{{ $totalKoordinator }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Koordinator --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Koordinator Biro</h5>
                    <p class="text-muted small mb-0">
                        Kelola data koordinator di bawah naungan biro Anda. Data ini akan ditampilkan pada halaman publik <strong>Tentang Bebras Biro</strong>.
                    </p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="btnOpenCreateModal">
                    <i class="bx bx-plus me-1"></i> Tambah Koordinator
                </button>
            </div>
<style>
    #tableKoordinatorSaya thead th,
    #tableKoordinatorSaya thead.table-dark th {
        color: #ffffff !important;
        background-color: #1e293b !important;
        border-color: #334155 !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.6px !important;
        text-transform: uppercase !important;
    }
</style>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped" id="tableKoordinatorSaya" style="width:100%">
                        <thead class="table-dark" style="background-color: #1e293b;">
                            <tr>
                                <th class="text-white" style="width: 50px; color: #fff !important;">#</th>
                                <th class="text-white" style="color: #fff !important;">Nama Koordinator</th>
                                <th class="text-white" style="color: #fff !important;">Email Koordinator</th>
                                <th class="text-white" style="width: 160px; color: #fff !important;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Koordinator -->
<div class="modal fade" id="modalFormKoordinator" tabindex="-1" aria-labelledby="modalFormKoordinatorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormKoordinatorLabel">Tambah Koordinator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formKoordinatorSaya">
                @csrf
                <input type="hidden" id="koordinator_id" name="koordinator_id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="koordinator_nama">Nama Koordinator <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="koordinator_nama" name="nama" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="koordinator_email">Email Koordinator <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="koordinator_email" name="email" placeholder="Contoh: budi@example.com" required>
                        <div class="form-text">Email ini akan digunakan sebagai tautan kontak (mailto) pada halaman publik.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitForm">
                        <i class="bx bx-check me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const tableKoordinator = $('#tableKoordinatorSaya').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('biro_koordinator.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama', name: 'nama' },
            { data: 'email_display', name: 'email' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        drawCallback: function(settings) {
            const api = this.api();
            const total = api.rows().count();
            // Update counter ringkasan jika data berubah
            $('#displayTotalKoordinator').text(settings.json ? settings.json.recordsTotal : total);
        }
    });

    // Buka Modal Tambah
    $('#btnOpenCreateModal').on('click', function() {
        $('#modalFormKoordinatorLabel').text('Tambah Koordinator');
        $('#koordinator_id').val('');
        $('#koordinator_nama').val('');
        $('#koordinator_email').val('');
        $('#btnSubmitForm').html('<i class="bx bx-check me-1"></i> Simpan');
        $('#modalFormKoordinator').modal('show');
    });

    // Buka Modal Edit
    $(document).on('click', '.btn-edit-koordinator', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const email = $(this).data('email');

        $('#modalFormKoordinatorLabel').text('Ubah Data Koordinator');
        $('#koordinator_id').val(id);
        $('#koordinator_nama').val(nama);
        $('#koordinator_email').val(email);
        $('#btnSubmitForm').html('<i class="bx bx-check me-1"></i> Perbarui');
        $('#modalFormKoordinator').modal('show');
    });

    // Submit Form (Tambah / Update)
    $('#formKoordinatorSaya').on('submit', function(e) {
        e.preventDefault();
        const id = $('#koordinator_id').val();
        const nama = $('#koordinator_nama').val();
        const email = $('#koordinator_email').val();
        const btn = $('#btnSubmitForm');

        btn.prop('disabled', true);

        const isEdit = Boolean(id);
        const url = isEdit
            ? "{{ url('/koordinator') }}/" + id
            : "{{ route('biro_koordinator.store') }}";
        const method = isEdit ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: method,
            data: {
                nama: nama,
                email: email
            },
            success: function(res) {
                btn.prop('disabled', false);
                $('#modalFormKoordinator').modal('hide');
                tableKoordinator.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Data koordinator berhasil disimpan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                let msg = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: msg
                });
            }
        });
    });

    // Hapus Koordinator
    $(document).on('click', '.btn-delete-koordinator', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Hapus Koordinator?',
            text: `Yakin ingin menghapus koordinator "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/koordinator') }}/" + id,
                    type: "DELETE",
                    success: function(res) {
                        tableKoordinator.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: res.message || 'Koordinator berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menghapus koordinator.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
