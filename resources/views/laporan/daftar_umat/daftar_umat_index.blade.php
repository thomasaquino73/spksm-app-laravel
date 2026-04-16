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

    <div class="row">
        <div class="card p-10">
            <div class="card-header bg-white">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">{{ $title }}</h5>
                    </div>
                    <div class="col-12 col-lg-6 text-lg-end">

                    </div>
                </div>
            </div>
            <div class="card-datatable text-nowrap ">
                <table class="table table-hover table-striped" id="table">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Avatar</th>
                            <th>Nomor ID</th>
                            <th>Nama Lengkap</th>
                            <th>Nomor Telp</th>
                            <th>Alamat</th>
                            <th>Lingkungan</th>
                            <th>Status</th>
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
            var groupColumn = 6; // kolom 'lingkungan' index 0-based

            var table = new DataTable('#table', {
                processing: true,
                serverSide: true,
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                columnDefs: [{
                    targets: groupColumn,
                    visible: false
                }],

                order: [
                    [groupColumn, 'asc']
                ],

                drawCallback: function(settings) {

                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();

                    var last = null;

                    api.column(groupColumn, {
                            page: 'current'
                        }).data()
                        .each(function(group, i) {

                            if (!group || group === '-') {
                                group = 'Tidak ada RW';
                            }

                            if (last !== group) {

                                var count = api.column(groupColumn)
                                    .data()
                                    .filter(function(value) {
                                        return value === group;
                                    }).length;

                                $(rows).eq(i).before(
                                    '<tr class="group">' +
                                    '<td colspan="11" class="bg-light fw-bold text-primary">' +
                                    'Lingkungan : ' + group +
                                    ' (' + count + ' umat)' +
                                    '</td>' +
                                    '</tr>'
                                );

                                last = group;
                            }

                        });

                },
                ajax: {
                    url: '{{ route('daftar-umat.tabelUmat') }}',
                    dataSrc: function(json) {
                        console.log(json.data); // cek properti 'lingkungan'
                        return json.data;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'avatar'
                    },
                    {
                        data: 'no_ID'
                    },
                    {
                        data: 'nama_lengkap'
                    },
                    {
                        data: 'no_telp'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: 'lingkungan'
                    }, // index 6
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
