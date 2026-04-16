@extends('layouts.app')
@section('title', $title)

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

        <div class="card p-4">

            <div class="card-header bg-white">

                <div class="row">

                    <div class="col-lg-6 mb-3">
                        <a href="{{ route('berita-video.index') }}"
                            class="me-3 text-decoration-none {{ request('status') == null ? 'fw-bold text-dark' : 'text-muted' }}">
                            Semua ({{ $countAll }})
                        </a>
                        <a href="{{ route('berita-video.index', ['status' => 1]) }}"
                            class="me-3 text-decoration-none {{ request('status') == 1 ? 'fw-bold text-secondary' : 'text-muted' }}">
                            Draft ({{ $countDraft }})
                        </a>
                        <a href="{{ route('berita-video.index', ['status' => 3]) }}"
                            class="me-3 text-decoration-none {{ request('status') == 3 ? 'fw-bold text-success' : 'text-muted' }}">
                            Publish ({{ $countPublish }})
                        </a>
                        <a href="{{ route('berita-video.index', ['status' => 2]) }}"
                            class="me-3 text-decoration-none {{ request('status') == 2 ? 'fw-bold text-warning' : 'text-muted' }}">
                            Unpublish ({{ $countUnpublish }})
                        </a>
                        <a href="{{ route('berita-video.trash') }}"
                            class="text-decoration-none {{ request('status') == 0 ? 'fw-bold text-secondary' : 'text-muted' }}">
                            Deleted ({{ $countDeleted }})
                        </a>
                    </div>


                    <div class="col-lg-6 text-lg-end">

                        <a href="{{ route('berita-video.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Tambah Video
                        </a>

                    </div>

                </div>

            </div>


            <div class="row mt-3">

                @forelse($videos as $video)
                    @php

                        $statusLabel = '';
                        $statusClass = '';
                        $statusIcon = '';

                        switch ($video->status) {
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

                        /* ambil id youtube dari link */

                        $link = $video->youtube_id;

                        $youtubeId = '';

                        if (preg_match('/youtu\.be\/([^\?]+)/', $link, $match)) {
                            $youtubeId = $match[1];
                        } elseif (preg_match('/v=([^&]+)/', $link, $match)) {
                            $youtubeId = $match[1];
                        } elseif (preg_match('/shorts\/([^\?]+)/', $link, $match)) {
                            $youtubeId = $match[1];
                        } else {
                            $youtubeId = $link;
                        }

                        $thumbnail = "https://img.youtube.com/vi/$youtubeId/hqdefault.jpg";

                    @endphp


                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                        <div class="card video-card shadow-sm">


                            <div class="video-thumbnail position-relative">

                                <img src="{{ $thumbnail }}" class="card-img-top">

                                <span class="badge {{ $statusClass }} position-absolute top-0 end-0 m-2">

                                    <i class="ti ti-{{ $statusIcon }}"></i>
                                    {{ $statusLabel }}

                                </span>


                                <div class="play-button preview-video" data-id="{{ $youtubeId }}" data-bs-toggle="modal"
                                    data-bs-target="#previewModal">

                                    <i class="ti ti-player-play-filled"></i>

                                </div>

                            </div>


                            <div class="card-body">

                                <h6 class="card-title mb-1">
                                    {{ $video->judul }}| <small class="text-muted ">{{ $video->kategori_names }}</small>
                                </h6>

                                @php
                                    $descLimit = 60;
                                    $description = $video->deskripsi;
                                @endphp

                                <p class="card-text text-muted small description-text mt-3"
                                    data-full="{{ $description }}">
                                    {{ Str::limit($description, $descLimit) }}
                                    @if (strlen($description) > $descLimit)
                                        <a href="#" class="show-more-link">Show More</a>
                                    @endif
                                </p>
                                @php
                                    $keywords = explode(',', $video->keyword);
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
                                    $createdBy = $video->creator->nama_lengkap ?? 'N/A';
                                    $updatedBy = $video->updater->nama_lengkap ?? 'N/A';

                                    // Tanggal
                                    $createdAt = $video->created_at?->format('d M Y H:i');
                                    $updatedAt = $video->updated_at?->format('d M Y H:i');

                                    // Publish date
                                    $publishDate = null;
                                    if (!empty($video->publish_date) && $video->publish_date != '0000-00-00 00:00:00') {
                                        try {
                                            $publishDate = \Carbon\Carbon::parse($video->publish_date)->format(
                                                'd M Y H:i',
                                            );
                                        } catch (\Exception $e) {
                                            $publishDate = null;
                                        }
                                    }

                                    // Status
                                    $status = $video->status;
                                    $statusLabel = $statusLabels[$status] ?? 'Tidak diketahui';

                                    // Alasan
                                    $reason = $video->publish_reason ?? null;
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
                                            <button data-id="{{ $video->id }}" class="dropdown-item restore">
                                                <i class="fa fa-undo me-1"></i>Restore
                                            </button>
                                        </li>


                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-12 text-center">

                        <p class="text-muted">Tidak ada video</p>

                    </div>
                @endforelse

            </div>


            <div class="mt-4">

                {{ $videos->links() }}

            </div>

        </div>

    </div>

@endsection

@push('style')
    <style>
        .video-card {
            transition: 0.3s;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .video-thumbnail img {
            height: 200px;
            object-fit: cover;
        }

        .play-button {

            position: absolute;
            top: 50%;
            left: 50%;

            transform: translate(-50%, -50%);

            background: rgba(0, 0, 0, 0.7);
            color: white;

            width: 60px;
            height: 60px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 28px;

            cursor: pointer;

            transition: 0.3s;

        }

        .play-button:hover {
            background: red;
        }
    </style>
@endpush
@push('scripts')
    <div class="modal fade" id="previewModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Preview Video</h5>
                </div>

                <div class="modal-body">

                    <div class="ratio ratio-16x9">
                        <iframe id="previewYoutube" src="" frameborder="0" allow="autoplay; encrypted-media"
                            allowfullscreen>
                        </iframe>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.preview-video', function() {

            let id = $(this).data('id');

            let url = "https://www.youtube.com/embed/" + id + "?autoplay=1&rel=0";

            $('#previewYoutube').attr('src', url);

        });

        $('#previewModal').on('hidden.bs.modal', function() {

            $('#previewYoutube').attr('src', '');

        });
        $(document).on('click', '.show-more-link', function(e) {

            e.preventDefault();

            let link = $(this);
            let p = link.closest('.description-text');
            let full = p.data('full');
            let limit = 60;

            if (link.text() === 'Show More') {

                p.html(full + ' <a href="#" class="show-more-link">Show Less</a>');

            } else {

                p.html(full.substring(0, limit) + '... <a href="#" class="show-more-link">Show More</a>');

            }

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
                            url: "{{ route('berita-video.restore', ':id') }}".replace(
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
