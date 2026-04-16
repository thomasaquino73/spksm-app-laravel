@extends('layouts.app')
@section('konten')
    <h4><span class="text-muted fw-light">
            @foreach ($breadcrumb as $key => $item)
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    {{ $item['label'] }}
                @endif

                @if (!$loop->last)
                    /
                @endif
            @endforeach
        </span>
    </h4>

    <div class="card ">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h5 class="mb-0">{{ $title }}</h5>
                </div>
                <div class="col-12 col-lg-6 text-lg-end">
                    <button id="create" class="btn  btn-sm btn-primary">
                        <i class="ti ti-plus me-1"></i> Tambah Data
                    </button>

                    <a href="{{ route('daftar-lingkungan.trash') }}" class="btn  btn-sm btn-secondary">
                        <i class="ti ti-trash "></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card text-nowrap ">
            <div class="card-datatable table-responsive" style="padding: 20px">

                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lingkungan</th>
                            <th>Wilayah</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Dibuat oleh</th>
                            <th>Diubah oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <div class="modal fade" id="modals" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple ">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2" id="modal-title"></h3>
                    </div>
                    <form id="postForm" name="postForm" method="POST" action="{{ route('daftar-lingkungan.store') }}">
                        @csrf
                        <input type="text" name="id" id="id" hidden>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label w-100" for="nama_lingkungan">Nama Lingkungan</label>
                                <div class="input-group input-group-merge">
                                    <input id="nama_lingkungan" name="nama_lingkungan" class="form-control " type="text"
                                        placeholder="Masukkan Nama Lingkungan" />
                                </div>
                                <span class="error text-danger" id="nama_lingkunganError"></span>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label w-100" for="wilayah">Wilayah</label>
                                <div class="input-group input-group-merge">
                                    <input id="wilayah" name="wilayah" class="form-control " type="text"
                                        placeholder="Masukkan Wilayah" />
                                </div>
                                <span class="error text-danger" id="wilayahError"></span>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label w-100" for="keterangan">Keterangan</label>
                                <div class="input-group input-group-merge">
                                    <textarea id="keterangan" name="keterangan" class="form-control " type="text" placeholder="Masukkan Keterangan"></textarea>
                                </div>
                                <span class="error text-danger" id="keteranganError"></span>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Status</label>

                                <div class="form-check mb-2">
                                    <input name="status" class="form-check-input" type="radio" value="1"
                                        id="status" />
                                    <label class="form-check-label" for="">
                                        Active
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input name="status" class="form-check-input" type="radio" value="2"
                                        id="status" />
                                    <label class="form-check-label" for="">
                                        Not Active
                                    </label>
                                </div>
                            </div>
                            <span class="error text-danger" id="statusError"></span>
                        </div>


                        <div class="col-12 text-center">
                            <button type="submit" id="savedata" name="savedata" class="btn btn-primary me-sm-3 me-1">
                            </button>
                    </form>
                    <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                        aria-label="Close">
                        Batal
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table = new DataTable('#table', {
                processing: true,
                serverSide: true,
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                ajax: '{{ route('daftar-lingkungan.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lingkungan',
                    },
                    {
                        data: 'wilayah',
                    },
                    {
                        data: 'keterangan',
                    },
                    {
                        data: 'status',
                    },
                    {
                        data: 'created_at',
                    },
                    {
                        data: 'updated_at',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            $('#create').click(function() {
                $('#modals').modal('show');
                $('#savedata').html('<i class="fa fa-save me-1"></i>Simpan');
                $('#modal-title').html('Tambah Daftar Lingkungan');
                $('#postForm').trigger('reset');
                $('#id').val('');
                resetValidation();
            });
            $('#postForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    datatype: 'json',
                    beforeSend: function(e) {
                        $('#savedata').html(
                            '<i class="fa fa-spin fa-spinner me-1"></i> Sending...');
                    },
                    complete: function(e) {
                        $('#savedata').html(' <i class="fa fa-save me-1"></i>Simpan');
                    },
                    success: function(response) {
                        $('#modals').modal('hide');
                        table.draw();
                        Swal.fire({
                            icon: 'success',
                            title: response.title,
                            text: response.message,
                            showClass: {
                                popup: 'animate__animated animate__bounceIn'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        });

                    },
                    error: function(xhr) {
                        resetValidation();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Periksa kembali data Anda.',
                            showClass: {
                                popup: 'animate__animated animate__bounceIn'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        });
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, value) {
                            // For other fields, display individual field errors if any
                            displayFieldError(key, value[0]);
                        });
                    }
                });


            });
            $('body').on('click', '.editPost', function(a) {
                $('#modals').modal('show');
                $('#savedata').html('<i class="fa fa-save me-1"></i>Simpan');
                resetValidation();

                var id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "/daftar-lingkungan/" + id + "/edit",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#modal-title').html('Ubah Daftar Lingkungan');
                        $('#id').val(data.id);
                        $('#nama_lingkungan').val(data.nama_lingkungan);
                        $('#wilayah').val(data.wilayah);
                        $('#keterangan').val(data.keterangan);
                        $('input[name="status"][value="' + data.status + '"]')
                            .prop('checked', true);
                        resetValidation();
                    }
                });
            });


            $('body').on('click', '#delete', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let token = $("meta[name='csrf-token']").attr("content");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Want to delete data: " + name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/daftar-lingkungan/${id}`,
                            type: "DELETE",
                            cache: false,
                            data: {
                                _token: token
                            },
                            success: function(response) {
                                table.draw();
                                toastr.success('Data Berhasil dihapus', '', {
                                    timeOut: 1500,
                                    progressBar: true,
                                    closeButton: false,
                                    positionClass: 'toast-top-right',
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to delete',
                                    text: 'An error occurred. Please try again later.',
                                    timer: 5000,
                                    customClass: {
                                        confirmButton: 'btn btn-primary waves-effect waves-light'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cancelled',
                            text: 'Your data is safe.',
                            customClass: {
                                confirmButton: 'btn btn-info waves-effect waves-light'
                            }
                        });
                    }
                });
            });

            $('body').on('click', '.restore', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let token = $("meta[name='csrf-token']").attr("content");

                Swal.fire({
                    title: 'Want To Restore This Area?',
                    text: name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/bjid/restore-area/${id}`,
                            type: "PATCH",
                            cache: false,
                            data: {
                                _token: token
                            },
                            success: function(response) {
                                table.draw();
                                toastr.success('Data Berhasil diUnpublish', '', {
                                    timeOut: 1500,
                                    progressBar: true,
                                    closeButton: false,
                                    positionClass: 'toast-top-right',
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to delete',
                                    text: 'An error occurred. Please try again later.',
                                    timer: 5000,
                                    customClass: {
                                        confirmButton: 'btn btn-info waves-effect waves-light'
                                    }
                                });
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cancelled',
                            text: 'Your data is safe.',
                            customClass: {
                                confirmButton: 'btn btn-info waves-effect waves-light'
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
