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
                    <a href="{{ url('pesan-ambulance') }}" id="create" class="btn  btn-sm btn-primary">
                        <i class="ti ti-plus me-1"></i> Pesan Ambulance
                    </a>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive" style="padding: 20px">
            <table class="datatables-ajax table" id="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Pemesanan</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat Penjemputan</th>
                        <th>Waktu Pemesanan</th>
                        <th>Kondisi Pasien</th>
                        <th>Lokasi Pengantaran</th>
                        <th>Catatan Singkat</th>
                        <th>Status</th>
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
                ajax: '{{ route('ambulance.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_pesan',
                    },
                    {
                        data: 'nama_pasien',
                    },
                    {
                        data: 'jenis_kelamin',
                    },
                    {
                        data: 'alamat_penjemputan',
                    },
                    {
                        data: 'waktu_pesan',
                    },
                    {
                        data: 'kondisi_pasien',
                    },
                    {
                        data: 'lokasi_pengantaran',
                    },
                    {
                        data: 'catatan_singkat',
                    },
                    {
                        data: 'status',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            $('body').on('click', '#batalPesan', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let token = $("meta[name='csrf-token']").attr("content");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Want to cancel data: " + name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, cancel it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/batal-pesan/${id}`,
                            type: "put",
                            cache: false,
                            data: {
                                _token: token
                            },
                            success: function(response) {
                                table.draw();
                                toastr.success('Pesanan Berhasil dibatalkan', '', {
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
