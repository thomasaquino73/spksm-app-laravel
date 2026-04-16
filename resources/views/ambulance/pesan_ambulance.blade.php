@extends('layouts.app')
@section('konten')
    <h4>
        <span class="text-muted fw-light">
            @foreach ($breadcrumb as $item)
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
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-4">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ambulance.pesan') ? 'active' : '' }}"
                        href="{{ url('pesan-ambulance') }}">
                        <i class="ti ti-ambulance ti-xs me-1"></i>Pesan Ambulance
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ambulance.index.*') ? 'active' : '' }}"
                        href="{{ route('ambulance.index') }}">
                        <i class="ti ti-ambulance ti-xs me-1"></i>Daftar Pesan Ambulance
                    </a>
                </li>

            </ul>
            <div class="card mb-4">
                <h5 class="card-header">{{ $title }}</h5>
                <div class="card-body">
                    <div class="divider divider-dashed">
                        <div class="divider-text">Isi data dengan lengkap dan benar</div>
                    </div>
                    <form action="{{ route('ambulance.store') }}" method="POST" id="postForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3 ">
                                <label>Nama Pasien<small>*</small></label>
                                <input type="text" name="nama_pasien" id="nama_pasien" class="form-control"
                                    value="">
                                <span class="text-danger error" id="nama_pasienError"></span>
                            </div>
                            <div class="col-md-6 mb-3 ">
                                <label>Jenis Kelamin<small>*</small></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <span class="text-danger error" id="jenis_kelaminError"></span>
                            </div>
                            <div class="col-md-12 mb-3 ">
                                <label>Alamat Penjemputan<small>*</small></label>
                                <input type="text" name="alamat_penjemputan" id="alamat_penjemputan" class="form-control"
                                    value="">
                                <span class="text-danger error" id="alamat_penjemputanError"></span>
                            </div>
                            <div class="col-md-6 mb-3 ">
                                <label>Kondisi Pasien<small>*</small></label>
                                <select name="kondisi_pasien" id="kondisi_pasien" class="form-control">
                                    <option value="">Pilih Kondisi Pasien</option>
                                    <option value="0">Sakit</option>
                                    <option value="1">Meninggal</option>
                                </select>
                                <span class="text-danger error" id="kondisi_pasienError"></span>
                            </div>
                            <div class="col-md-6 mb-3 ">
                                <label>Lokasi Pengantaran<small>*</small></label>
                                <select name="lokasi_pengantaran" id="lokasi_pengantaran" class="form-control">
                                    <option value="">Pilih Lokasi Pengantaran</option>
                                    <option value="1">Rumah</option>
                                    <option value="2">Rumah Sakit</option>
                                    <option value="3">Rumah Duka</option>
                                </select>
                                <span class="text-danger error" id="lokasi_pengantaranError"></span>
                            </div>
                            <div class="col-md-12 mb-3 ">
                                <label>Catatan Singkat</label>
                                <input type="text" name="catatan_singkat" id="catatan_singkat" class="form-control"
                                    value="">
                                <span class="text-danger error" id="catatan_singkatError"></span>
                            </div>


                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" id="savedata">
                                <i class="fa fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            $('#jenis_kelamin').select2({
                placeholder: "Pilih Jenis Kelamin",
                allowClear: true,
                width: '100%'
            });
            $('#kondisi_pasien').select2({
                placeholder: "Pilih Kondisi Pasien",
                allowClear: true,
                width: '100%'
            });
            $('#lokasi_pengantaran').select2({
                placeholder: "Pilih Lokasi Pengantaran",
                allowClear: true,
                width: '100%'
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
                        $('#savedata').html(' <i class="fa fa-save me-1"></i> Simpan');
                    },
                    success: function(response) {
                        window.location.href = response.redirect;
                        Swal.fire({
                            icon: 'success',
                            title: 'Simpan Berhasil',
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
                            title: 'Simpan Data Gagal',
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
        });
    </script>
@endpush
