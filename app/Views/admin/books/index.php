<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$adminPath = env('dealsach.adminPath', 'ds-admin');
$makePageUrl = static fn (int $p): string => site_url($adminPath . '/books') . '?' . http_build_query(array_filter(['q' => $q, 'page' => $p], static fn ($v) => $v !== '' && $v !== null));
?>
<div class="d-flex justify-content-between align-items-start gap-3 mb-4">
    <div>
        <p class="text-uppercase fw-bold text-success small mb-1">CRUD sách</p>
        <h1 class="h3 fw-bold mb-0">Quản lý sách</h1>
        <p class="text-muted mb-0">Hiển thị <?= count($books) ?> trên <?= (int) $total ?> sách.</p>
    </div>
    <a class="btn btn-admin" href="<?= site_url($adminPath . '/books/new') ?>"><i class="bi bi-plus-lg me-1"></i>Thêm sách</a>
</div>

<div class="admin-card p-3 mb-3">
    <form class="row g-2 align-items-center" method="get" action="<?= site_url($adminPath . '/books') ?>">
        <div class="col-md">
            <input class="form-control" id="adminBookSearch" name="q" value="<?= esc($q) ?>" placeholder="Tìm theo tên sách hoặc ISBN">
        </div>
        <div class="col-md-auto">
            <button class="btn btn-admin" type="submit"><i class="bi bi-search me-1"></i>Tìm</button>
        </div>
    </form>
    <div id="liveSearchResults" class="small mt-2"></div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Sách</th><th>ISBN</th><th>NXB</th><th>Offer</th><th>Giá thấp nhất</th><th class="text-end">Thao tác</th></tr></thead>
            <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><strong><?= esc($book['title']) ?></strong><div class="text-muted small"><?= esc($book['slug']) ?></div></td>
                    <td><?= esc($book['isbn'] ?: '-') ?></td>
                    <td><?= esc($book['publisher_name'] ?: '-') ?></td>
                    <td><?= (int) $book['offer_count'] ?></td>
                    <td><?= $book['lowest_price'] ? format_vnd((int) $book['lowest_price']) : '-' ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= site_url($adminPath . '/books/' . $book['id']) ?>" title="Xem"><i class="bi bi-eye"></i></a>
                        <a class="btn btn-sm btn-outline-success" href="<?= site_url($adminPath . '/books/' . $book['id'] . '/edit') ?>" title="Sửa"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="post" action="<?= site_url($adminPath . '/books/' . $book['id'] . '/delete') ?>" onsubmit="return confirm('Xóa sách này?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Xóa"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-3"><ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= esc($makePageUrl($i)) ?>"><?= $i ?></a></li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
  const input = document.getElementById('adminBookSearch');
  const box = document.getElementById('liveSearchResults');
  if (!input || !box) return;
  let timer;
  input.addEventListener('input', () => {
    clearTimeout(timer);
    const q = input.value.trim();
    if (q.length < 2) { box.innerHTML = ''; return; }
    timer = setTimeout(async () => {
      const res = await fetch('<?= site_url($adminPath . '/ajax/books/search') ?>?q=' + encodeURIComponent(q));
      const rows = await res.json();
      box.innerHTML = rows.map(row => `<a class="badge text-bg-light border text-decoration-none me-1 mb-1" href="<?= site_url($adminPath . '/books') ?>/${row.id}">${row.title}</a>`).join('');
    }, 220);
  });
})();
</script>
<?= $this->endSection() ?>
