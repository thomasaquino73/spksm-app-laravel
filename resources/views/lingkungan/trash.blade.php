@extends('layouts.app')
@push('style')
    <style>
        .bg-custom-red {
            background-color: rgba(168, 35, 35, 0.664) !important;
        }
    </style>
@endpush
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

    <div class="row">
        <div class="card p-10">
            <div class="card-header bg-custom-red mt-2 ">
                <div class="row ">
                    <div class="col-12 col-lg-6 d-flex align-items-center">
                        <i class="ti ti-trash me-2 text-white"></i>
                        <h5 class="mb-0 text-white">{{ $title }}</h5>
                    </div>
                    <div class="col-12 col-lg-6 text-lg-end">

                        <a href="{{ route('daftar-lingkungan.index') }}"
                            class="btn  btn-sm btn-secondary btn-outline-secondary text-white">
                            <i class="ti ti-chevron-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-datatable text-nowrap ">
                <table class="datatables-ajax table" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lingkungan</th>
                            <th>Wilayah</th>
                            <th>Keterangan</th>
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
                ajax: '{{ route('daftar-lingkungan.trash') }}',
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
            $('body').on('click', '.restore', function() {
                let id = $(this).data('id');
                let token = $("meta[name='csrf-token']").attr("content");
                let row = $(this).closest('tr');
                Swal.fire({
                    title: 'Restore this data?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, restore!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-success me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('daftar-lingkungan.restore', ':id') }}".replace(':id', id),
                            type: 'PUT',
                            data: {
                                _token: token
                            },
                            success: function(response) {
                                table.draw();
                                if (response.redirect) {
                                    toastr.success(response.message, '', {
                                        timeOut: 2000,
                                        progressBar: true,
                                        positionClass: 'toast-top-right'
                                    });

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: response.message ||
                                            'Error restoring user'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errMsg = 'Error restoring user';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errMsg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: errMsg,
                                    timer: 5000,
                                    customClass: {
                                        confirmButton: 'btn btn-info waves-effect waves-light'
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
