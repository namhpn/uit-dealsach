<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle) ?> | DealSach</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: "Be Vietnam Pro", sans-serif; }
        body { min-height: 100vh; background: #f5f3ec; color: #14211d; display: grid; place-items: center; }
        .login-panel { width: min(440px, calc(100vw - 2rem)); background: #fffdf7; border: 1px solid #ded8ca; border-radius: 8px; box-shadow: 0 24px 70px rgba(20,33,29,.12); }
        .btn-admin { background: #0f5c45; border-color: #0f5c45; color: #fff; }
        .btn-admin:hover { background: #0a4634; border-color: #0a4634; color: #fff; }
    </style>
</head>
<body>
<main class="login-panel p-4">
    <div class="text-center mb-4">
        <div class="fs-1 text-success"><i class="bi bi-book-half"></i></div>
        <h1 class="h4 fw-bold mb-1">DealSach Admin</h1>
        <p class="text-muted mb-0">Khu vực quản trị nội bộ</p>
    </div>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= site_url(($adminPath ?? 'ds-admin') . '/login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label fw-semibold" for="username">Tài khoản</label>
            <input class="form-control form-control-lg" id="username" name="username" value="<?= old('username') ?>" autocomplete="username" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" for="password">Mật khẩu</label>
            <input class="form-control form-control-lg" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-admin btn-lg w-100" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</button>
    </form>
    <p class="text-muted small mt-3 mb-0">Demo mặc định: <code>admin</code> / <code>123456</code></p>
</main>
</body>
</html>
