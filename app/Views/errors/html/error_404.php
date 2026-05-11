<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>404 — Không tìm thấy trang | DealSach</title>

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
            --ds-primary: #1a73e8;
            --ds-primary-dark: #1557b0;
            --ds-accent: #ff6d00;
            --ds-bg: #f8f9fa;
            --ds-text: #212529;
            --ds-text-muted: #6c757d;
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

        main { flex: 1 0 auto; }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--ds-gradient-start), var(--ds-gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .error-icon {
            font-size: 4rem;
            color: var(--ds-accent);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .ds-footer {
            background: #1e1e2f;
            color: rgba(255,255,255,.6);
            font-size: .85rem;
        }
    </style>
</head>
<body>

<!-- ════════════ Navbar ════════════ -->
<nav class="navbar ds-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-book-half me-1"></i>DealSach
        </a>
    </div>
</nav>

<!-- ════════════ Error Content ════════════ -->
<main class="d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center py-5">
                <div class="error-icon mb-3">
                    <i class="bi bi-search"></i>
                </div>
                <h1 class="error-code mb-2">404</h1>
                <h2 class="fw-semibold mb-3">Không tìm thấy trang</h2>
                <p class="text-muted mb-4">
                    Trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.<br>
                    Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
                </p>

                <?php if (ENVIRONMENT !== 'production' && ! empty($message)) : ?>
                    <div class="alert alert-warning text-start small mb-4" role="alert">
                        <strong><i class="bi bi-bug me-1"></i>Chi tiết lỗi (development):</strong><br>
                        <?= nl2br(esc($message)) ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="/" class="btn btn-primary px-4">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </a>
                    <a href="/sach" class="btn btn-outline-primary px-4">
                        <i class="bi bi-search me-1"></i>Danh mục sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ════════════ Footer ════════════ -->
<footer class="ds-footer py-3 mt-auto">
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> DealSach &mdash; UIT IS207. Mọi quyền được bảo lưu.</p>
    </div>
</footer>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
