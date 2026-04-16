@extends('layouts.app')
@section('title', $title)

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">

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

                <div class="card mb-4">

                    <h5 class="card-header">{{ $title }}</h5>

                    <div class="card-body">

                        <form action="{{ route('user.store') }}" method="POST" id="postForm" enctype="multipart/form-data">
                            @csrf
                            <div class="divider divider-dashed">
                                <div class="divider-text">PERSONAL DATA</div>
                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>Avatar</label>
                                    <input type="file" name="avatar" id="avatar" class="form-control">
                                    <span class="text-danger error" id="avatarError"></span>
                                </div>
                                <div class="col-md-6 mb-3 ">
                                    <label>Nomor ID<small>*</small></label>
                                    <input type="text" name="no_ID" id="no_ID" class="form-control">
                                    <span class="text-danger error" id="no_IDError"></span>
                                </div>
                                <div class="col-md-6 mb-3 ">
                                    <label>Nama Lengkap<small>*</small></label>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control">
                                    <span class="text-danger error" id="nama_lengkapError"></span>
                                </div>
                                <div class="col-md-6 mb-3 ">
                                    <label>Tempat Lahir<small>*</small></label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control">
                                    <span class="text-danger error" id="tempat_lahirError"></span>
                                </div>
                                <div class="col-md-6 mb-3 ">
                                    <label>Tanggal Lahir<small>*</small></label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control">
                                    <span class="text-danger error" id="tanggal_lahirError"></span>
                                </div>
                                <div class="col-lg-6 col-sm-12 mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin<small>*</small></label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                        <option value="" hidden selected>Pilih Jenis Kelamin</option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                    <span class="error text-danger" id="jenis_kelaminError"></span>
                                </div>
                                <div class="col-md-12 mb-3 ">
                                    <label>Alamat<small>*</small></label>
                                    <input type="text" name="alamat" id="alamat" class="form-control">
                                    <span class="text-danger error" id="alamatError"></span>
                                </div>
                                <div class="col-lg-3 col-sm-12 mb-3">
                                    <label for="jenis_kelamin" class="form-label">Lingkungan<small>*</small></label>
                                    <select name="daftar_lingkungan_id" id="daftar_lingkungan_id"
                                        class="form-control select2">
                                        <option value="" hidden selected>Pilih Daftar Lingkungan</option>
                                        @foreach ($lingkungan as $lingkungan)
                                            <option value="{{ $lingkungan->id }}">{{ $lingkungan->nama_lingkungan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="error text-danger" id="daftar_lingkungan_idError"></span>
                                </div>
                                <div class="col-lg-3 col-sm-12 mb-3">
                                    <label for="jenis_kelamin" class="form-label">Warga Negara<small>*</small></label>
                                    <select name="warga_negara" id="warga_negara" class="form-control">
                                        <option value="" hidden selected>Pilih Jenis Warga Negara</option>
                                        <option value="WNI">WNI</option>
                                        <option value="WNA">WNA</option>
                                    </select>
                                    <span class="error text-danger" id="warga_negaraError"></span>
                                </div>
                                <div class="col-md-6 mb-3 ">
                                    <label>No. Telp</label>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control">
                                    <span class="text-danger error" id="no_telpError"></span>
                                </div>
                            </div>
                            <div class="divider divider-dashed">
                                <div class="divider-text">ACCOUNT DATA</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Username *</label>
                                    <input type="text" name="username" id="username" class="form-control">
                                    <span class="text-danger error" id="usernameError"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email *</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                    <span class="text-danger error" id="emailError"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Password *</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                    <small>Password minimal 8 karakter</small>
                                    <span class="text-danger error" id="passwordError"></span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Confirm Password *</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control">
                                    <small>Password minimal 8 karakter</small>
                                    <span class="text-danger error" id="confirm_passwordError"></span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Status *</label>
                                    <select name="status" id="status" class="form-select select2">
                                        <option value="Active">Active</option>
                                        <option value="Not Active">Not Active</option>
                                    </select>
                                    <span class="text-danger error" id="statusError"></span>

                                </div>


                                {{-- ROLE --}}
                                <div class="col-md-6 mb-3">
                                    <label>Hak Akess *</label>
                                    <select name="roles" id="roles" class="form-select select2">
                                        <option value="">-- Pilih Hak Akess --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error" id="rolesError"></span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('user.index') }}" class="btn btn-secondary"> Kembali </a>
                                <button class="btn btn-primary" id="savedata">
                                    <i class="fa fa-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            $('#daftar_lingkungan_id').select2({
                placeholder: "Pilih Lingkungan",
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
                            title: 'Ubah Data Gagal',
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
