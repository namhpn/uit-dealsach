<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<style>
    .ds-hero {
        padding: 4.5rem 0 3rem;
        background:
            linear-gradient(90deg, rgba(246,244,238,.98) 0%, rgba(246,244,238,.78) 54%, rgba(246,244,238,.92) 100%),
            url("https://images.unsplash.com/photo-1519682337058-a94d519337bc?auto=format&fit=crop&w=1800&q=80") center/cover;
        border-bottom: 1px solid var(--ds-line);
    }

    .ds-hero h1 {
        max-width: 780px;
        font-size: clamp(2.2rem, 5vw, 4.8rem);
        line-height: 1.02;
        font-weight: 800;
    }

    .ds-hero-copy {
        max-width: 660px;
        color: var(--ds-muted);
        font-size: 1.05rem;
    }

    .ds-hero-search {
        max-width: 680px;
        background: var(--ds-paper);
        border: 1px solid var(--ds-line);
        border-radius: 8px;
        padding: .55rem;
        box-shadow: 0 18px 45px rgba(22, 32, 29, .08);
    }

    .ds-hero-search .form-control {
        border: 0;
        background: transparent;
        min-height: 48px;
    }

    .ds-stat {
        min-height: 128px;
        padding: 1.2rem;
    }

    .ds-stat .value {
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--ds-green-dark);
    }

    .ds-section-title {
        font-weight: 800;
        color: var(--ds-ink);
    }

    .ds-book-card {
        height: 100%;
        overflow: hidden;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .ds-book-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(22, 32, 29, .1);
    }

    .ds-cover {
        aspect-ratio: 3 / 4;
        object-fit: cover;
        width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="ds-hero">
    <div class="container">
        <p class="text-uppercase fw-bold mb-2" style="color: var(--ds-green); font-size:.82rem;">So sánh giá sách Việt Nam</p>
        <h1>DealSach tìm giá tốt nhất trước khi bạn đặt mua.</h1>
        <p class="ds-hero-copy mt-3 mb-4">
            Tìm sách theo tên, tác giả hoặc ISBN; xem giá từ Fahasa, Tiki, Shopee và Nhà sách Phương Nam trên cùng một trang.
        </p>

        <form class="ds-hero-search d-flex align-items-center" action="<?= site_url('sach') ?>" method="get">
            <i class="bi bi-search fs-5 ms-2" style="color: var(--ds-muted);"></i>
            <input class="form-control" type="search" name="q" placeholder="Ví dụ: Đắc Nhân Tâm, Nguyễn Nhật Ánh, 978..." aria-label="Tìm kiếm sách">
            <button class="btn ds-search-btn px-4" type="submit">Tìm</button>
        </form>
    </div>
</section>

<section class="container mt-4">
    <div class="row g-3 row-cols-2 row-cols-lg-4">
        <div class="col">
            <div class="ds-card ds-stat">
                <i class="bi bi-book fs-4" style="color: var(--ds-gold);"></i>
                <div class="value mt-2"><?= number_format((int) ($stats['books'] ?? 0), 0, ',', '.') ?></div>
                <div class="text-muted">Đầu sách</div>
            </div>
        </div>
        <div class="col">
            <div class="ds-card ds-stat">
                <i class="bi bi-shop fs-4" style="color: var(--ds-gold);"></i>
                <div class="value mt-2"><?= number_format((int) ($stats['retailers'] ?? 0), 0, ',', '.') ?></div>
                <div class="text-muted">Nhà bán</div>
            </div>
        </div>
        <div class="col">
            <div class="ds-card ds-stat">
                <i class="bi bi-tags fs-4" style="color: var(--ds-gold);"></i>
                <div class="value mt-2"><?= number_format((int) ($stats['offers'] ?? 0), 0, ',', '.') ?></div>
                <div class="text-muted">Lượt giá</div>
            </div>
        </div>
        <div class="col">
            <div class="ds-card ds-stat">
                <i class="bi bi-bell fs-4" style="color: var(--ds-gold);"></i>
                <div class="value mt-2"><?= number_format((int) ($stats['trackingRules'] ?? 0), 0, ',', '.') ?></div>
                <div class="text-muted">Theo dõi giá</div>
            </div>
        </div>
    </div>
</section>

<section class="container mt-5">
    <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
        <div>
            <p class="text-uppercase fw-bold mb-1" style="color: var(--ds-green); font-size:.78rem;">Gợi ý nhanh</p>
            <h2 class="ds-section-title h3 mb-0">Sách có giá tốt</h2>
        </div>
        <a class="btn btn-outline-success" href="<?= site_url('sach') ?>">
            Xem danh mục <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if ($featuredBooks === []): ?>
        <div class="ds-card p-5 text-center">
            <i class="bi bi-database-x fs-1 text-muted"></i>
            <p class="text-muted mt-3 mb-0">Chưa có dữ liệu sách. Hãy chạy migration và seed demo trước.</p>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            <?php foreach ($featuredBooks as $book): ?>
                <div class="col">
                    <a class="text-decoration-none text-reset" href="<?= site_url('sach/' . $book['slug']) ?>">
                        <article class="ds-card ds-book-card">
                            <?php if (! empty($book['cover_image_url'])): ?>
                                <img class="ds-cover" src="<?= esc($book['cover_image_url']) ?>" alt="<?= esc($book['title']) ?>">
                            <?php else: ?>
                                <div class="ds-cover ds-empty-cover d-flex align-items-center justify-content-center">
                                    <i class="bi bi-book fs-1"></i>
                                </div>
                            <?php endif; ?>
                            <div class="p-3">
                                <h3 class="h6 fw-bold mb-1"><?= esc($book['title']) ?></h3>
                                <p class="text-muted small mb-2"><?= esc($book['authors']) ?></p>
                                <?php if ($book['lowest_price'] !== null): ?>
                                    <div class="ds-price"><?= format_vnd($book['lowest_price']) ?></div>
                                <?php else: ?>
                                    <div class="text-muted fw-semibold">Chưa có giá</div>
                                <?php endif; ?>
                                <div class="small text-muted mt-1"><?= (int) $book['available_offer_count'] ?> nơi còn hàng</div>
                            </div>
                        </article>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="container mt-5 mb-5">
    <div class="ds-card p-4">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5">
                <h2 class="h4 fw-bold mb-1">Nguồn dữ liệu so sánh</h2>
                <p class="text-muted mb-0">DealSach gom giá theo mô hình import-first, không crawl trực tiếp khi người dùng mở trang.</p>
            </div>
            <div class="col-lg-7">
                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    <?php foreach ($retailers as $retailer): ?>
                        <span class="badge text-bg-light border px-3 py-2"><?= esc($retailer['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
