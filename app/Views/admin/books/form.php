<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $adminPath = env('dealsach.adminPath', 'ds-admin'); ?>
<?php $isEdit = $book !== null; ?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 fw-bold mb-0"><?= $isEdit ? 'Sửa sách' : 'Thêm sách' ?></h1>
    <a class="btn btn-outline-secondary" href="<?= site_url($adminPath . '/books') ?>"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= esc($action) ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="title">Tên sách</label>
                    <input class="form-control" id="title" name="title" value="<?= esc(old('title', $book['title'] ?? '')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="isbn">ISBN</label>
                    <input class="form-control" id="isbn" name="isbn" value="<?= esc(old('isbn', $book['isbn'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="publisher_id">Nhà xuất bản</label>
                    <select class="form-select" id="publisher_id" name="publisher_id" required>
                        <?php foreach ($publishers as $publisher): ?>
                            <option value="<?= (int) $publisher['id'] ?>" <?= (int) old('publisher_id', $book['publisher_id'] ?? 0) === (int) $publisher['id'] ? 'selected' : '' ?>>
                                <?= esc($publisher['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="format">Định dạng</label>
                    <input class="form-control" id="format" name="format" value="<?= esc(old('format', $book['format'] ?? 'Bìa mềm')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="language">Ngôn ngữ</label>
                    <input class="form-control" id="language" name="language" value="<?= esc(old('language', $book['language'] ?? 'Tiếng Việt')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="cover_image_url">URL ảnh bìa</label>
                    <input class="form-control" id="cover_image_url" name="cover_image_url" value="<?= esc(old('cover_image_url', $book['cover_image_url'] ?? '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= esc(old('description', $book['description'] ?? '')) ?></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= (bool) old('is_active', $book['is_active'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Hiển thị trên trang công khai</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Lưu sách</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
