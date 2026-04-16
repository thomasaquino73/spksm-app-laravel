@extends('layouts.app')
@section('title', $title)
@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
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
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">{{ $title }}</h5>
                <div class="card-header-elements ms-auto">

                </div>
            </div>
            <div class="card-datatable table-responsive" style="padding: 20px">

                <form method="POST" action="{{ route('galeri.store') }}" class="py-2" id="postForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Foto</label>
                                    <input type="file" name="photo_filename" id="photo_filename" class="form-control">
                                    <span class="error text-danger" id="photo_filenameError"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Caption</label>
                                    <input type="text" name="caption" id="caption" class="form-control">
                                    <span class="error text-danger" id="captionError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kategori</label>

                                    <select name="kategori_berita_id[]" id="kategori_berita_id" class="form-select select2"
                                        multiple>

                                        @foreach ($kategori as $k)
                                            <option value="{{ $k->id }}">
                                                {{ $k->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <span class="error text-danger" id="kategori_berita_idError"></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hastag</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control"
                                        value="{{ old('keyword') }}" placeholder="Enter the Hastag...">
                                    <span class="error text-danger" id="keywordError"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Photographer</label>
                                    <select name="photographer_id" id="photographer_id" class="form-select select2">
                                        <option value=""></option>
                                        @foreach ($fotographer as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="error text-danger" id="photographer_idError"></span>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="3" class="form-control"></textarea>
                                    <span class="error text-danger" id="descriptionError"></span>
                                </div>
                                {{-- <div class="col-12 mb-3">
                                    <label class="form-label w-100" for="content">Content</label>
                                    <div id="editor-container" style="height: 300px;"></div>
                                    <textarea id="content" name="content" class="d-none" placeholder="Article Content"></textarea>
                                    <span class="error text-danger" id="contentError"></span>

                                </div> --}}
                            </div>

                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end gap-2">
                        <button type="submit" id="savedata" class="btn btn-primary" data-save-and-new="false">
                            <i class="fa fa-upload me-1"></i> Save and Close
                        </button>

                        <button type="submit" id="savedatamore" class="btn btn-success" data-save-and-new="true">
                            <i class="fa fa-plus-circle me-1"></i> Save and Create New
                        </button>
                        <a href="{{ route('galeri.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#photographer_id').select2({
            placeholder: "Pilih Fotographer",
            allowClear: true,
            width: '100%'
        });
        $('#kategori_berita_id').select2({
            placeholder: "Pilih Kategori",
            allowClear: true,
            width: '100%'
        });
    </script>
    {{-- Tagify config & setting --}}
    <script>
        /* ======================================================
         * Tagify config & setting
         * ====================================================== */
        document.addEventListener("DOMContentLoaded", function() {
            let input = document.querySelector("#keyword");
            if (!input) return console.warn('Hastags not found');

            // === Inisialisasi Tagify ===
            const tagify = new Tagify(input, {
                whitelist: [],
                enforceWhitelist: false,
                dropdown: {
                    enabled: 0,
                    maxItems: 100,
                    closeOnSelect: false,
                    classname: 'readonly-dropdown'
                },
                // 🔥 intercept di sini — semua tag otomatis lowercase
                transformTag: (tagData) => {
                    if (tagData && tagData.value) {
                        tagData.value = tagData.value.toLowerCase();
                    }
                    return tagData;
                }
            });

            // === ⿡ Saat mengetik, ubah teks input menjadi lowercase (live typing) ===
            const tagInput = tagify.DOM.inputElm;
            if (tagInput) {
                tagInput.addEventListener("input", function() {
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    this.value = this.value.toLowerCase();
                    this.setSelectionRange(start, end);
                });
            }


            // Saat field difokuskan -> load semua tag suggestion dari server
            tagify.on("focus", function(e) {
                fetch("{{ route('galeri.searchtags') }}")
                    .then(res => res.json())
                    .then(function(whitelist) {
                        tagify.settings.whitelist = whitelist;
                        tagify.dropdown.show(); // tampilkan semua suggestion saat fokus
                    });
            });

            // Saat user mengetik -> load suggestion filter
            tagify.on("input", function(e) {
                let value = e.detail.value;
                if (value.length < 1) return;

                fetch("{{ route('galeri.searchtags') }}?q=" + encodeURIComponent(value))
                    .then(res => res.json())
                    .then(function(whitelist) {
                        tagify.settings.whitelist = whitelist;
                        tagify.dropdown.show(value); // tampilkan dropdown suggestion
                    })
            });

            // Cegah user memilih dari dropdown (sebelum ditambahkan)
            tagify.on("beforeAdd", function(e) {
                // kalau item berasal dari whitelist (suggestion), tolak
                if (e.detail && e.detail.__isSuggestion) {
                    e.preventDefault(); // stop proses add
                    return false;
                }
            });

            // Cegah user memilih dari dropdown
            tagify.on("dropdown:select", function(e) {
                e.preventDefault();
                console.log("User mencoba pilih dari dropdown:", e.detail);
                //tagify.dropdown.hide(); // sembunyikan dropdown setelah klik
                // kalau suggestion berhasil masuk, langsung hapus lagi
                if (e.detail && e.detail.data && e.detail.data.value) {
                    // jalankan sedikit delay supaya sempat ke-insert dulu
                    // setTimeout(() => {
                    //     tagify.removeTag(e.detail.data.value);
                    // }, 10);
                }
                return false;
            });

        });
    </script>
    <script>
        let saveAndNew = false;

        $('#savedata').click(function(e) {
            saveAndNew = false;
        });

        $('#savedatamore').click(function(e) {
            saveAndNew = true;
        });

        $('#postForm').on('submit', function(e) {
            e.preventDefault();
            let form = this;
            let btn = saveAndNew ? $('#savedatamore') : $('#savedata');
            let formData = new FormData(form);
            formData.append('save_and_new', saveAndNew ? 1 : 0);

            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    btn.html('<i class="fa fa-spin fa-spinner me-1"></i> Sending...');
                    btn.prop('disabled', true);
                },
                complete: function() {
                    if (saveAndNew) {
                        btn.html('<i class="fa fa-plus-circle me-1"></i> Save and Create New');
                    } else {
                        btn.html('<i class="fa fa-upload me-1"></i> Save and Close');
                    }
                    btn.prop('disabled', false);
                },
                success: function(response) {
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
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                },
                error: function(xhr) {
                    // reset validation messages (buat kamu implement sendiri)
                    resetValidation();

                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Gambar Gagal',
                        text: 'Periksa kembali data Anda.',
                        showClass: {
                            popup: 'animate__animated animate__bounceIn'
                        },
                        customClass: {
                            confirmButton: 'btn btn-primary waves-effect waves-light'
                        },
                        buttonsStyling: false
                    });

                    let errors = xhr.responseJSON.errors || {};

                    $.each(errors, function(key, value) {
                        displayFieldError(key, value[
                            0]); // fungsi buat nampilin error per field
                    });
                }
            });
        });
    </script>
@endpush
