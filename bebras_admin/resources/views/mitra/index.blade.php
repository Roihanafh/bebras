@extends('app')

@section('content')
    <div class="col-12">
        <x-breadcrumbs :items="$breadCrumbs" />
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mitra</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahMitraModal">
                    <i class="bx bx-plus me-1"></i> Tambah Mitra
                </button>
            </div>
            <div class="card-body">
                {{-- Alert Box --}}
                <div id="alert-box" class="alert mb-3" role="alert" style="display:none;"></div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="table-mitra">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Logo</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- data dimuat via DataTables AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- =========================================================
         Modal Tambah Mitra
    ========================================================= --}}
    <div class="modal fade" id="tambahMitraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahMitra" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="tambah_nama" class="form-control"
                                placeholder="Masukkan nama mitra" required>
                            <div class="invalid-feedback" id="error-tambah-nama"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="urutan" id="tambah_urutan" class="form-control"
                                placeholder="0" min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" id="tambah_logo" class="form-control" accept="image/*">
                            <div class="form-text">Format: JPG, JPEG, PNG. Maks: 2 MB.</div>
                            <div class="invalid-feedback" id="error-tambah-logo"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanMitra">Simpan</button>
                </div>
            </div>
        </div>
    </div>


    {{-- =========================================================
         Modal Edit Mitra
    ========================================================= --}}
    <div class="modal fade" id="editMitraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditMitra" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="editMitraId">

                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control"
                                placeholder="Masukkan nama mitra" required>
                            <div class="invalid-feedback" id="error-edit-nama"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="urutan" id="edit_urutan" class="form-control"
                                placeholder="0" min="0">
                        </div>

                        <div class="row">
                            {{-- Logo Saat Ini --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <strong>Logo Saat Ini</strong>
                                    </div>
                                    <div class="card-body text-center">
                                        <img id="edit_preview_logo" src="" alt="Logo Mitra"
                                            class="img-fluid rounded mb-2 border"
                                            style="max-height: 100px; display: none;">
                                        <p class="text-muted small" id="edit_no_logo_text">Belum ada logo</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Ganti Logo --}}
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <strong>Ganti / Tambah Logo</strong>
                                    </div>
                                    <div class="card-body">
                                        <input type="file" name="logo" id="edit_logo" class="form-control"
                                            accept="image/*">
                                        <div class="form-text">Format: JPG, JPEG, PNG. Maks: 2 MB.</div>
                                        <div class="invalid-feedback" id="error-edit-logo"></div>
                                        <div class="mt-3 text-center">
                                            <img id="edit_preview_new" class="img-fluid rounded border"
                                                style="max-height: 100px; display: none;" alt="Preview logo baru">
                                            <p class="text-muted small">Preview logo baru</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnUpdateMitra">Perbarui</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
$(document).ready(function () {

    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // =========================================================
    // Inisialisasi DataTables
    // =========================================================
    var table = $('#table-mitra').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("mitra.list") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama',        name: 'nama' },
            { data: 'logo',        name: 'logo', orderable: false, searchable: false },
            { data: 'aksi',        name: 'aksi', orderable: false, searchable: false },
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });

    // =========================================================
    // Helper: tampilkan / sembunyikan alert box
    // =========================================================
    function showAlert(message, type) {
        var box = $('#alert-box');
        box.removeClass('alert-success alert-danger')
           .addClass('alert-' + type)
           .text(message)
           .show();
        setTimeout(function () { box.fadeOut(); }, 4000);
    }

    // =========================================================
    // Helper: reset validasi field dalam form
    // =========================================================
    function clearValidation(prefix) {
        $('#' + prefix + ' .form-control').removeClass('is-invalid');
        $('#' + prefix + ' .invalid-feedback').text('');
    }

    function showValidationErrors(errors, prefix) {
        $.each(errors, function (field, messages) {
            $('#' + prefix + '_' + field).addClass('is-invalid');
            $('#error-' + prefix + '-' + field).text(messages[0]);
        });
    }

    // =========================================================
    // Reset form tambah ketika modal ditutup
    // =========================================================
    $('#tambahMitraModal').on('hidden.bs.modal', function () {
        document.getElementById('formTambahMitra').reset();
        clearValidation('formTambahMitra');
    });

    // =========================================================
    // Reset form edit ketika modal ditutup
    // =========================================================
    $('#editMitraModal').on('hidden.bs.modal', function () {
        document.getElementById('formEditMitra').reset();
        clearValidation('formEditMitra');
        $('#edit_preview_logo').hide();
        $('#edit_no_logo_text').show();
        $('#edit_preview_new').hide();
    });

    // =========================================================
    // Preview logo baru di modal edit
    // =========================================================
    $('#edit_logo').on('change', function () {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#edit_preview_new').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#edit_preview_new').hide();
        }
    });

    // =========================================================
    // Simpan: POST /mitra/store
    // =========================================================
    $('#btnSimpanMitra').on('click', function () {
        clearValidation('formTambahMitra');

        var formData = new FormData(document.getElementById('formTambahMitra'));
        var btn = $(this);
        btn.text('Menyimpan...').prop('disabled', true);

        fetch('{{ route("mitra.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { status: response.status, body: data };
            });
        })
        .then(function (result) {
            if (result.status === 200 && result.body.status === 'success') {
                bootstrap.Modal.getInstance(document.getElementById('tambahMitraModal')).hide();
                table.ajax.reload(null, false);
                showAlert(result.body.message, 'success');
            } else if (result.status === 422 && result.body.errors) {
                showValidationErrors(result.body.errors, 'tambah');
            } else {
                showAlert(result.body.message ?? 'Terjadi kesalahan.', 'danger');
            }
        })
        .catch(function () {
            showAlert('Gagal menghubungi server.', 'danger');
        })
        .finally(function () {
            btn.text('Simpan').prop('disabled', false);
        });
    });

    // =========================================================
    // Buka modal edit: GET /mitra/{id}/edit
    // =========================================================
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');

        clearValidation('formEditMitra');
        $('#edit_preview_logo').hide();
        $('#edit_no_logo_text').show();
        $('#edit_preview_new').hide();
        document.getElementById('formEditMitra').reset();

        fetch('{{ url("mitra") }}/' + id + '/edit', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(function (response) { return response.json(); })
        .then(function (json) {
            if (json.status === 'success') {
                var data = json.data;
                $('#editMitraId').val(data.id);
                $('#edit_nama').val(data.nama);
                $('#edit_urutan').val(data.urutan ?? 0);

                if (data.logo) {
                    $('#edit_preview_logo')
                        .attr('src', '{{ asset("storage") }}/' + data.logo)
                        .show();
                    $('#edit_no_logo_text').hide();
                }

                new bootstrap.Modal(document.getElementById('editMitraModal')).show();
            } else {
                showAlert(json.message ?? 'Data tidak ditemukan.', 'danger');
            }
        })
        .catch(function () {
            showAlert('Gagal memuat data.', 'danger');
        });
    });

    // =========================================================
    // Perbarui: POST /mitra/{id}/update
    // =========================================================
    $('#btnUpdateMitra').on('click', function () {
        clearValidation('formEditMitra');

        var id = $('#editMitraId').val();
        var formData = new FormData(document.getElementById('formEditMitra'));
        var btn = $(this);
        btn.text('Menyimpan...').prop('disabled', true);

        fetch('{{ url("mitra") }}/' + id + '/update', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { status: response.status, body: data };
            });
        })
        .then(function (result) {
            if (result.status === 200 && result.body.status === 'success') {
                bootstrap.Modal.getInstance(document.getElementById('editMitraModal')).hide();
                table.ajax.reload(null, false);
                showAlert(result.body.message, 'success');
            } else if (result.status === 422 && result.body.errors) {
                showValidationErrors(result.body.errors, 'edit');
            } else {
                showAlert(result.body.message ?? 'Terjadi kesalahan.', 'danger');
            }
        })
        .catch(function () {
            showAlert('Gagal menghubungi server.', 'danger');
        })
        .finally(function () {
            btn.text('Perbarui').prop('disabled', false);
        });
    });

    // =========================================================
    // Hapus: DELETE /mitra/{id}
    // =========================================================
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Mitra?',
            text: 'Data yang sudah dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                fetch('{{ url("mitra") }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                .then(function (response) { return response.json(); })
                .then(function (json) {
                    if (json.status === 'success') {
                        table.ajax.reload(null, false);
                        showAlert(json.message, 'success');
                    } else {
                        showAlert(json.message ?? 'Terjadi kesalahan.', 'danger');
                    }
                })
                .catch(function () {
                    showAlert('Gagal menghubungi server.', 'danger');
                });
            }
        });
    });

});
</script>
@endpush
