<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle ?? 'Quản trị') ?> | DealSach Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-sidebar-w: 260px;
            --admin-ink: #14211d;
            --admin-muted: #68746f;
            --admin-bg: #f5f3ec;
            --admin-panel: #fffdf7;
            --admin-line: #ded8ca;
            --admin-green: #0f5c45;
        }
        * { font-family: "Be Vietnam Pro", sans-serif; letter-spacing: 0; }
        body { margin: 0; background: var(--admin-bg); color: var(--admin-ink); }
        .admin-sidebar {
            position: fixed; inset: 0 auto 0 0; width: var(--admin-sidebar-w);
            background: #15231f; color: rgba(255,255,255,.74); z-index: 1040;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            color: #fff; text-decoration: none; font-weight: 800; font-size: 1.2rem;
            padding: 1.15rem 1.35rem; border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-nav { flex: 1; padding: .75rem; overflow-y: auto; }
        .nav-section-label { color: rgba(255,255,255,.38); font-size: .72rem; text-transform: uppercase; font-weight: 800; padding: .8rem .65rem .35rem; }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,.72); border-radius: 8px; padding: .65rem .75rem; font-weight: 600;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: rgba(255,255,255,.1); color: #fff; }
        .sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255,255,255,.08); }
        .admin-main { margin-left: var(--admin-sidebar-w); min-height: 100vh; }
        .admin-topbar {
            height: 64px; background: rgba(255,253,247,.94); border-bottom: 1px solid var(--admin-line);
            display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; position: sticky; top: 0; z-index: 1030;
        }
        .admin-content { padding: 1.5rem; }
        .admin-card { background: var(--admin-panel); border: 1px solid var(--admin-line); border-radius: 8px; }
        .btn-admin { background: var(--admin-green); border-color: var(--admin-green); color: #fff; }
        .btn-admin:hover { background: #0a4634; border-color: #0a4634; color: #fff; }
        .sidebar-toggle, .sidebar-overlay { display: none; }
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); transition: transform .2s ease; }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .sidebar-toggle { display: inline-flex; }
            .sidebar-overlay.show { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.38); z-index: 1035; }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
<?php $adminPath = env('dealsach.adminPath', 'ds-admin'); ?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="admin-sidebar" id="adminSidebar">
    <a class="sidebar-brand d-flex align-items-center gap-2" href="<?= site_url($adminPath) ?>">
        <i class="bi bi-book-half"></i><span>DealSach Admin</span>
    </a>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Tổng quan</div>
        <a class="nav-link <?= url_is($adminPath) ? 'active' : '' ?>" href="<?= site_url($adminPath) ?>">
            <i class="bi bi-speedometer2 me-2"></i>Bảng điều khiển
        </a>
        <div class="nav-section-label">Nội dung</div>
        <a class="nav-link <?= url_is($adminPath . '/books*') ? 'active' : '' ?>" href="<?= site_url($adminPath . '/books') ?>">
            <i class="bi bi-journal-bookmark me-2"></i>Quản lý sách
        </a>
        <div class="nav-section-label">Báo cáo</div>
        <a class="nav-link" href="<?= site_url($adminPath . '/exports/books.csv') ?>"><i class="bi bi-filetype-csv me-2"></i>CSV sách</a>
        <a class="nav-link" href="<?= site_url($adminPath . '/exports/activity.csv') ?>"><i class="bi bi-activity me-2"></i>CSV hoạt động</a>
    </nav>
    <div class="sidebar-footer">
        <div class="small mb-2"><i class="bi bi-person-circle me-1"></i><?= esc(session()->get('admin_display_name') ?? 'Admin') ?></div>
        <form action="<?= site_url($adminPath . '/logout') ?>" method="post">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-light w-100" type="submit"><i class="bi bi-box-arrow-left me-1"></i>Đăng xuất</button>
        </form>
    </div>
</aside>
<div class="admin-main">
    <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle" type="button" id="sidebarToggleBtn" aria-label="Mở menu"><i class="bi bi-list"></i></button>
            <strong><?= esc($pageTitle ?? 'Quản trị') ?></strong>
        </div>
        <a class="btn btn-sm btn-outline-success" href="<?= site_url('/') ?>" target="_blank"><i class="bi bi-globe me-1"></i>Trang công khai</a>
    </header>
    <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
        <div class="admin-content pb-0">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <main class="admin-content"><?= $this->renderSection('content') ?></main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggle = document.getElementById('sidebarToggleBtn');
  if (!sidebar || !overlay || !toggle) return;
  toggle.addEventListener('click', () => { sidebar.classList.toggle('show'); overlay.classList.toggle('show'); });
  overlay.addEventListener('click', () => { sidebar.classList.remove('show'); overlay.classList.remove('show'); });
})();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
