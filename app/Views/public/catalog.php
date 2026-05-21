<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<style>
    .ds-catalog-head {
        background: var(--ds-paper);
        border-bottom: 1px solid var(--ds-line);
    }

    .ds-filter-panel {
        position: sticky;
        top: 82px;
    }

    .ds-book-card {
        height: 100%;
        transition: transform .18s ease, box-shadow .18s ease;
        overflow: hidden;
    }

    .ds-book-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(22, 32, 29, .1);
    }

    .ds-cover {
        width: 100%;
        aspect-ratio: 3 / 4;
        object-fit: cover;
    }

    .ds-book-title {
        min-height: 2.8rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 991.98px) {
        .ds-filter-panel { position: static; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$selectedRetailers = (array) ($filters['retailer'] ?? []);
$currentPage = (int) $result['page'];
$totalPages = (int) $result['totalPages'];
$makePageUrl = static function (int $page) use ($queryParams): string {
    $params = $queryParams;
    $params['page'] = $page;

    return site_url('sach') . '?' . http_build_query($params);
};
?>

<header class="ds-catalog-head py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-2">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh mục sách</li>
            </ol>
        </nav>
        <div class="row g-3 align-items-end">
            <div class="col-lg-7">
                <h1 class="fw-bold mb-2">Danh mục sách</h1>
                <p class="text-muted mb-0">Tìm kiếm, lọc và so sánh giá bán mới nhất từ các nhà bán đã import.</p>
            </div>
            <div class="col-lg-5">
                <form class="d-flex" action="<?= site_url('sach') ?>" method="get">
                    <input class="form-control form-control-lg" name="q" value="<?= esc($filters['q'] ?? '') ?>" type="search" placeholder="Tên sách, tác giả, ISBN">
                    <button class="btn ds-search-btn ms-2 px-4" type="submit" title="Tìm">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<div class="container py-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="ds-card ds-filter-panel p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-funnel me-1"></i>Bộ lọc</h2>
                    <a class="small text-decoration-none" href="<?= site_url('sach') ?>">Xóa lọc</a>
                </div>

                <form action="<?= site_url('sach') ?>" method="get">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="filterKeyword">Từ khóa</label>
                        <input class="form-control" id="filterKeyword" type="search" name="q" value="<?= esc($filters['q'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="filterCategory">Danh mục</label>
                        <select class="form-select" id="filterCategory" name="category">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= esc($category['slug']) ?>" <?= (($filters['category'] ?? '') === $category['slug']) ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nhà bán</label>
                        <?php foreach ($retailers as $retailer): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="retailer[]" id="retailer<?= (int) $retailer['id'] ?>"
                                       value="<?= esc($retailer['slug']) ?>" <?= in_array($retailer['slug'], $selectedRetailers, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="retailer<?= (int) $retailer['id'] ?>"><?= esc($retailer['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="filterStock">Tình trạng</label>
                        <select class="form-select" id="filterStock" name="stock">
                            <option value="" <?= (($filters['stock'] ?? '') === '') ? 'selected' : '' ?>>Tất cả</option>
                            <option value="in_stock" <?= (($filters['stock'] ?? '') === 'in_stock') ? 'selected' : '' ?>>Còn hàng</option>
                            <option value="out_of_stock" <?= (($filters['stock'] ?? '') === 'out_of_stock') ? 'selected' : '' ?>>Hết hàng</option>
                        </select>
                    </div>

                    <button class="btn ds-search-btn w-100" type="submit">
                        <i class="bi bi-check2 me-1"></i>Áp dụng
                    </button>
                </form>
            </div>
        </aside>

        <section class="col-lg-9">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">Kết quả</h2>
                    <p class="text-muted mb-0 small">
                        Hiển thị <?= count($result['books']) ?> trên <?= number_format((int) $result['total'], 0, ',', '.') ?> sách
                    </p>
                </div>
                <?php if (($filters['q'] ?? '') !== ''): ?>
                    <span class="badge rounded-pill text-bg-light border px-3 py-2">Từ khóa: <?= esc($filters['q']) ?></span>
                <?php endif; ?>
            </div>

            <?php if ($result['books'] === []): ?>
                <div class="ds-card p-5 text-center">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h3 class="h5 fw-bold mt-3">Không tìm thấy sách phù hợp</h3>
                    <p class="text-muted mb-0">Thử đổi từ khóa hoặc bỏ bớt bộ lọc.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
                    <?php foreach ($result['books'] as $book): ?>
                        <div class="col">
                            <article class="ds-card ds-book-card">
                                <a class="text-decoration-none text-reset" href="<?= site_url('sach/' . $book['slug']) ?>">
                                    <?php if (! empty($book['cover_image_url'])): ?>
                                        <img class="ds-cover" src="<?= esc($book['cover_image_url']) ?>" alt="<?= esc($book['title']) ?>">
                                    <?php else: ?>
                                        <div class="ds-cover ds-empty-cover d-flex align-items-center justify-content-center">
                                            <i class="bi bi-book fs-1"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div class="p-3">
                                    <a class="text-decoration-none text-reset" href="<?= site_url('sach/' . $book['slug']) ?>">
                                        <h3 class="h6 fw-bold ds-book-title mb-1"><?= esc($book['title']) ?></h3>
                                    </a>
                                    <p class="small text-muted mb-2"><?= esc($book['authors']) ?></p>
                                    <div class="d-flex align-items-end justify-content-between gap-2">
                                        <div>
                                            <?php if ($book['lowest_price'] !== null): ?>
                                                <div class="ds-price"><?= format_vnd($book['lowest_price']) ?></div>
                                                <div class="small text-muted"><?= (int) $book['available_offer_count'] ?> nơi còn hàng</div>
                                            <?php else: ?>
                                                <div class="fw-semibold text-muted">Chưa có giá</div>
                                                <div class="small text-muted"><?= (int) $book['offer_count'] ?> lượt giá</div>
                                            <?php endif; ?>
                                        </div>
                                        <a class="btn btn-sm btn-outline-success" href="<?= site_url('sach/' . $book['slug']) ?>" title="Xem chi tiết">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4" aria-label="Phân trang danh mục">
                        <ul class="pagination justify-content-center flex-wrap">
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $currentPage <= 1 ? '#' : esc($makePageUrl($currentPage - 1)) ?>">Trước</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= esc($makePageUrl($i)) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : esc($makePageUrl($currentPage + 1)) ?>">Sau</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
