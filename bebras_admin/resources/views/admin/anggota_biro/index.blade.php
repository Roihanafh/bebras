@extends('app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-breadcrumbs :items="$breadcrumbs" />

        {{-- Kartu Ringkasan Status --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Total Anggota</span>
                                <h3 class="mb-0 fw-bold text-primary mt-1" id="statTotalAll">{{ $totalAll }}</h3>
                            </div>
                            <div class="avatar avatar-md bg-label-primary rounded p-2">
                                <i class="bx bx-group fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-warning border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Menunggu Validasi</span>
                                <h3 class="mb-0 fw-bold text-warning mt-1" id="statTotalPending">{{ $totalPending }}</h3>
                            </div>
                            <div class="avatar avatar-md bg-label-warning rounded p-2">
                                <i class="bx bx-time-five fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-success border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Disetujui</span>
                                <h3 class="mb-0 fw-bold text-success mt-1" id="statTotalApproved">{{ $totalApproved }}</h3>
                            </div>
                            <div class="avatar avatar-md bg-label-success rounded p-2">
                                <i class="bx bx-check-circle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-danger border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Ditolak</span>
                                <h3 class="mb-0 fw-bold text-danger mt-1" id="statTotalRejected">{{ $totalRejected }}</h3>
                            </div>
                            <div class="avatar avatar-md bg-label-danger rounded p-2">
                                <i class="bx bx-x-circle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Utama Pengelolaan Anggota Biro --}}
        <div class="card shadow-sm">
            <div class="card-header pb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1 fw-bold"><i class="bx bx-id-card me-2 text-primary"></i>Manajemen Anggota Biro</h5>
                        <p class="text-muted small mb-0">
                            Pusat validasi dan pengelolaan data seluruh anggota dari semua biro Bebras.
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnOpenCreateModal">
                        <i class="bx bx-plus me-1"></i> Tambah Anggota
                    </button>
                </div>

                {{-- Filter Section --}}
                <div class="row g-2 mt-3 pt-3 border-top">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Filter Berdasarkan Biro:</label>
                        <select class="form-select form-select-sm" id="filter_biro_id">
                            <option value="">-- Semua Biro --</option>
                            @foreach($biros as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} {{ $b->kode_biro ? '(' . $b->kode_biro . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">Filter Status Validasi:</label>
                        <select class="form-select form-select-sm" id="filter_status_validasi">
                            <option value="">-- Semua Status --</option>
                            <option value="pending">Pending (Menunggu Validasi)</option>
                            <option value="approved">Disetujui (Approved)</option>
                            <option value="rejected">Ditolak (Rejected)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="btnResetFilter">
                            <i class="bx bx-reset me-1"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>

<style>
    #tableAnggotaBiro thead th,
    #tableAnggotaBiro thead.table-dark th {
        color: #ffffff !important;
        background-color: #1e293b !important;
        border-color: #334155 !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.6px !important;
        text-transform: uppercase !important;
    }
    #tableAnggotaBiro thead th.sorting:before,
    #tableAnggotaBiro thead th.sorting:after,
    #tableAnggotaBiro thead th.sorting_asc:before,
    #tableAnggotaBiro thead th.sorting_asc:after,
    #tableAnggotaBiro thead th.sorting_desc:before,
    #tableAnggotaBiro thead th.sorting_desc:after {
        color: #ffffff !important;
        opacity: 0.65 !important;
    }
    #tableAnggotaBiro thead th.sorting_asc:after,
    #tableAnggotaBiro thead th.sorting_desc:before {
        color: #38bdf8 !important;
        opacity: 1 !important;
    }
</style>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped" id="tableAnggotaBiro" style="width:100%">
                        <thead class="table-dark" style="background-color: #1e293b;">
                            <tr>
                                <th class="text-white" style="width: 40px; color: #fff !important;">#</th>
                                <th class="text-white" style="width: 100px; color: #fff !important;">No. Anggota</th>
                                <th class="text-white" style="color: #fff !important;">Nama Anggota</th>
                                <th class="text-white" style="color: #fff !important;">Biro Asal</th>
                                <th class="text-white" style="width: 110px; color: #fff !important;">Tahun Masuk</th>
                                <th class="text-white" style="width: 120px; color: #fff !important;">Tanggal Lahir</th>
                                <th class="text-white" style="width: 140px; color: #fff !important;">Status</th>
                                <th class="text-white" style="width: 170px; color: #fff !important;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Anggota -->
<div class="modal fade" id="modalFormAnggota" tabindex="-1" aria-labelledby="modalFormAnggotaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalFormAnggotaLabel">Tambah Anggota Biro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAnggotaAdmin">
                @csrf
                <input type="hidden" id="anggota_id" name="anggota_id" value="">
                <div class="modal-body">
                    {{-- Pilih Biro --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="modal_biro_id">Pilih Biro <span class="text-danger">*</span></label>
                        <select class="form-select" id="modal_biro_id" name="biro_id" required>
                            <option value="">-- Pilih Biro --</option>
                            @foreach($biros as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} {{ $b->kode_biro ? '(' . $b->kode_biro . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- No. Anggota --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="modal_no_anggota">No. Anggota (3 Digit) <span class="text-muted small">(Unik per Biro)</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" id="modal_no_anggota" name="no_anggota" maxlength="3" placeholder="Contoh: 001">
                            <button class="btn btn-outline-secondary" type="button" id="btnAutoNoAnggota" title="Generate Nomor Berikutnya">
                                <i class="bx bx-refresh me-1"></i> Auto
                            </button>
                        </div>
                        <div class="form-text">Format 3 digit angka (001, 002, dst). Kosongkan untuk otomatis menggunakan nomor berikutnya untuk biro terpilih.</div>
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
                            <input type="number" class="form-control" id="modal_tahun_masuk" name="tahun_masuk" min="1900" max="2100" placeholder="{{ date('Y') }}" required>
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="modal_tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="modal_tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                    </div>

                    {{-- Status Validasi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="modal_status_validasi">Status Validasi <span class="text-danger">*</span></label>
                        <select class="form-select" id="modal_status_validasi" name="status_validasi" required>
                            <option value="approved">Disetujui (Approved)</option>
                            <option value="pending">Pending (Menunggu Validasi)</option>
                            <option value="rejected">Ditolak (Rejected)</option>
                        </select>
                        <div class="form-text">Anggota yang disetujui akan ditampilkan pada halaman publik Tentang Bebras Biro.</div>
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

    const tableAnggota = $('#tableAnggotaBiro').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.anggota_biro.list') }}",
            data: function(d) {
                d.biro_id = $('#filter_biro_id').val();
                d.status_validasi = $('#filter_status_validasi').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'no_anggota_display', name: 'no_anggota' },
            { data: 'nama', name: 'nama' },
            { data: 'biro_display', name: 'biro.name' },
            { data: 'tahun_masuk', name: 'tahun_masuk' },
            { data: 'tanggal_lahir_display', name: 'tanggal_lahir' },
            { data: 'status_display', name: 'status_validasi' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data anggota biro ditemukan",
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

    // Event Filter
    $('#filter_biro_id, #filter_status_validasi').on('change', function() {
        tableAnggota.draw();
    });

    $('#btnResetFilter').on('click', function() {
        $('#filter_biro_id').val('');
        $('#filter_status_validasi').val('');
        tableAnggota.draw();
    });

    // Buka Modal Tambah
    $('#btnOpenCreateModal').on('click', function() {
        $('#formAnggotaAdmin')[0].reset();
        $('#anggota_id').val('');
        $('#modal_status_validasi').val('approved');
        $('#modalFormAnggotaLabel').text('Tambah Anggota Biro');
        $('#modalFormAnggota').modal('show');

        // Jika filter biro sudah terpilih, set langsung ke modal
        const currentBiroFilter = $('#filter_biro_id').val();
        if (currentBiroFilter) {
            $('#modal_biro_id').val(currentBiroFilter).trigger('change');
        }
    });

    // Otomatis fetch next no anggota saat biro dipilih pada mode tambah
    $('#modal_biro_id').on('change', function() {
        const biroId = $(this).val();
        const isEdit = Boolean($('#anggota_id').val());

        if (biroId && !isEdit) {
            fetchNextNo(biroId);
        }
    });

    $('#btnAutoNoAnggota').on('click', function() {
        const biroId = $('#modal_biro_id').val();
        if (!biroId) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Biro Dulu',
                text: 'Silakan pilih biro terlebih dahulu untuk menentukan urutan nomor.'
            });
            return;
        }
        fetchNextNo(biroId);
    });

    function fetchNextNo(biroId) {
        $.ajax({
            url: "{{ url('/admin/anggota-biro/next-no') }}/" + biroId,
            type: "GET",
            success: function(res) {
                if (res.status === 'success') {
                    $('#modal_no_anggota').val(res.next_no);
                }
            }
        });
    }

    // Submit Form (Tambah / Edit)
    $('#formAnggotaAdmin').on('submit', function(e) {
        e.preventDefault();
        const id = $('#anggota_id').val();
        const isEdit = Boolean(id);
        const url = isEdit
            ? "{{ url('/admin/anggota-biro') }}/" + id
            : "{{ route('admin.anggota_biro.store') }}";
        const method = isEdit ? "PUT" : "POST";
        const btn = $('#btnSubmitForm');

        btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: method,
            data: {
                biro_id: $('#modal_biro_id').val(),
                nama: $('#modal_nama').val(),
                tahun_masuk: $('#modal_tahun_masuk').val(),
                tanggal_lahir: $('#modal_tanggal_lahir').val(),
                no_anggota: $('#modal_no_anggota').val(),
                status_validasi: $('#modal_status_validasi').val(),
            },
            success: function(res) {
                btn.prop('disabled', false);
                $('#modalFormAnggota').modal('hide');
                tableAnggota.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Data anggota berhasil disimpan.',
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

    // Buka Modal Edit
    $(document).on('click', '.btn-edit-anggota', function() {
        const id = $(this).data('id');

        $.ajax({
            url: "{{ url('/admin/anggota-biro') }}/" + id + "/edit",
            type: "GET",
            success: function(res) {
                if (res.status === 'success') {
                    const data = res.data;
                    $('#anggota_id').val(data.id);
                    $('#modal_biro_id').val(data.biro_id);
                    $('#modal_nama').val(data.nama);
                    $('#modal_tahun_masuk').val(data.tahun_masuk);
                    $('#modal_tanggal_lahir').val(data.tanggal_lahir);
                    $('#modal_no_anggota').val(data.no_anggota);
                    $('#modal_status_validasi').val(data.status_validasi);

                    $('#modalFormAnggotaLabel').text('Edit Anggota Biro (' + (data.biro_name || '') + ')');
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

    // Aksi Setujui (Approve)
    $(document).on('click', '.btn-approve', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Setujui Anggota?',
            text: `Yakin ingin menyetujui pengajuan anggota "${nama}"? Anggota akan tampil di frontend publik.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/admin/anggota-biro') }}/" + id + "/approve",
                    type: "POST",
                    success: function(res) {
                        tableAnggota.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Disetujui!',
                            text: res.message || 'Pengajuan anggota berhasil disetujui.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memproses persetujuan.';
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

    // Aksi Tolak (Reject)
    $(document).on('click', '.btn-reject', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Tolak Anggota?',
            text: `Yakin ingin menolak pengajuan anggota "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/admin/anggota-biro') }}/" + id + "/reject",
                    type: "POST",
                    success: function(res) {
                        tableAnggota.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Ditolak!',
                            text: res.message || 'Pengajuan anggota telah ditolak.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memproses penolakan.';
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

    // Hapus Anggota
    $(document).on('click', '.btn-delete-anggota', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Hapus Anggota?',
            text: `Yakin ingin menghapus data anggota "${nama}"? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/admin/anggota-biro') }}/" + id,
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
