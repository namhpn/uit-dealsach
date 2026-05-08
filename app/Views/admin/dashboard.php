<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-start gap-3 mb-4">
    <div>
        <p class="text-uppercase fw-bold text-success small mb-1">Tổng quan vận hành</p>
        <h1 class="h3 fw-bold mb-0">Bảng điều khiển</h1>
    </div>
    <a class="btn btn-admin" href="<?= site_url(env('dealsach.adminPath', 'ds-admin') . '/books/new') ?>"><i class="bi bi-plus-lg me-1"></i>Thêm sách</a>
</div>

<div class="row g-3 row-cols-1 row-cols-md-2 row-cols-xl-4 mb-4">
    <?php
    $cards = [
        ['Sách', $metrics['books'], 'bi-book'],
        ['Retailer', $metrics['retailers'], 'bi-shop'],
        ['Snapshots', $metrics['snapshots'], 'bi-clock-history'],
        ['Click ra ngoài', $metrics['clicks'], 'bi-box-arrow-up-right'],
        ['Theo dõi giá', $metrics['trackingRules'], 'bi-bell'],
        ['Alert events', $metrics['alerts'], 'bi-envelope'],
        ['Job lỗi', $metrics['failedJobs'], 'bi-exclamation-triangle'],
        ['Crawl mới nhất', $metrics['latestCrawl'], 'bi-cloud-download'],
    ];
    ?>
    <?php foreach ($cards as [$label, $value, $icon]): ?>
        <div class="col">
            <div class="admin-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small"><?= esc($label) ?></span>
                    <i class="bi <?= esc($icon) ?> text-success"></i>
                </div>
                <div class="fs-4 fw-bold mt-2"><?= esc((string) $value) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="admin-card p-3">
            <h2 class="h5 fw-bold mb-3">Crawl gần đây</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>ID</th><th>Trạng thái</th><th>Số dòng</th><th>Hoàn tất</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentCrawls as $job): ?>
                        <tr>
                            <td>#<?= (int) $job['id'] ?></td>
                            <td><span class="badge text-bg-<?= $job['status'] === 'failed' ? 'danger' : 'success' ?>"><?= esc($job['status']) ?></span></td>
                            <td><?= (int) $job['total_items_processed'] ?></td>
                            <td class="text-muted small"><?= esc($job['completed_at'] ?? 'Đang chạy') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="admin-card p-3">
            <h2 class="h5 fw-bold mb-3">Đăng nhập gần đây</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Tài khoản</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentAuthLogs as $log): ?>
                        <tr>
                            <td><?= esc($log['username'] ?? 'unknown') ?></td>
                            <td><span class="badge text-bg-<?= $log['status'] === 'success' ? 'success' : ($log['status'] === 'logout' ? 'secondary' : 'danger') ?>"><?= esc($log['status']) ?></span></td>
                            <td class="text-muted small"><?= esc($log['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
