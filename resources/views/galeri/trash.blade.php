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
@push('style')
    <style>
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-right: 30px;
            margin-bottom: 30px;
        }

        .cover-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
        }

        .gallery-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: 0.4s;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .gallery-caption {
            padding: 15px;
        }

        .gallery-caption h5 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .gallery-caption p {
            font-size: 14px;
            color: #666;
        }
    </style>
@endpush


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

        <div class="card p-10">
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
                            class="text-decoration-none {{ request('status') == 0 ? 'fw-bold text-danger' : 'text-muted' }}">
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
            <div class="row">
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
                                    {{ $galeri->caption }}
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
                                    if (
                                        !empty($galeri->publish_date) &&
                                        $galeri->publish_date != '0000-00-00 00:00:00'
                                    ) {
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
                                        <li>
                                            <button data-id="{{ $galeri->id }}" class="dropdown-item restore">
                                                <i class="fa fa-undo me-1"></i> Restore
                                            </button>
                                        </li>

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
                            url: "{{ route('galeri.restore', ':id') }}".replace(
                                ':id', id),
                            type: 'PUT',
                            data: {
                                _token: token
                            },
                            success: function(response) {
                                window.location.href = response.redirect;
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
