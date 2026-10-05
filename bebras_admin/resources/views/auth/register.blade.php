@extends('app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Forms /</span> Register Akun
        </h4>

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">

                    <!-- Header Card -->
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Form Register Akun</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bx bx-plus"></i> Tambah Akun
                        </button>
                    </div>

                    <!-- Body Card -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="table-akun">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Register Akun -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Tambah Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegister">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="username" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Role</label>
                            <div class="col-sm-10">
                                <select name="roles" class="form-select">
                                    <option value="">-- pilih role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password" />
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@include('admin.edit-akun')
@push('js')
<script>
    $(document).ready(function() {
        let table = $('#table-akun').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
               url: "{{ route('register.get-akun') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'username', name: 'username' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
            ]
        });

       $('#formRegister').on('submit', function (e) {
            e.preventDefault(); 

            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');
            const originalText = $submitButton.text();
            $submitButton.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ route('store.akun') }}",
                method: "POST",
                data: $form.serialize(),
                dataType: "json", 
                success: function (res) {
                    $form[0].reset();
                    const modalEl = document.getElementById('registerModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Akun berhasil ditambahkan!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    if ($.fn.DataTable.isDataTable('#table-akun')) {
                        $('#table-akun').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let msg = "";
                        $.each(errors, function (key, val) {
                            msg += val[0] + "<br>";
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal!',
                            html: msg
                        });
                    } else {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan pada server!'
                        });
                    }
                },
                complete: function () {
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
        
    $("#table-akun").on('click', '.edit', function() {
        let id = $(this).data('id');

        $.get("{{ url('akun') }}/" + id + "/edit", function(res) {
            $('#edit_id').val(res.user.id);
            $('#edit_name').val(res.user.name);
            $('#edit_username').val(res.user.username);
            $('#edit_email').val(res.user.email);
            $('#edit_password').val('');

            $('#edit_role_id').empty();
            $.each(res.roles, function(i, role) {
                let selected = (res.user.roles.length && res.user.roles[0].id === role.id) ? 'selected' : '';
                $('#edit_role_id').append(`<option value="${role.id}" ${selected}>${role.name}</option>`);
            });

            $('#editModal').modal('show');
        });
    });

   $('#formEdit').on('submit', function(e) {
        e.preventDefault();

        let id = $('#edit_id').val();
        let formData = $(this).serialize();

        $.ajax({
            url: "/akun/" + id,   
            type: "POST",       
            data: formData + '&_method=PUT',
            success: function(res) {
                $('#editModal').modal('hide');
                $('#table-akun').DataTable().ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'User berhasil diperbarui.',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                let msg = 'Terjadi kesalahan saat memperbarui akun!';
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: msg
                });
            }
        });
    });

    $("#table-akun").on('click', '.delete', function () {
        let id = $(this).data("id");

        Swal.fire({
            title: 'Hapus User ini?',
            text: "Data user yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/akun') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: res.message || 'User berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#table-akun').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghapus user!'
                        });
                    }
                });
            }
        });
    });

    });

</script>
@endpush
@endsection