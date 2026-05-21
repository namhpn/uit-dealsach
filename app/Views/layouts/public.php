<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= esc($metaDescription ?? 'DealSach - So sánh giá sách trực tuyến tại Việt Nam từ Fahasa, Tiki, Shopee và Nhà sách Phương Nam.') ?>">
    <title><?= esc($pageTitle ?? 'DealSach') ?> | DealSach</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --ds-ink: #16201d;
            --ds-muted: #64706b;
            --ds-bg: #f6f4ee;
            --ds-paper: #fffdf7;
            --ds-line: #ded8ca;
            --ds-green: #116149;
            --ds-green-dark: #0b4635;
            --ds-red: #c43b2f;
            --ds-gold: #d89b28;
        }

        * { font-family: "Be Vietnam Pro", sans-serif; letter-spacing: 0; }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--ds-bg);
            color: var(--ds-ink);
        }

        main { flex: 1 0 auto; }

        .ds-navbar {
            background: rgba(255, 253, 247, .96);
            border-bottom: 1px solid var(--ds-line);
            backdrop-filter: blur(14px);
        }

        .navbar-brand {
            color: var(--ds-green-dark) !important;
            font-weight: 800;
        }

        .ds-navbar .nav-link {
            color: var(--ds-muted);
            font-weight: 600;
        }

        .ds-navbar .nav-link:hover,
        .ds-navbar .nav-link.active {
            color: var(--ds-green-dark);
        }

        .ds-icon-btn,
        .ds-search-btn {
            background: var(--ds-green);
            border-color: var(--ds-green);
            color: #fff;
        }

        .ds-icon-btn:hover,
        .ds-search-btn:hover {
            background: var(--ds-green-dark);
            border-color: var(--ds-green-dark);
            color: #fff;
        }

        .ds-global-search {
            max-width: 380px;
        }

        .ds-global-search .form-control {
            border-color: var(--ds-line);
            background: #fff;
        }

        .ds-footer {
            background: #17231f;
            color: rgba(255,255,255,.72);
            font-size: .9rem;
        }

        .ds-footer a {
            color: rgba(255,255,255,.84);
            text-decoration: none;
        }

        .ds-footer a:hover { color: #fff; }

        .ds-card {
            background: var(--ds-paper);
            border: 1px solid var(--ds-line);
            border-radius: 8px;
        }

        .ds-price {
            color: var(--ds-red);
            font-weight: 800;
        }

        .ds-empty-cover {
            background:
                linear-gradient(135deg, rgba(17,97,73,.1), rgba(216,155,40,.12)),
                var(--ds-paper);
            border: 1px solid var(--ds-line);
            color: var(--ds-green-dark);
        }

        .pagination .page-link {
            color: var(--ds-green);
            border-color: var(--ds-line);
        }

        .pagination .active .page-link {
            background: var(--ds-green);
            border-color: var(--ds-green);
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>
<nav class="navbar navbar-expand-lg ds-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('/') ?>">
            <i class="bi bi-book-half"></i><span>DealSach</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav"
                aria-controls="publicNav" aria-expanded="false" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= url_is('sach*') ? 'active' : '' ?>" href="<?= site_url('sach') ?>">
                        <i class="bi bi-search me-1"></i>Danh mục sách
                    </a>
                </li>
            </ul>
            <form class="d-flex ds-global-search" role="search" action="<?= site_url('sach') ?>" method="get">
                <input class="form-control" type="search" name="q" placeholder="Tên sách, tác giả, ISBN" aria-label="Tìm sách">
                <button class="btn ds-search-btn ms-2" type="submit" title="Tìm kiếm">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

<?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
<div class="container mt-3">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<main>
    <?= $this->renderSection('content') ?>
</main>

<footer class="ds-footer py-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h6 class="text-white fw-bold mb-2"><i class="bi bi-book-half me-1"></i>DealSach</h6>
                <p class="mb-0">So sánh giá sách trực tuyến tại Việt Nam. Dữ liệu demo phục vụ đồ án UIT IS207.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-semibold mb-2">Liên kết</h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= site_url('/') ?>">Trang chủ</a></li>
                    <li><a href="<?= site_url('sach') ?>">Danh mục sách</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-semibold mb-2">Nguồn dữ liệu</h6>
                <p class="mb-0">Fahasa, Nhà sách Phương Nam, Tiki, Shopee.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
