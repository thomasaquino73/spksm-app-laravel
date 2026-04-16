@extends('layouts.app')
@section('title', $title)
@php
    function imageUrl($path)
    {
        if (Str::startsWith($path, 'private/')) {
            return route('preview.image', ['path' => Str::after($path, 'private/')]);
        }
        return asset($path);
    }
@endphp
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


    <div class="card ">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <a href="{{ route('galeri.index') }}"
                        class="me-3 text-decoration-none {{ request('status') == null ? 'fw-bold text-dark' : 'text-muted' }}">
                        Semua ({{ $countAll }})
                    </a>
                    <a href="{{ route('galeri.index', ['status' => 1]) }}"
                        class="me-3 text-decoration-none {{ request('status') == 1 ? 'fw-bold text-secondary' : 'text-muted' }}">
                        Draft ({{ $countDraft }})
                    </a>
                    <a href="{{ route('galeri.index', ['status' => 3]) }}"
                        class="me-3 text-decoration-none {{ request('status') == 3 ? 'fw-bold text-success' : 'text-muted' }}">
                        Publish ({{ $countPublish }})
                    </a>
                    <a href="{{ route('galeri.index', ['status' => 2]) }}"
                        class="me-3 text-decoration-none {{ request('status') == 2 ? 'fw-bold text-warning' : 'text-muted' }}">
                        Unpublish ({{ $countUnpublish }})
                    </a>
                    <a href="{{ route('galeri.trash') }}"
                        class="text-decoration-none {{ request('status') == 0 ? 'fw-bold text-secondary' : 'text-muted' }}">
                        Deleted ({{ $countDeleted }})
                    </a>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <a href="{{ route('galeri.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Tambah Data
                    </a>
                    {{-- <a href="{{ route('galeri.trash') }}" class="btn btn-secondary">
                            <i class="ti ti-trash me-1"></i>
                        </a> --}}
                </div>
            </div>
        </div>
        <div class="row p-4">
            <div class="row mb-4  ">
                <div class="col-md-4 ">
                    <form method="GET" action="{{ route('galeri.index') }}">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="basic-addon-search31">Kategori</span>
                            <select name="kategori" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Semua Kategori --</option>

                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </form>
                </div>
            </div>
            @forelse($galleries as $galeri)
                @php
                    $statusLabel = '';
                    $statusClass = '';
                    $statusIcon = '';

                    switch ($galeri->status) {
                        case 1:
                            $statusLabel = 'Draft';
                            $statusClass = 'bg-secondary';
                            $statusIcon = 'cloud-off';
                            break;
                        case 2:
                            $statusLabel = 'Unpublish';
                            $statusClass = 'bg-danger';
                            $statusIcon = 'cloud-down';
                            break;
                        case 3:
                            $statusLabel = 'Publish';
                            $statusClass = 'bg-success';
                            $statusIcon = 'cloud-up';
                            break;
                        case 4:
                            $statusLabel = 'Dijadwalkan';
                            $statusClass = 'bg-warning';
                            $statusIcon = 'cloud-up';
                            break;
                        case 0:
                            $statusLabel = 'Deleted';
                            $statusClass = 'bg-danger';
                            $statusIcon = 'trash';
                            break;
                    }
                @endphp

                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                    <div class="card shadow-sm">

                        <div class="position-relative">

                            <span class="badge {{ $statusClass }} position-absolute top-0 end-0 m-2">
                                <i class="ti ti-{{ $statusIcon }} me-1"></i> {{ $statusLabel }}
                            </span>

                            <img src="{{ asset($galeri->photo_thumbnail) }}" class="card-img-top"
                                style="height:220px; object-fit:cover;" alt="{{ $galeri->caption }}">
                        </div>

                        <div class="card-body">

                            <h6 class="card-title mb-1">
                                {{ $galeri->caption }} | <small class="text-muted">{{ $galeri->kategori_names }}</small>
                            </h6>

                            @php
                                $descLimit = 60;
                                $description = $galeri->description;
                            @endphp

                            <p class="card-text text-muted small description-text" data-full="{{ $description }}">
                                {{ Str::limit($description, $descLimit) }}
                                @if (strlen($description) > $descLimit)
                                    <a href="#" class="show-more-link">Show More</a>
                                @endif
                            </p>
                            @php
                                $keywords = explode(',', $galeri->keyword);
                            @endphp

                            @foreach ($keywords as $keyword)
                                <span class="badge bg-primary">#{{ trim($keyword) }}</span>
                            @endforeach
                            <br>
                            @php
                                // Label status
                                $statusLabels = [
                                    0 => 'Dihapus',
                                    1 => 'Diubah',
                                    2 => 'Diunpublish',
                                    3 => 'Dipublish',
                                    4 => 'Dijadwalkan',
                                ];

                                // User
                                $createdBy = $galeri->creator->nama_lengkap ?? 'N/A';
                                $updatedBy = $galeri->updater->nama_lengkap ?? 'N/A';

                                // Tanggal
                                $createdAt = $galeri->created_at?->format('d M Y H:i');
                                $updatedAt = $galeri->updated_at?->format('d M Y H:i');

                                // Publish date
                                $publishDate = null;
                                if (!empty($galeri->publish_date) && $galeri->publish_date != '0000-00-00 00:00:00') {
                                    try {
                                        $publishDate = \Carbon\Carbon::parse($galeri->publish_date)->format(
                                            'd M Y H:i',
                                        );
                                    } catch (\Exception $e) {
                                        $publishDate = null;
                                    }
                                }

                                // Status
                                $status = $galeri->status;
                                $statusLabel = $statusLabels[$status] ?? 'Tidak diketahui';

                                // Alasan
                                $reason = $galeri->publish_reason ?? null;
                            @endphp

                            <small class="text-muted">
                                Dibuat oleh: {{ $createdBy }} pada {{ $createdAt }}

                                @if ($status == 3 && $publishDate)
                                    | {{ $statusLabel }} oleh: {{ $updatedBy }} pada {{ $publishDate }}
                                @elseif ($status == 4 && $publishDate)
                                    | {{ $statusLabel }} oleh: {{ $updatedBy }} pada {{ $publishDate }}
                                @elseif (in_array($status, [1, 2, 0]) && $updatedAt)
                                    | {{ $statusLabel }} oleh: {{ $updatedBy }} pada {{ $updatedAt }}
                                @endif

                                @if (in_array($status, [0, 2]) && $reason)
                                    | Alasan: {{ $reason }}
                                @endif
                            </small>
                        </div>

                        <div class="card-footer bg-white border-0">

                            <div class="dropdown d-grid">

                                <button class="btn btn-primary btn-sm" data-bs-toggle="dropdown">
                                    <i class="ti ti-menu-2 me-1"></i> Action
                                </button>

                                <ul class="dropdown-menu w-100">

                                    @if ($galeri->status != 3)
                                        <li>
                                            <a href="{{ route('galeri.edit', $galeri->id) }}" class="dropdown-item">
                                                <i class="ti ti-edit me-1"></i> Ubah
                                            </a>
                                        </li>

                                        <li>
                                            <button class="dropdown-item " id="delete" data-id="{{ $galeri->id }}">
                                                <i class="ti ti-trash me-1"></i> Hapus
                                            </button>
                                        </li>
                                    @endif

                                    @if ($galeri->status == 2)
                                        <li>
                                            <button data-id="{{ $galeri->id }}" class="dropdown-item toggle-publish ">
                                                <i class="ti ti-cloud-up me-1"></i> Publish Ulang
                                            </button>
                                        </li>
                                    @elseif ($galeri->status != 2 && $galeri->status != 3)
                                        <li>
                                            <a href="{{ route('galeri.publish', $galeri->id) }}" class="dropdown-item">
                                                <i class="ti ti-cloud-up me-1"></i> Publish
                                            </a>
                                        </li>
                                    @endif

                                    @if ($galeri->status == 3)
                                        <li>
                                            <button data-id="{{ $galeri->id }}"
                                                class="dropdown-item toggle-publish unpublish">
                                                <i class="ti ti-cloud-down me-1"></i> Unpublish
                                            </button>
                                        </li>
                                    @endif

                                    <li>
                                        <button class="dropdown-item preview-image"
                                            data-src="{{ asset($galeri->photo_folder . $galeri->photo_filename) }}"
                                            data-bs-toggle="modal" data-bs-target="#previewModal">
                                            <i class="ti ti-eye me-1"></i> Lihat
                                        </button>
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12 text-center">
                    <p class="text-muted">Tidak ada galeri</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $galleries->links() }}
        </div>
    </div>



    </div>
@endsection
@push('scripts')
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Gambar</h5>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" class="img-fluid rounded shadow-sm">
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#previewModal').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let imgSrc = button.data('src');
            $('#previewImage').attr('src', imgSrc);

        });
        $(document).ready(function() {
            $('body').on('click', '#delete', function() {
                let id = $(this).data('id');
                let token = $('meta[name="csrf-token"]').attr('content');

                Swal.fire({
                    title: 'Masukkan alasan',
                    input: 'textarea',
                    inputLabel: 'Alasan penghapusan',
                    inputPlaceholder: 'Tulis alasan di sini...',
                    inputAttributes: {
                        'aria-label': 'Alasan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    inputValidator: (value) => {
                        if (!value) return 'Alasan wajib diisi!';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/galeri/${id}`,
                            type: "delete",
                            data: {
                                _method: 'DELETE',
                                reason: result.value
                            },
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            success: function(response) {

                                toastr.success(response.message ||
                                    'Data berhasil dihapus', '', {
                                        timeOut: 1500,
                                        progressBar: true,
                                        closeButton: false,
                                        positionClass: 'toast-top-right',
                                    });

                                setTimeout(() => {
                                    location.reload();
                                }, 1200);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Error deleting data', '', {
                                        timeOut: 1500,
                                        progressBar: true,
                                        closeButton: false,
                                        positionClass: 'toast-top-right',
                                    });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.toggle-publish', function() {
                let button = $(this);
                let id = button.data('id');
                let isUnpublish = button.hasClass('unpublish');

                let swalOptions = {
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                };

                if (isUnpublish) {
                    swalOptions.title = 'Masukkan alasan';
                    swalOptions.input = 'textarea';
                    swalOptions.inputLabel = 'Alasan Unpublish';
                    swalOptions.inputPlaceholder = 'Tulis alasan di sini...';
                    swalOptions.inputAttributes = {
                        'aria-label': 'Alasan'
                    };
                    swalOptions.inputValidator = (value) => {
                        if (!value) return 'Alasan wajib diisi!';
                    };
                } else {
                    swalOptions.title = 'Apakah anda yakin ingin publish ulang album ini?';
                }

                Swal.fire(swalOptions).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/galeri/${id}/toggle-publish`,
                            type: 'PATCH',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reason: isUnpublish ? result.value : null
                            },
                            beforeSend: function() {
                                button.prop('disabled', true).html(
                                    '<i class="fa fa-spinner fa-spin"></i>');
                            },
                            success: function(res) {
                                if (res.success) {
                                    toastr.success(res.message, '', {
                                        timeOut: 2000,
                                        progressBar: true,
                                        positionClass: 'toast-top-right'
                                    });

                                    setTimeout(() => {
                                        window.location.href = window.location
                                            .href;
                                    }, 1200);

                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan!',
                                    icon: 'error',
                                    showClass: {
                                        popup: 'animate__animated animate__bounceIn'
                                    },
                                    customClass: {
                                        confirmButton: 'btn btn-primary waves-effect waves-light'
                                    },
                                    buttonsStyling: false,
                                    confirmButtonText: 'OK'
                                });
                            },
                            complete: function() {
                                button.prop('disabled', false);
                            }
                        });
                    }
                });
            });
            $(document).on('click', '.show-more-link', function(e) {
                e.preventDefault();
                let link = $(this);
                let p = link.closest('.description-text');
                let fullText = p.data('full');
                let limit = 60;

                if (link.text() === 'Show More') {
                    p.html(fullText + ' <a href="#" class="show-more-link">Show Less</a>');
                } else {
                    p.html(fullText.substring(0, limit) +
                        '... <a href="#" class="show-more-link">Show More</a>');
                }
            });
        });
    </script>
@endpush
