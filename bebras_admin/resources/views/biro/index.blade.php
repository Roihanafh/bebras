@extends('app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen /</span> Biro & Koordinator
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">Daftar Biro Bebras</h5>
                <p class="text-muted small mb-0">
                    Daftar akun pengguna dengan role <strong>biro</strong>. Akun dan role dikelola melalui <a href="{{ route('register') }}" class="text-primary fw-semibold">Manajemen Akun</a>, sedangkan kode biro dan relasi koordinator dikelola di menu ini.
                </p>
            </div>
        </div>

<style>
    #tableBiro thead th,
    #tableBiro thead.table-dark th {
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
                <table class="table table-bordered table-striped" id="tableBiro" style="width:100%">
                    <thead class="table-dark" style="background-color: #1e293b;">
                        <tr>
                            <th class="text-white" style="width: 50px; color: #fff !important;">#</th>
                            <th class="text-white" style="color: #fff !important;">Nama Biro</th>
                            <th class="text-white" style="width: 120px; color: #fff !important;">Kode Biro</th>
                            <th class="text-white" style="color: #fff !important;">Email</th>
                            <th class="text-white" style="width: 160px; color: #fff !important;">Jumlah Koordinator</th>
                            <th class="text-white" style="width: 200px; color: #fff !important;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola Koordinator -->
<div class="modal fade" id="modalKoordinator" tabindex="-1" aria-labelledby="modalKoordinatorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalKoordinatorLabel">Kelola Koordinator Biro</h5>
                    <p class="text-muted small mb-0" id="modalBiroInfo"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Tambah / Edit Koordinator -->
                <div class="card bg-light border mb-4">
                    <div class="card-body py-3">
                        <h6 class="fw-bold mb-3" id="formKoordinatorTitle">Tambah Koordinator</h6>
                        <form id="formKoordinator">
                            @csrf
                            <input type="hidden" id="koordinator_id" name="koordinator_id" value="">
                            <input type="hidden" id="biro_user_id" name="biro_user_id" value="">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label">Nama Koordinator <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="koordinator_nama" name="nama" placeholder="Contoh: Budi Santoso" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Email Koordinator <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-sm" id="koordinator_email" name="email" placeholder="contoh: budi@example.com" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="w-100">
                                        <button type="submit" class="btn btn-sm btn-primary w-100" id="btnSubmitKoordinator">
                                            <i class="bx bx-plus"></i> Simpan
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-1 d-none" id="btnCancelEditKoordinator">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Koordinator Biro -->
                <h6 class="fw-bold mb-2">Daftar Koordinator Terdaftar</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover" id="tableListKoordinator">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyKoordinator">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Memuat data koordinator...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Kode Biro -->
<div class="modal fade" id="modalEditKode" tabindex="-1" aria-labelledby="modalEditKodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditKodeLabel">Edit Kode Biro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKode">
                @csrf
                <input type="hidden" id="edit_kode_biro_id" name="biro_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" id="labelEditKodeBiro">Nama Biro</label>
                        <input type="text" class="form-control" id="input_kode_biro" name="kode_biro" maxlength="3" placeholder="Contoh: 001">
                        <div class="form-text">Kode biro berupa 3 digit string (misal: 001, 002). Kosongkan jika belum ada.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveKode">Simpan Kode</button>
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

    const tableBiro = $('#tableBiro').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('biro.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'kode_biro_display', name: 'kode_biro' },
            { data: 'email', name: 'email' },
            { data: 'jumlah_koordinator', name: 'jumlah_koordinator', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });

    // Buka Modal Edit Kode Biro
    $(document).on('click', '.btn-edit-kode', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const kode = $(this).data('kode');

        $('#edit_kode_biro_id').val(id);
        $('#labelEditKodeBiro').text('Kode Biro untuk ' + name);
        $('#input_kode_biro').val(kode);
        $('#modalEditKode').modal('show');
    });

    // Submit Edit Kode Biro
    $('#formEditKode').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_kode_biro_id').val();
        const kode = $('#input_kode_biro').val();
        const btn = $('#btnSaveKode');

        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ url('/biro') }}/" + id + "/kode",
            type: "PUT",
            data: {
                kode_biro: kode
            },
            success: function(res) {
                btn.prop('disabled', false).text('Simpan Kode');
                $('#modalEditKode').modal('hide');
                tableBiro.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Kode biro berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan Kode');
                let msg = 'Terjadi kesalahan saat menyimpan kode biro.';
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

    // Helper untuk memuat data koordinator ke modal
    function loadKoordinator(biroId) {
        $('#tbodyKoordinator').html('<tr><td colspan="4" class="text-center text-muted py-3">Memuat data koordinator...</td></tr>');
        
        $.ajax({
            url: "{{ url('/biro') }}/" + biroId + "/koordinator",
            type: "GET",
            success: function(res) {
                const biro = res.biro;
                const list = res.koordinator;

                let kodeText = biro.kode_biro ? `(Kode: ${biro.kode_biro})` : '(Kode: -)';
                $('#modalBiroInfo').text(`${biro.name} ${kodeText} — ${biro.email}`);

                if (!list || list.length === 0) {
                    $('#tbodyKoordinator').html('<tr><td colspan="4" class="text-center text-muted py-3">Belum ada koordinator untuk biro ini. Tambahkan melalui form di atas.</td></tr>');
                    return;
                }

                let rows = '';
                list.forEach(function(item, index) {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><span class="fw-semibold">${escapeHtml(item.nama)}</span></td>
                            <td><a href="mailto:${escapeHtml(item.email)}">${escapeHtml(item.email)}</a></td>
                            <td>
                                <button type="button" class="btn btn-xs btn-warning btn-edit-koordinator me-1" 
                                    data-id="${item.id}" 
                                    data-nama="${escapeHtml(item.nama)}" 
                                    data-email="${escapeHtml(item.email)}">
                                    <i class="bx bx-edit-alt"></i> Edit
                                </button>
                                <button type="button" class="btn btn-xs btn-danger btn-delete-koordinator" 
                                    data-id="${item.id}" 
                                    data-nama="${escapeHtml(item.nama)}">
                                    <i class="bx bx-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#tbodyKoordinator').html(rows);
            },
            error: function() {
                $('#tbodyKoordinator').html('<tr><td colspan="4" class="text-center text-danger py-3">Gagal memuat data koordinator.</td></tr>');
            }
        });
    }

    function resetKoordinatorForm() {
        $('#koordinator_id').val('');
        $('#koordinator_nama').val('');
        $('#koordinator_email').val('');
        $('#formKoordinatorTitle').text('Tambah Koordinator');
        $('#btnSubmitKoordinator').html('<i class="bx bx-plus"></i> Simpan');
        $('#btnCancelEditKoordinator').addClass('d-none');
    }

    // Buka Modal Kelola Koordinator
    $(document).on('click', '.btn-manage-koordinator', function() {
        const biroId = $(this).data('id');
        const biroName = $(this).data('name');

        $('#biro_user_id').val(biroId);
        resetKoordinatorForm();
        loadKoordinator(biroId);

        $('#modalKoordinator').modal('show');
    });

    // Form Submit (Tambah atau Update Koordinator)
    $('#formKoordinator').on('submit', function(e) {
        e.preventDefault();
        const biroId = $('#biro_user_id').val();
        const koordinatorId = $('#koordinator_id').val();
        const nama = $('#koordinator_nama').val();
        const email = $('#koordinator_email').val();
        const btn = $('#btnSubmitKoordinator');

        btn.prop('disabled', true);

        const isEdit = Boolean(koordinatorId);
        const url = isEdit 
            ? "{{ url('/biro/koordinator') }}/" + koordinatorId 
            : "{{ url('/biro') }}/" + biroId + "/koordinator";
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
                resetKoordinatorForm();
                loadKoordinator(biroId);
                tableBiro.ajax.reload(null, false);
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
                let msg = 'Terjadi kesalahan.';
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

    // Klik Edit Koordinator
    $(document).on('click', '.btn-edit-koordinator', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const email = $(this).data('email');

        $('#koordinator_id').val(id);
        $('#koordinator_nama').val(nama);
        $('#koordinator_email').val(email);

        $('#formKoordinatorTitle').text('Ubah Data Koordinator');
        $('#btnSubmitKoordinator').html('<i class="bx bx-check"></i> Perbarui');
        $('#btnCancelEditKoordinator').removeClass('d-none');
        $('#koordinator_nama').focus();
    });

    // Batal Edit Koordinator
    $('#btnCancelEditKoordinator').on('click', function() {
        resetKoordinatorForm();
    });

    // Hapus Koordinator
    $(document).on('click', '.btn-delete-koordinator', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const biroId = $('#biro_user_id').val();

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
                    url: "{{ url('/biro/koordinator') }}/" + id,
                    type: "DELETE",
                    success: function(res) {
                        loadKoordinator(biroId);
                        tableBiro.ajax.reload(null, false);
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

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
@endpush
