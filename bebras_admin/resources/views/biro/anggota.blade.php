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
                    <div class="d-flex gap-3 text-center">
                        <div class="px-3 py-2 bg-light rounded">
                            <span class="text-muted d-block small">Total Anggota</span>
                            <h4 class="mb-0 fw-bold text-dark">{{ $totalAll }}</h4>
                        </div>
                        <div class="px-3 py-2 bg-label-warning rounded">
                            <span class="text-warning d-block small fw-semibold">Pending</span>
                            <h4 class="mb-0 fw-bold text-warning">{{ $totalPending }}</h4>
                        </div>
                        <div class="px-3 py-2 bg-label-success rounded">
                            <span class="text-success d-block small fw-semibold">Disetujui</span>
                            <h4 class="mb-0 fw-bold text-success">{{ $totalApproved }}</h4>
                        </div>
                        <div class="px-3 py-2 bg-label-danger rounded">
                            <span class="text-danger d-block small fw-semibold">Ditolak</span>
                            <h4 class="mb-0 fw-bold text-danger">{{ $totalRejected }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Pengajuan Anggota --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1 fw-bold">Daftar Anggota Biro Saya</h5>
                    <p class="text-muted small mb-0">
                        Ajukan data anggota biro Anda. Setiap anggota baru akan berstatus <strong>Pending</strong> sampai divalidasi dan disetujui oleh Admin Bebras.
                    </p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="btnOpenCreateModal">
                    <i class="bx bx-plus me-1"></i> Ajukan Anggota Baru
                </button>
            </div>
<style>
    #tableAnggotaSaya thead th,
    #tableAnggotaSaya thead.table-dark th {
        color: #ffffff !important;
        background-color: #1e293b !important;
        border-color: #334155 !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.6px !important;
        text-transform: uppercase !important;
    }
    #tableAnggotaSaya thead th.sorting:before,
    #tableAnggotaSaya thead th.sorting:after,
    #tableAnggotaSaya thead th.sorting_asc:before,
    #tableAnggotaSaya thead th.sorting_asc:after,
    #tableAnggotaSaya thead th.sorting_desc:before,
    #tableAnggotaSaya thead th.sorting_desc:after {
        color: #ffffff !important;
        opacity: 0.65 !important;
    }
    #tableAnggotaSaya thead th.sorting_asc:after,
    #tableAnggotaSaya thead th.sorting_desc:before {
        color: #38bdf8 !important;
        opacity: 1 !important;
    }
</style>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped" id="tableAnggotaSaya" style="width:100%">
                        <thead class="table-dark" style="background-color: #1e293b;">
                            <tr>
                                <th class="text-white" style="width: 50px; color: #fff !important;">#</th>
                                <th class="text-white" style="width: 100px; color: #fff !important;">No. Anggota</th>
                                <th class="text-white" style="color: #fff !important;">Nama Anggota</th>
                                <th class="text-white" style="width: 120px; color: #fff !important;">Tahun Masuk</th>
                                <th class="text-white" style="width: 140px; color: #fff !important;">Tanggal Lahir</th>
                                <th class="text-white" style="width: 160px; color: #fff !important;">Status Validasi</th>
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

<!-- Modal Tambah / Edit Anggota Biro -->
<div class="modal fade" id="modalFormAnggota" tabindex="-1" aria-labelledby="modalFormAnggotaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalFormAnggotaLabel">Ajukan Anggota Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAnggotaSaya">
                @csrf
                <input type="hidden" id="anggota_id" name="anggota_id" value="">
                <div class="modal-body">
                    {{-- Alert Info Pengajuan --}}
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bx bx-info-circle me-1"></i> Data anggota yang diajukan akan berstatus <strong>Pending</strong> hingga disetujui Admin.
                    </div>

                    {{-- No. Anggota --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="modal_no_anggota">No. Anggota (3 Digit) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" id="modal_no_anggota" name="no_anggota" maxlength="3" placeholder="Contoh: {{ $nextNo }}" value="{{ $nextNo }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnRefreshNextNo" title="Gunakan Nomor Otomatis Berikutnya">
                                <i class="bx bx-refresh me-1"></i> Auto
                            </button>
                        </div>
                        <div class="form-text">Nomor 3 digit unik untuk biro Anda (001, 002, dst). Anda dapat mengubahnya selama belum digunakan di biro ini.</div>
                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="modal_nama">Nama Lengkap Anggota <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_nama" name="nama" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    <div class="row g-2 mb-3">
                        {{-- Tahun Masuk --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="modal_tahun_masuk">Tahun Masuk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="modal_tahun_masuk" name="tahun_masuk" min="1900" max="2100" value="{{ date('Y') }}" required>
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="modal_tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="modal_tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitForm">
                        <i class="bx bx-paper-plane me-1"></i> Kirim Pengajuan
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

    const defaultNextNo = "{{ $nextNo }}";

    const tableAnggota = $('#tableAnggotaSaya').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('biro_anggota.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'no_anggota_display', name: 'no_anggota' },
            { data: 'nama', name: 'nama' },
            { data: 'tahun_masuk', name: 'tahun_masuk' },
            { data: 'tanggal_lahir_display', name: 'tanggal_lahir' },
            { data: 'status_display', name: 'status_validasi' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Belum ada anggota biro yang diajukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Buka Modal Tambah
    $('#btnOpenCreateModal').on('click', function() {
        $('#formAnggotaSaya')[0].reset();
        $('#anggota_id').val('');
        $('#modalFormAnggotaLabel').text('Ajukan Anggota Baru');
        $('#btnSubmitForm').html('<i class="bx bx-paper-plane me-1"></i> Kirim Pengajuan');

        // Ambil nomor anggota berikutnya terbaru
        fetchNextNo();
        $('#modalFormAnggota').modal('show');
    });

    $('#btnRefreshNextNo').on('click', function() {
        fetchNextNo();
    });

    function fetchNextNo() {
        $.ajax({
            url: "{{ route('biro_anggota.next_no') }}",
            type: "GET",
            success: function(res) {
                if (res.status === 'success') {
                    $('#modal_no_anggota').val(res.next_no);
                }
            }
        });
    }

    // Submit Form (Tambah / Edit)
    $('#formAnggotaSaya').on('submit', function(e) {
        e.preventDefault();
        const id = $('#anggota_id').val();
        const isEdit = Boolean(id);
        const url = isEdit
            ? "{{ url('/anggota-biro') }}/" + id
            : "{{ route('biro_anggota.store') }}";
        const method = isEdit ? "PUT" : "POST";
        const btn = $('#btnSubmitForm');

        btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: method,
            data: {
                nama: $('#modal_nama').val(),
                tahun_masuk: $('#modal_tahun_masuk').val(),
                tanggal_lahir: $('#modal_tanggal_lahir').val(),
                no_anggota: $('#modal_no_anggota').val(),
            },
            success: function(res) {
                btn.prop('disabled', false);
                $('#modalFormAnggota').modal('hide');
                tableAnggota.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Pengajuan anggota berhasil disimpan.',
                    timer: 2000,
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

    // Buka Modal Edit
    $(document).on('click', '.btn-edit-anggota', function() {
        const id = $(this).data('id');

        $.ajax({
            url: "{{ url('/anggota-biro') }}/" + id + "/edit",
            type: "GET",
            success: function(res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#anggota_id').val(data.id);
                    $('#modal_nama').val(data.nama);
                    $('#modal_tahun_masuk').val(data.tahun_masuk);
                    $('#modal_tanggal_lahir').val(data.tanggal_lahir);
                    $('#modal_no_anggota').val(data.no_anggota);

                    $('#modalFormAnggotaLabel').text('Edit Anggota Biro');
                    $('#btnSubmitForm').html('<i class="bx bx-check me-1"></i> Simpan Perubahan');
                    $('#modalFormAnggota').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data anggota.'
                });
            }
        });
    });

    // Hapus Anggota
    $(document).on('click', '.btn-delete-anggota', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Hapus Anggota?',
            text: `Yakin ingin menghapus anggota "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/anggota-biro') }}/" + id,
                    type: "DELETE",
                    success: function(res) {
                        tableAnggota.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: res.message || 'Anggota berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menghapus anggota.';
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
