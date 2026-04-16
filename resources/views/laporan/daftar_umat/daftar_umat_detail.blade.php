@extends('layouts.app')
@section('title', $title)
@section('konten')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">User / View /</span> Account
    </h4>
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <img class="img-fluid rounded mb-3 pt-1 mt-4"
                                src="{{ $detail->avatar ? asset($detail->avatar) : asset('image/no-images.jpg') }}"
                                height="100" width="100" alt="User avatar" />
                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $detail->nama_lengkap }}</h4>
                                <span class="badge bg-label-secondary mt-1">
                                    @foreach ($detail->getRoleNames() as $role)
                                        {{ $role }}
                                        @if (!$loop->last)
                                            |
                                        @endif
                                    @endforeach
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                        <div class="d-flex align-items-start me-4 mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i
                                    class="ti ti-heart-rate-monitor ti-sm"></i></span>
                            <div>
                                <p class="mb-0 fw-medium">1.23k</p>
                                <small>Pelayanan Kesehatan</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class="ti ti-ambulance ti-sm"></i></span>
                            <div>
                                <p class="mb-0 fw-medium">568</p>
                                <small>Pelayanan Ambulance</small>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 small text-uppercase text-muted">Details</p>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-medium me-1">Username:</span>
                                <span>{{ $detail->username }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Email:</span>
                                <span>{{ $detail->email }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Status:</span>
                                @if ($detail->status == 'Active')
                                    <span class="badge bg-success"> Active</span>
                                @else
                                    <span class="badge bg-danger">Not Active</span>
                                @endif
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Alamat:</span>
                                <span>{{ $detail->alamat }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Lingkungan:</span>
                                <span>{{ $detail->lingkungan->nama_lingkungan }}</span>
                            </li>
                            <li class="mb-2 pt-1">

                                <span class="fw-medium me-1">Jenis Kelamin:</span>
                                <span>
                                    <i
                                        class="me-1 ti {{ $detail->jenis_kelamin == 'Pria' ? 'ti-gender-male text-success' : 'ti-gender-male text-danger' }}"></i>{{ $detail->jenis_kelamin }}
                                </span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">No. Telp:</span>
                                <span>{{ $detail->no_telp }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Warga Negara:</span>
                                <span>{{ $detail->warga_negara }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Tempat, Tanggal lahir:</span>
                                <span>{{ $detail->tempat_lahir }},
                                    {{ Carbon\Carbon::parse($detail->tanggal_lahir)->format('d-m-Y') }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Umur:</span>

                                @php
                                    $umur = \Carbon\Carbon::parse($detail->tanggal_lahir)->age;
                                @endphp

                                <span>{{ $umur }} Tahun</span>
                            </li>
                        </ul>
                        {{-- <div class="d-flex justify-content-center">
                            <a href="{{ route('daftar-umat.kartu', $detail->id) }}" target="_blank"
                                class="btn btn-primary me-3">
                                <i class="ti ti-id-badge-2 me-1"></i>Cetak Kartu
                            </a>
                            <a href="javascript:;" class="btn btn-label-danger suspend-user waves-effect">Suspended</a>
                        </div> --}}
                    </div>
                </div>
            </div>
            <!-- /User Card -->

        </div>
        <!--/ User Sidebar -->

        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- User Pills -->
            {{-- <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i
                            class="ti ti-user-check ti-xs me-1"></i>Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-security.html"><i class="ti ti-lock ti-xs me-1"></i>Security</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-billing.html"><i
                            class="ti ti-currency-dollar ti-xs me-1"></i>Billing & Plans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-notifications.html"><i
                            class="ti ti-bell ti-xs me-1"></i>Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="app-user-view-connections.html"><i
                            class="ti ti-link ti-xs me-1"></i>Connections</a>
                </li>
            </ul> --}}
            <!--/ User Pills -->

            <!-- Project table -->
            <div class="card mb-4">
                <h5 class="card-header">Daftar Permintaan Layanan Kesehatan</h5>
                <div class="table-responsive mb-3">
                    <table class="table datatable-project border-top">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Project</th>
                                <th class="text-nowrap">Total Task</th>
                                <th>Progress</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <h5 class="card-header">Daftar Permintaan Layanan Ambulance</h5>
                <div class="table-responsive mb-3">
                    <table class="table datatable-project border-top">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Project</th>
                                <th class="text-nowrap">Total Task</th>
                                <th>Progress</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <!-- /Modal -->
@endsection
