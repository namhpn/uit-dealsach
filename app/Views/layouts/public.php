<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $metaDescription ?? 'DealSach — So sánh giá sách trực tuyến tại Việt Nam. Tìm sách giá tốt nhất từ Fahasa, Tiki, Shopee, Nhà sách Phương Nam.' ?>">
    <title><?= esc($pageTitle ?? 'DealSach') ?> | DealSach</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YcnS/GqVl2h4TFP3xI4xCPpN4YKgrk00f7F" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ds-primary: #1a73e8;
            --ds-primary-dark: #1557b0;
            --ds-accent: #ff6d00;
            --ds-bg: #f8f9fa;
            --ds-card-bg: #ffffff;
            --ds-text: #212529;
            --ds-text-muted: #6c757d;
            --ds-border: #dee2e6;
            --ds-gradient-start: #1a73e8;
            --ds-gradient-end: #6610f2;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background-color: var(--ds-bg);
            color: var(--ds-text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .ds-navbar {
            background: linear-gradient(135deg, var(--ds-gradient-start), var(--ds-gradient-end));
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
        }
        .ds-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -.02em;
            color: #fff !important;
        }
        .ds-navbar .navbar-brand i { color: var(--ds-accent); }
        .ds-navbar .nav-link {
            color: rgba(255,255,255,.85) !important;
            font-weight: 500;
            transition: color .2s;
        }
        .ds-navbar .nav-link:hover,
        .ds-navbar .nav-link.active { color: #fff !important; }

        .ds-search-box { max-width: 360px; }
        .ds-search-box .form-control {
            border-radius: 2rem 0 0 2rem;
            border: none;
        }
        .ds-search-box .btn {
            border-radius: 0 2rem 2rem 0;
            background: var(--ds-accent);
            border: none;
            color: #fff;
        }

        /* ── Content ── */
        main { flex: 1 0 auto; }

        /* ── Footer ── */
        .ds-footer {
            background: #1e1e2f;
            color: rgba(255,255,255,.6);
            font-size: .85rem;
        }
        .ds-footer a {
            color: rgba(255,255,255,.75);
            text-decoration: none;
        }
        .ds-footer a:hover { color: #fff; }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>

<!-- ════════════ Navbar ════════════ -->
<nav class="navbar navbar-expand-lg ds-navbar sticky-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">
            <i class="bi bi-book-half me-1"></i>DealSach
        </a>
        <button class="navbar-toggler border-0 text-white" type="button"
                data-bs-toggle="collapse" data-bs-target="#publicNav"
                aria-controls="publicNav" aria-expanded="false" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('/')) ? 'active' : '' ?>"
                       aria-current="page" href="<?= site_url('/') ?>">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (url_is('sach*')) ? 'active' : '' ?>"
                       href="<?= site_url('sach') ?>">
                        <i class="bi bi-search me-1"></i>Danh mục sách
                    </a>
                </li>
            </ul>
            <form class="d-flex ds-search-box" role="search" action="<?= site_url('sach') ?>" method="get" id="globalSearchForm">
                <input class="form-control" type="search" name="q"
                       placeholder="Tìm sách theo tên, tác giả, ISBN…" aria-label="Tìm kiếm">
                <button class="btn" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>
</nav>

<!-- ════════════ Flash Messages ════════════ -->
<?php if (session()->getFlashdata('success')): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
</div>
<?php endif; ?>

<!-- ════════════ Main Content ════════════ -->
<main>
    <?= $this->renderSection('content') ?>
</main>

<!-- ════════════ Footer ════════════ -->
<footer class="ds-footer py-4 mt-5" id="siteFooter">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h6 class="text-white fw-semibold mb-2"><i class="bi bi-book-half me-1"></i>DealSach</h6>
                <p class="mb-0">So sánh giá sách trực tuyến tại Việt Nam.<br>
                    Dự án đồ án môn học IS207 — UIT.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-semibold mb-2">Liên kết</h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= site_url('/') ?>">Trang chủ</a></li>
                    <li><a href="<?= site_url('sach') ?>">Danh mục sách</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-semibold mb-2">Nguồn dữ liệu</h6>
                <ul class="list-unstyled mb-0">
                    <li>Fahasa</li>
                    <li>Nhà sách Phương Nam</li>
                    <li>Tiki</li>
                    <li>Shopee</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <p class="text-center mb-0">&copy; <?= date('Y') ?> DealSach &mdash; UIT IS207. Mọi quyền được bảo lưu.</p>
    </div>
</footer>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
