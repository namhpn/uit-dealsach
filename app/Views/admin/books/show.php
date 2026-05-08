<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $adminPath = env('dealsach.adminPath', 'ds-admin'); ?>
<div class="d-flex justify-content-between align-items-start gap-3 mb-4">
    <div>
        <p class="text-uppercase fw-bold text-success small mb-1">Chi tiết sách</p>
        <h1 class="h3 fw-bold mb-1"><?= esc($book['title']) ?></h1>
        <p class="text-muted mb-0"><?= esc($book['publisher_name'] ?: 'Chưa rõ NXB') ?> · <?= esc($book['isbn'] ?: 'Chưa có ISBN') ?></p>
    </div>
    <a class="btn btn-admin" href="<?= site_url($adminPath . '/books/' . $book['id'] . '/edit') ?>"><i class="bi bi-pencil me-1"></i>Sửa</a>
</div>

<div class="row g-4">
    <div class="col-xl-5">
        <div class="admin-card p-3">
            <h2 class="h5 fw-bold mb-3">Thông tin</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Slug</dt><dd class="col-sm-8"><?= esc($book['slug']) ?></dd>
                <dt class="col-sm-4">Định dạng</dt><dd class="col-sm-8"><?= esc($book['format'] ?: '-') ?></dd>
                <dt class="col-sm-4">Ngôn ngữ</dt><dd class="col-sm-8"><?= esc($book['language']) ?></dd>
                <dt class="col-sm-4">Trạng thái</dt><dd class="col-sm-8"><?= (int) $book['is_active'] === 1 ? 'Đang hiển thị' : 'Ẩn' ?></dd>
            </dl>
            <p class="text-muted mt-3 mb-0"><?= esc($book['description'] ?: 'Chưa có mô tả.') ?></p>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="admin-card p-3">
            <h2 class="h5 fw-bold mb-3">Retailer offers</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nhà bán</th><th>Giá</th><th>Tồn kho</th><th>Cập nhật</th></tr></thead>
                    <tbody>
                    <?php foreach ($offers as $offer): ?>
                        <tr>
                            <td><?= esc($offer['retailer_name']) ?></td>
                            <td><?= $offer['current_effective_price'] ? format_vnd((int) $offer['current_effective_price']) : '-' ?></td>
                            <td><?= (int) $offer['in_stock'] === 1 ? 'Còn hàng' : 'Hết hàng' ?></td>
                            <td class="text-muted small"><?= esc($offer['last_crawled_at'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="admin-card p-3">
            <h2 class="h5 fw-bold mb-3">Snapshots gần đây</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>ID</th><th>Nhà bán</th><th>Giá niêm yết</th><th>Giá bán</th><th>Tồn kho</th><th>Thời gian</th></tr></thead>
                    <tbody>
                    <?php foreach ($snapshots as $snapshot): ?>
                        <tr>
                            <td>#<?= (int) $snapshot['id'] ?></td>
                            <td><?= esc($snapshot['retailer_name']) ?></td>
                            <td><?= $snapshot['listed_price'] ? format_vnd((int) $snapshot['listed_price']) : '-' ?></td>
                            <td><?= $snapshot['effective_price'] ? format_vnd((int) $snapshot['effective_price']) : '-' ?></td>
                            <td><?= (int) $snapshot['in_stock'] === 1 ? 'Còn hàng' : 'Hết hàng' ?></td>
                            <td class="text-muted small"><?= esc($snapshot['captured_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
