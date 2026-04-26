<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle ?? 'Quản trị') ?> | DealSach Admin</title>

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
            --admin-sidebar-w: 260px;
            --admin-primary: #4f46e5;
            --admin-primary-hover: #4338ca;
            --admin-sidebar-bg: #1e1e2f;
            --admin-sidebar-text: rgba(255,255,255,.7);
            --admin-sidebar-active: rgba(255,255,255,1);
            --admin-sidebar-active-bg: rgba(255,255,255,.08);
            --admin-bg: #f1f5f9;
            --admin-card-bg: #fff;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background-color: var(--admin-bg);
            margin: 0;
            overflow-x: hidden;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--admin-sidebar-w);
            height: 100vh;
            background: var(--admin-sidebar-bg);
            color: var(--admin-sidebar-text);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
        }
        .admin-sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,.08);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .admin-sidebar .sidebar-brand i { color: #818cf8; font-size: 1.3rem; }

        .admin-sidebar .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: .75rem 0;
        }
        .admin-sidebar .nav-section-label {
            padding: .5rem 1.5rem;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.35);
            margin-top: .5rem;
        }
        .admin-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .6rem 1.5rem;
            color: var(--admin-sidebar-text);
            font-size: .9rem;
            font-weight: 500;
            border-radius: 0;
            transition: background .15s, color .15s;
        }
        .admin-sidebar .nav-link:hover {
            background: var(--admin-sidebar-active-bg);
            color: var(--admin-sidebar-active);
        }
        .admin-sidebar .nav-link.active {
            background: var(--admin-sidebar-active-bg);
            color: var(--admin-sidebar-active);
            border-left: 3px solid #818cf8;
        }
        .admin-sidebar .nav-link i { font-size: 1.1rem; width: 1.3rem; text-align: center; }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
            font-size: .82rem;
        }

        /* ── Topbar ── */
        .admin-topbar {
            height: 60px;
            background: var(--admin-card-bg);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .admin-topbar .topbar-title { font-weight: 600; font-size: 1.05rem; }

        /* ── Main wrapper ── */
        .admin-main { margin-left: var(--admin-sidebar-w); }
        .admin-content { padding: 1.5rem; }

        /* ── Sidebar toggle for mobile ── */
        .sidebar-toggle { display: none; }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 1035;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .sidebar-toggle { display: inline-flex; }
            .sidebar-overlay.show { display: block; }
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>

<?php
    // Determine admin path from env or fall back to 'ds-admin'
    $adminPath = env('dealsach.adminPath', 'ds-admin');
?>

<!-- ════════════ Sidebar Overlay (mobile) ════════════ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ════════════ Sidebar ════════════ -->
<aside class="admin-sidebar" id="adminSidebar">
    <a class="sidebar-brand" href="<?= site_url($adminPath) ?>">
        <i class="bi bi-book-half"></i> DealSach Admin
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Tổng quan</div>
        <a class="nav-link <?= (url_is($adminPath)) ? 'active' : '' ?>"
           href="<?= site_url($adminPath) ?>">
            <i class="bi bi-speedometer2"></i> Bảng điều khiển
        </a>

        <div class="nav-section-label">Quản lý nội dung</div>
        <a class="nav-link <?= (url_is($adminPath . '/books*')) ? 'active' : '' ?>"
           href="<?= site_url($adminPath . '/books') ?>">
            <i class="bi bi-journal-bookmark-fill"></i> Quản lý sách
        </a>

        <div class="nav-section-label">Xuất dữ liệu</div>
        <a class="nav-link <?= (url_is($adminPath . '/exports*')) ? 'active' : '' ?>"
           href="<?= site_url($adminPath . '/exports/books.csv') ?>">
            <i class="bi bi-filetype-csv"></i> Xuất CSV sách
        </a>
        <a class="nav-link"
           href="<?= site_url($adminPath . '/exports/activity.csv') ?>">
            <i class="bi bi-activity"></i> Xuất hoạt động
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-person-circle"></i>
            <span><?= esc(session()->get('admin_display_name') ?? 'Admin') ?></span>
        </div>
        <form action="<?= site_url($adminPath . '/logout') ?>" method="post" id="logoutForm">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-left me-1"></i>Đăng xuất
            </button>
        </form>
    </div>
</aside>

<!-- ════════════ Main Area ════════════ -->
<div class="admin-main">
    <!-- Top Bar -->
    <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle" type="button" id="sidebarToggleBtn"
                    aria-label="Mở menu">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="topbar-title"><?= esc($pageTitle ?? 'Bảng điều khiển') ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= site_url('/') ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="bi bi-globe me-1"></i>Xem trang công khai
            </a>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="admin-content pb-0">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="admin-content pb-0">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="admin-content">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    // Sidebar toggle for mobile
    (function () {
        const sidebar  = document.getElementById('adminSidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        if (!sidebar || !overlay || !toggleBtn) return;

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    })();
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
