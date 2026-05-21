<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $adminPath = env('dealsach.adminPath', 'ds-admin'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Quản lý sách</h1>
        <p class="text-muted mb-0">CRUD đầy đủ cho bảng sách trong phạm vi đồ án.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url($adminPath . '/books/new') ?>">
        <i class="bi bi-plus-circle me-1"></i>Thêm sách
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form class="row g-2 mb-3" action="<?= site_url($adminPath . '/books') ?>" method="get">
            <div class="col-md">
                <input class="form-control" type="search" name="q" value="<?= esc($q) ?>" placeholder="Tìm theo tên sách hoặc ISBN">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search me-1"></i>Tìm</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sách</th>
                    <th>ISBN</th>
                    <th>NXB</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= (int) $book['id'] ?></td>
                        <td class="fw-semibold"><?= esc($book['title']) ?></td>
                        <td><?= esc($book['isbn'] ?? '') ?></td>
                        <td><?= esc($book['publisher_name'] ?? '') ?></td>
                        <td><span class="badge <?= (bool) $book['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= (bool) $book['is_active'] ? 'Đang hiển thị' : 'Ẩn' ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url($adminPath . '/books/' . $book['id'] . '/edit') ?>"><i class="bi bi-pencil"></i></a>
                            <form class="d-inline" action="<?= site_url($adminPath . '/books/' . $book['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Xóa sách này?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($books === []): ?>
                    <tr><td class="text-center text-muted py-4" colspan="6">Không có sách phù hợp.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
