@extends('layouts.app')
@section('title', $title)
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

    {{-- <div class="row"> --}}
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

                    <a href="{{ route('daftar-rumah-duka.trash') }}" class="btn  btn-sm btn-secondary">
                        <i class="ti ti-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive" style="padding: 20px">
            <table class="datatables-ajax table" id="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Rumah Duka</th>
                        <th>Alamat</th>
                        <th>No. Telp / Email</th>
                        <th>status</th>
                        <th>Dibuat oleh</th>
                        <th>Diubah oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- </div> --}}
@endsection
@push('style')
    <style>
        input[name^="plat_"] {
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            letter-spacing: 2px;
            font-size: 1.1rem;
        }
    </style>
@endpush
@push('scripts')
    <div class="modal fade" id="modals" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="mb-2" id="modal-title"></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="postForm" name="postForm" method="POST" action="{{ route('daftar-rumah-duka.store') }}">
                        @csrf
                        <input type="text" name="id" id="id" hidden>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="nama" class="form-label">Nama Rumah Duka<small>*</small></label>
                                <input type="text" id="nama" name="nama" class="form-control"
                                    placeholder="Masukkan Nama Rumah Duka">
                                <span class="error text-danger" id="namaError"></span>

                            </div>
                            <div class="col-6 mb-3">
                                <label for="alamat" class="form-label">Alamat<small>*</small></label>
                                <input type="text" id="alamat" name="alamat" class="form-control"
                                    placeholder="Masukkan Alamat">
                                <span class="error text-danger" id="alamatError"></span>

                            </div>
                            <div class="col-6 mb-3">
                                <label for="kontak" class="form-label">No Telp / Email<small>*</small></label>
                                <input type="text" id="kontak" name="kontak" class="form-control"
                                    placeholder="Masukkan Nama Iuran">
                                <span class="error text-danger" id="kontakError"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Pilih Status</option>
                                    <option value="1">Active</option>
                                    <option value="2">Not Active</option>
                                </select>
                                <span class="error text-danger" id="statusError"></span>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" id="savedata" name="savedata" class="btn btn-primary me-sm-3 me-1">
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
                ajax: '{{ route('daftar-rumah-duka.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                    },
                    {
                        data: 'alamat',
                    },
                    {
                        data: 'kontak',
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
                let kodeOtomatis = 'IUR' + Date.now();
                $('#modals').modal('show');
                $('#modal-title').html('Tambah Rumah Duka');
                $('#savedata').html('<i class="fa fa-save me-1"></i> Simpan');
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
                    url: "/daftar-rumah-duka/" + id + "/edit",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        $('#modal-title').html('Ubah Rumah Duka');
                        $('#id').val(data.id);
                        $('#nama').val(data.nama);
                        $('#alamat').val(data.alamat);
                        $('#kontak').val(data.kontak);
                        $('#status').val(data.status).trigger('change');
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
                            url: `/daftar-rumah-duka/${id}`,
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
        });
    </script>
@endpush
