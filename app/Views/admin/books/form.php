<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $isEdit = $book !== null; ?>
<div class="mb-4">
    <p class="text-uppercase fw-bold text-success small mb-1">Thông tin canonical book</p>
    <h1 class="h3 fw-bold mb-0"><?= $isEdit ? 'Sửa sách' : 'Thêm sách' ?></h1>
</div>

<?php if (session('errors')): ?>
    <div class="alert alert-danger">
        <?php foreach (session('errors') as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="admin-card p-4" method="post" action="<?= esc($action) ?>">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label fw-semibold" for="title">Tên sách</label>
            <input class="form-control" id="title" name="title" value="<?= esc(old('title', $book['title'] ?? '')) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="slug">Slug</label>
            <input class="form-control" id="slug" name="slug" value="<?= esc(old('slug', $book['slug'] ?? '')) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="isbn">ISBN</label>
            <input class="form-control" id="isbn" name="isbn" value="<?= esc(old('isbn', $book['isbn'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold" for="publisher_id">Nhà xuất bản</label>
            <select class="form-select" id="publisher_id" name="publisher_id">
                <option value="">Chưa rõ</option>
                <?php foreach ($publishers as $publisher): ?>
                    <option value="<?= (int) $publisher['id'] ?>" <?= (string) old('publisher_id', $book['publisher_id'] ?? '') === (string) $publisher['id'] ? 'selected' : '' ?>>
                        <?= esc($publisher['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold" for="format">Định dạng</label>
            <input class="form-control" id="format" name="format" value="<?= esc(old('format', $book['format'] ?? 'Bìa mềm')) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold" for="language">Ngôn ngữ</label>
            <input class="form-control" id="language" name="language" value="<?= esc(old('language', $book['language'] ?? 'Tiếng Việt')) ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="cover_image_url">URL ảnh bìa</label>
            <input class="form-control" id="cover_image_url" name="cover_image_url" value="<?= esc(old('cover_image_url', $book['cover_image_url'] ?? '')) ?>">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="description">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="5"><?= esc(old('description', $book['description'] ?? '')) ?></textarea>
        </div>
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= (int) old('is_active', $book['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">Đang hiển thị</label>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-admin" type="submit"><i class="bi bi-save me-1"></i>Lưu</button>
        <a class="btn btn-outline-secondary" href="<?= site_url(env('dealsach.adminPath', 'ds-admin') . '/books') ?>">Hủy</a>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
  const title = document.getElementById('title');
  const slug = document.getElementById('slug');
  if (!title || !slug || slug.value) return;
  title.addEventListener('input', () => {
    slug.value = title.value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'd').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  });
})();
</script>
<?= $this->endSection() ?>
