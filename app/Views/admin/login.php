<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Đăng nhập quản trị | DealSach</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --login-gradient-start: #1e1e2f;
            --login-gradient-end: #2d2b55;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, var(--login-gradient-start), var(--login-gradient-end));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 16px 48px rgba(0,0,0,.25);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            color: #fff;
        }
        .login-header i { font-size: 2.5rem; margin-bottom: .5rem; }
        .login-header h1 { font-size: 1.3rem; font-weight: 700; margin-bottom: .25rem; }
        .login-header p { font-size: .85rem; opacity: .8; margin-bottom: 0; }

        .login-body { padding: 2rem; }

        .login-body .form-label { font-size: .85rem; font-weight: 500; }
        .login-body .form-control { border-radius: .5rem; }
        .login-body .form-control:focus {
            box-shadow: 0 0 0 3px rgba(79,70,229,.25);
            border-color: #4f46e5;
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: .5rem;
            color: #fff;
            font-weight: 600;
            padding: .65rem;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }

        .login-footer {
            text-align: center;
            padding: 0 2rem 1.5rem;
        }
        .login-footer a { font-size: .82rem; color: #6c757d; text-decoration: none; }
        .login-footer a:hover { color: #4f46e5; }
    </style>
</head>
<body>

<div class="login-card" id="loginCard">
    <!-- Header -->
    <div class="login-header">
        <i class="bi bi-shield-lock-fill d-block"></i>
        <h1>Quản trị DealSach</h1>
        <p>Đăng nhập để tiếp tục</p>
    </div>

    <!-- Body -->
    <div class="login-body">
        <!-- Error message -->
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger py-2" role="alert" style="font-size:.85rem;">
            <i class="bi bi-exclamation-triangle me-1"></i><?= esc(session()->getFlashdata('error')) ?>
        </div>
        <?php endif; ?>

        <?php
            $adminPath = env('dealsach.adminPath', 'ds-admin');
        ?>

        <form action="<?= site_url($adminPath . '/login') ?>" method="post" id="adminLoginForm">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="loginUsername" class="form-label">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="loginUsername" name="username"
                           placeholder="admin" autocomplete="username" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="loginPassword" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="loginPassword" name="password"
                           placeholder="••••••••" autocomplete="current-password" required>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-login" id="loginSubmitBtn">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="login-footer">
        <a href="<?= site_url('/') ?>"><i class="bi bi-arrow-left me-1"></i>Quay về trang chủ</a>
    </div>
</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
