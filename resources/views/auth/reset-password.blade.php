@extends('layouts.guest')
@section('konten')
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    @if (session('logout_message'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('logout_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <!-- Logo -->
                    {{-- <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="index.html" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                                        fill="#7367F0" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                                        fill="#161616" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                                        fill="#161616" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                                        fill="#7367F0" />
                                </svg>
                            </span>
                            <span class="app-brand-text demo text-body fw-bold ms-1">Vuexy</span>
                        </a>
                    </div> --}}
                    <!-- /Logo -->
                    <h4 class="mb-1 pt-2 text-center">{{ $aplikasi }}</h4>
                    {{-- <p class="mb-4">Please sign-in to your account and start the adventure</p> --}}
                    <div class="divider my-4">
                        <div class="divider-text">Ubah Password</div>
                    </div>
                    <form id="resetForm" method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email"
                                value="{{ old('email', $request->email) }}" placeholder="Masukkan Email Anda" readonly />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password Baru</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" autofocus />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            <span class="error text-danger" id="passwordError"></span>
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password_confirmation" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            <span class="error text-danger" id="password_confirmationError"></span>
                        </div>
                        <div class="mb-3">
                            <button id="savedata" name="savedata" type="submit"
                                class="btn btn-primary d-grid w-100 mb-3">Ubah
                                Password</button>
                        </div>
                    </form>
                </div>
                <a href="{{ route('login') }}" type="button" class="btn btn-secondary d-grid w-100">Kembali ke halaman
                    depan</a>
            </div>
            <!-- /Register -->
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            $('#resetForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;

                resetValidation();

                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    beforeSend: function() {
                        $('#savedata').html(
                            '<i class="fa fa-spin fa-spinner"></i> Sedang diproses...');
                    },
                    complete: function() {
                        $('#savedata').html('Ubah Password');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Password anda berhasil diubah.',
                            showClass: {
                                popup: 'animate__animated animate__bounceIn'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false,
                        }).then(() => {
                            window.location.href = "{{ route('login') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan, silakan coba lagi.',
                            showClass: {
                                popup: 'animate__animated animate__bounceIn'
                            },
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        });

                        resetValidation();

                        if (xhr.responseJSON?.errors) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                displayFieldError(key, value[0]);
                            });
                        }
                    }
                });
            });

            function resetValidation() {
                $('.error').text('');
                $('input, select').removeClass('is-invalid');
            }

            function displayFieldError(fieldId, errorMessage) {
                if (!$('#' + fieldId + 'Error').length) {
                    $('#' + fieldId).after(
                        `<div id="${fieldId}Error" class="invalid-feedback">${errorMessage}</div>`);
                } else {
                    $('#' + fieldId + 'Error').text(errorMessage);
                }
                $('#' + fieldId).addClass('is-invalid');
            }
        });
    </script>
@endpush
