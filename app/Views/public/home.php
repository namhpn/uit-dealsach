<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<style>
    /* ── Hero Section ── */
    .ds-hero {
        background: linear-gradient(135deg, #1a73e8 0%, #6610f2 100%);
        color: #fff;
        padding: 4rem 0 3.5rem;
        position: relative;
        overflow: hidden;
    }
    .ds-hero::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 60px;
        background: var(--ds-bg, #f8f9fa);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .ds-hero h1 {
        font-weight: 700;
        font-size: 2.4rem;
        margin-bottom: .75rem;
    }
    .ds-hero .lead {
        font-weight: 400;
        opacity: .9;
        font-size: 1.15rem;
    }
    .ds-hero-search {
        max-width: 560px;
        margin: 1.75rem auto 0;
    }
    .ds-hero-search .form-control {
        border-radius: 2rem 0 0 2rem;
        border: none;
        padding: .75rem 1.25rem;
        font-size: 1rem;
    }
    .ds-hero-search .btn {
        border-radius: 0 2rem 2rem 0;
        background: #ff6d00;
        border: none;
        color: #fff;
        padding: .75rem 1.5rem;
        font-weight: 600;
    }
    .ds-hero-search .btn:hover { background: #e65100; }

    /* ── Stats Row ── */
    .ds-stat-card {
        background: #fff;
        border-radius: .75rem;
        padding: 1.25rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        transition: transform .2s, box-shadow .2s;
    }
    .ds-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,.1);
    }
    .ds-stat-card .stat-icon {
        font-size: 2rem;
        background: linear-gradient(135deg, #1a73e8, #6610f2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .ds-stat-card .stat-value { font-weight: 700; font-size: 1.5rem; }
    .ds-stat-card .stat-label { color: #6c757d; font-size: .82rem; }

    /* ── Section headings ── */
    .ds-section-title {
        font-weight: 700;
        font-size: 1.4rem;
        position: relative;
        display: inline-block;
        padding-bottom: .4rem;
    }
    .ds-section-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 48px;
        height: 3px;
        border-radius: 2px;
        background: linear-gradient(90deg, #1a73e8, #6610f2);
    }

    /* ── Book card ── */
    .ds-book-card {
        border: none;
        border-radius: .75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        transition: transform .2s, box-shadow .2s;
        height: 100%;
    }
    .ds-book-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
    }
    .ds-book-card .card-img-top {
        height: 220px;
        object-fit: cover;
        background: #eee;
    }
    .ds-book-card .card-body { padding: 1rem 1.1rem; }
    .ds-book-card .card-title {
        font-weight: 600;
        font-size: .95rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ds-book-card .price-highlight {
        color: #e53935;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .ds-book-card .price-original {
        text-decoration: line-through;
        color: #999;
        font-size: .85rem;
    }
    .ds-book-card .badge-retailer {
        font-size: .7rem;
        font-weight: 500;
    }

    /* ── Retailer logos strip ── */
    .ds-retailer-strip img {
        height: 36px;
        filter: grayscale(100%);
        opacity: .5;
        transition: filter .3s, opacity .3s;
    }
    .ds-retailer-strip img:hover { filter: grayscale(0); opacity: 1; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ════════════ Hero ════════════ -->
<section class="ds-hero text-center" id="heroSection">
    <div class="container position-relative" style="z-index:2;">
        <h1>So sánh giá sách<br class="d-md-none"> trực tuyến tại Việt Nam</h1>
        <p class="lead">Tìm sách giá tốt nhất từ Fahasa, Tiki, Shopee và Nhà sách Phương Nam — tất cả trên một trang.</p>

        <form class="ds-hero-search d-flex mx-auto" action="<?= site_url('sach') ?>" method="get" id="heroSearchForm">
            <input class="form-control" type="search" name="q"
                   placeholder="Nhập tên sách, tác giả hoặc ISBN…" aria-label="Tìm kiếm sách">
            <button class="btn" type="submit">
                <i class="bi bi-search me-1"></i>Tìm
            </button>
        </form>
    </div>
</section>

<!-- ════════════ Stats Row ════════════ -->
<section class="container mt-5" id="statsSection">
    <div class="row g-3 row-cols-2 row-cols-md-4">
        <div class="col">
            <div class="ds-stat-card">
                <div class="stat-icon"><i class="bi bi-book"></i></div>
                <div class="stat-value mt-1">0</div>
                <div class="stat-label">Đầu sách</div>
            </div>
        </div>
        <div class="col">
            <div class="ds-stat-card">
                <div class="stat-icon"><i class="bi bi-shop"></i></div>
                <div class="stat-value mt-1">4</div>
                <div class="stat-label">Nhà bán lẻ</div>
            </div>
        </div>
        <div class="col">
            <div class="ds-stat-card">
                <div class="stat-icon"><i class="bi bi-graph-down-arrow"></i></div>
                <div class="stat-value mt-1">0</div>
                <div class="stat-label">Giảm giá hôm nay</div>
            </div>
        </div>
        <div class="col">
            <div class="ds-stat-card">
                <div class="stat-icon"><i class="bi bi-bell"></i></div>
                <div class="stat-value mt-1">0</div>
                <div class="stat-label">Theo dõi giá</div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════ Featured Books ════════════ -->
<section class="container mt-5" id="featuredSection">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="ds-section-title">Sách nổi bật</h2>
        <a href="<?= site_url('sach') ?>" class="text-decoration-none fw-semibold">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        <!-- Placeholder cards – replace with dynamic data -->
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="col">
            <div class="card ds-book-card">
                <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted">
                    <i class="bi bi-image fs-1"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Tên sách mẫu <?= $i ?></h5>
                    <p class="mb-1 text-muted" style="font-size:.82rem;">Tác giả mẫu</p>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="price-highlight">—</span>
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <span class="badge bg-primary bg-opacity-10 text-primary badge-retailer">Fahasa</span>
                        <span class="badge bg-success bg-opacity-10 text-success badge-retailer">Tiki</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</section>

<!-- ════════════ Latest Books ════════════ -->
<section class="container mt-5 mb-5" id="latestSection">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="ds-section-title">Sách mới cập nhật</h2>
        <a href="<?= site_url('sach') ?>" class="text-decoration-none fw-semibold">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        <?php for ($i = 5; $i <= 8; $i++): ?>
        <div class="col">
            <div class="card ds-book-card">
                <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted">
                    <i class="bi bi-image fs-1"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Tên sách mẫu <?= $i ?></h5>
                    <p class="mb-1 text-muted" style="font-size:.82rem;">Tác giả mẫu</p>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="price-highlight">—</span>
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <span class="badge bg-warning bg-opacity-10 text-warning badge-retailer">Shopee</span>
                        <span class="badge bg-info bg-opacity-10 text-info badge-retailer">Phương Nam</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</section>

<!-- ════════════ Retailer Strip ════════════ -->
<section class="container mb-5" id="retailerSection">
    <div class="text-center">
        <p class="text-muted fw-semibold mb-3" style="font-size:.85rem;">NGUỒN DỮ LIỆU TỪ</p>
        <div class="ds-retailer-strip d-flex justify-content-center align-items-center gap-4 flex-wrap">
            <span class="fw-bold text-muted">Fahasa</span>
            <span class="fw-bold text-muted">Tiki</span>
            <span class="fw-bold text-muted">Shopee</span>
            <span class="fw-bold text-muted">Phương Nam</span>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
