<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- ════════════ Breadcrumb ════════════ -->
<div class="container mt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="font-size:.85rem;">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh mục sách</li>
        </ol>
    </nav>
</div>

<div class="container py-4">
    <div class="row g-4">

        <!-- ── Sidebar Filters ── -->
        <aside class="col-lg-3" id="catalogFilters">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-funnel me-1"></i>Bộ lọc</h5>

                    <form action="<?= site_url('sach') ?>" method="get" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="filterKeyword" class="form-label fw-medium" style="font-size:.85rem;">Từ khóa</label>
                            <input type="search" class="form-control form-control-sm" id="filterKeyword"
                                   name="q" placeholder="Tên sách, tác giả, ISBN…">
                        </div>

                        <!-- Retailer filter -->
                        <div class="mb-3">
                            <label class="form-label fw-medium" style="font-size:.85rem;">Nhà bán lẻ</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filterFahasa" name="retailer[]" value="fahasa">
                                <label class="form-check-label" for="filterFahasa">Fahasa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filterTiki" name="retailer[]" value="tiki">
                                <label class="form-check-label" for="filterTiki">Tiki</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filterShopee" name="retailer[]" value="shopee">
                                <label class="form-check-label" for="filterShopee">Shopee</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filterPhuongnam" name="retailer[]" value="phuongnam">
                                <label class="form-check-label" for="filterPhuongnam">Phương Nam</label>
                            </div>
                        </div>

                        <!-- Availability filter -->
                        <div class="mb-3">
                            <label class="form-label fw-medium" style="font-size:.85rem;">Tình trạng</label>
                            <select class="form-select form-select-sm" name="stock" id="filterStock">
                                <option value="">Tất cả</option>
                                <option value="in_stock">Còn hàng</option>
                                <option value="out_of_stock">Hết hàng</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>Áp dụng bộ lọc
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ── Book Grid ── -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 fw-bold mb-0">Danh mục sách</h1>
                <span class="text-muted" style="font-size:.85rem;">Hiển thị 0 kết quả</span>
            </div>

            <!-- Placeholder: empty state -->
            <div class="text-center py-5" id="catalogEmpty">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="mt-2 text-muted">Chưa có dữ liệu sách.<br>Hãy chạy lệnh crawl để nhập dữ liệu.</p>
                <code class="d-block mt-1" style="font-size:.8rem;">php spark dealsach:crawl all</code>
            </div>

            <!-- Placeholder: Pagination -->
            <nav aria-label="Phân trang danh mục" class="mt-4 d-none" id="catalogPagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Sau</a></li>
                </ul>
            </nav>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
