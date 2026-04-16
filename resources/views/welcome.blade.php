<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clone Kompas Header - Mobile Burger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="{{ asset('assets/css/mystyle.css') }}">
</head>

<body>

    <header class="kp-header-top">
        <div class="container d-flex align-items-center justify-content-between">
            <div>
                <img src="{{ asset('') }}image/logo/logo-full.png" alt="Kompas Logo" class="logo">
            </div>

            <div class="search-container d-none d-md-flex">
                <input type="text" placeholder="Cari tokoh, topik atau peristiwa">
                <i class="bi bi-search"></i>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-primary btn-sm rounded-pill d-none d-lg-block fw-bold px-3"
                    style="font-size: 11px;"><i class="fa fa-whatsapp me-1"
                        style="font-size: 20px; width: 20px;"></i>Hubungi Kami</button>
                <i class="bi bi-search text-white d-md-none fs-5"></i>
                <a href="{{ url('/login') }}">
                    <i class="bi bi-person-circle text-white fs-4"></i>
                </a>

                <button class="btn p-0 text-white d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasMobileMenu">
                    <i class="bi bi-list fs-1"></i>
                </button>
            </div>
        </div>
    </header>

    <nav class="kp-nav-bg d-none d-lg-block">
        <div class="container">
            <ul class="nav justify-content-center">
                {{-- Gunakan $kat atau $item, jangan pakai nama yang sama dengan koleksinya --}}
                @foreach ($kategori as $kat)
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ Str::ucfirst($kat->name) }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMobileMenu">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold">MENU KATEGORI</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column">
                {{-- Sekarang variabel $kategori masih utuh dan bisa dilooping lagi --}}
                @foreach ($kategori as $kat)
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ Str::ucfirst($kat->name) }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="trending-bar">
        <div class="container text-nowrap overflow-hidden">
            <span class="trending-title">HASTAGS:</span>
            @foreach ($hastags as $hastag)
                <a href="#" class="trending-item small">{{ Str::ucfirst($hastag->tag_name) }}</a>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
