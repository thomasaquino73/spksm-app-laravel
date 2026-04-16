@extends('layouts.app')
@section('konten')
    <div class="container-xxl">
        <div class="row">
            <div class="col-lg-6 order-3 order-xl-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-7">
                            <div class="card-body text-nowrap">
                                <h5 class="card-title mb-0">Pengumuman Terbaru!</h5>
                            </div>
                        </div>
                        <div class="col-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success"><i
                                        class="ti ti-ambulance ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $total_ambulance }}</h4>
                        </div>
                        <p class="mb-1">Total Laporan Ambulance</p>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success"><i
                                        class="ti ti-heartbeat ti-md"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0"></h4>
                        </div>
                        <p class="mb-1">Total Laporan Kesehatan</p>

                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="divider mb-4">
            <div class="divider-text " style="font-size: 40px"> Pelayanan Kami</div>
        </div> --}}
        <div class="mt-5">
            @include('partials.dashboard.pelayanan')
        </div>
        {{-- 
        <div class="divider mb-5">
            <div class="divider-text " style="font-size: 40px">Pengurus</div>
        </div> --}}
        <div class="mt-5">
            @include('partials.dashboard.pengurus')
        </div>
        {{-- <div class="divider mb-5">
            <div class="divider-text " style="font-size: 40px"> Galeri Foto</div>
        </div> --}}
        <div class="mt-5">
            @include('partials.dashboard.gallery')
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.show-more-link', function(e) {
                let link = $(this);
                let p = link.closest('.description-text');
                let fullText = p.data('full');
                let limit = 60;

                if (link.text() === 'Show More') {
                    p.html(fullText +
                        ' <span class="show-more-link text-primary" style="cursor:pointer">Show Less</span>'
                    );
                } else {
                    p.html(fullText.substring(0, limit) +
                        '... <span class="show-more-link text-primary" style="cursor:pointer">Show More</span>'
                    );
                }
            });
        });
    </script>
@endpush
