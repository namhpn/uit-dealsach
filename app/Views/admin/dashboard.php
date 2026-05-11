<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="alert alert-primary border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
    <i class="bi bi-speedometer2 fs-3 text-primary"></i>
    <div>
        <strong>Xin chào, <?= esc(session()->get('admin_display_name') ?? 'Admin') ?>!</strong><br>
        <span class="text-muted small">Tổng quan nhanh cho dữ liệu demo DealSach.</span>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Sách', $metrics['books'] ?? 0, 'bi-book', 'primary'],
        ['Nhà bán', $metrics['retailers'] ?? 0, 'bi-shop', 'success'],
        ['Lượt giá', $metrics['offers'] ?? 0, 'bi-tags', 'warning'],
        ['Theo dõi giá', $metrics['trackingRules'] ?? 0, 'bi-bell', 'info'],
        ['Chuyển hướng', $metrics['clicks'] ?? 0, 'bi-cursor', 'secondary'],
        ['Cảnh báo hôm nay', $metrics['alertsToday'] ?? 0, 'bi-envelope-check', 'danger'],
        ['Job lỗi 24h', $metrics['failedJobs'] ?? 0, 'bi-exclamation-triangle', 'danger'],
        ['Đăng nhập 24h', $metrics['recentSignIns'] ?? 0, 'bi-person-check', 'primary'],
    ];
    ?>
    <?php foreach ($cards as [$label, $value, $icon, $color]): ?>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded bg-<?= esc($color) ?> bg-opacity-10 text-<?= esc($color) ?> d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi <?= esc($icon) ?> fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4"><?= number_format((int) $value, 0, ',', '.') ?></div>
                        <div class="text-muted small"><?= esc($label) ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Crawl gần đây</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Trạng thái</th>
                        <th>Sản phẩm</th>
                        <th>Hoàn tất</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($recentCrawls === []): ?>
                        <tr><td class="text-center text-muted py-3" colspan="4">Chưa có dữ liệu crawl.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recentCrawls as $job): ?>
                        <tr>
                            <td>#<?= (int) $job['id'] ?></td>
                            <td><span class="badge text-bg-light border"><?= esc($job['status']) ?></span></td>
                            <td><?= number_format((int) $job['total_items_processed'], 0, ',', '.') ?></td>
                            <td><?= esc($job['completed_at'] ?? $job['created_at'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Thao tác nhanh</div>
            <div class="card-body d-flex flex-wrap gap-2">
                <?php $adminPath = env('dealsach.adminPath', 'ds-admin'); ?>
                <a class="btn btn-primary btn-sm" href="<?= site_url($adminPath . '/books/new') ?>">
                    <i class="bi bi-plus-circle me-1"></i>Thêm sách
                </a>
                <a class="btn btn-outline-primary btn-sm" href="<?= site_url($adminPath . '/books') ?>">
                    <i class="bi bi-journal-bookmark-fill me-1"></i>Danh sách
                </a>
                <a class="btn btn-outline-success btn-sm" href="<?= site_url($adminPath . '/exports/books.csv') ?>">
                    <i class="bi bi-filetype-csv me-1"></i>CSV sách
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
