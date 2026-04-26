<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<style>
    .ds-detail-cover {
        max-height: 400px;
        object-fit: contain;
        border-radius: .5rem;
        background: #f5f5f5;
    }
    .ds-price-table th { font-size: .82rem; font-weight: 600; }
    .ds-price-table td { vertical-align: middle; }
    .ds-price-best { color: #e53935; font-weight: 700; font-size: 1.05rem; }
    .ds-stock-in { color: #2e7d32; }
    .ds-stock-out { color: #999; }
    .ds-tracking-box {
        background: linear-gradient(135deg, #e3f2fd, #ede7f6);
        border-radius: .75rem;
        padding: 1.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ════════════ Breadcrumb ════════════ -->
<div class="container mt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="font-size:.85rem;">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('sach') ?>">Danh mục sách</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết sách</li>
        </ol>
    </nav>
</div>

<div class="container py-4">
    <div class="row g-4">

        <!-- ── Cover Image ── -->
        <div class="col-md-4 text-center">
            <div class="bg-light rounded-3 p-4 d-flex align-items-center justify-content-center" style="min-height:320px;">
                <i class="bi bi-image text-muted" style="font-size:4rem;"></i>
            </div>
        </div>

        <!-- ── Book Info ── -->
        <div class="col-md-8">
            <h1 class="h3 fw-bold mb-1">Tên sách mẫu</h1>
            <p class="text-muted mb-2">Tác giả: <span class="fw-medium">Tác giả mẫu</span></p>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary bg-opacity-10 text-primary">ISBN: —</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary">Ngôn ngữ: Tiếng Việt</span>
                <span class="badge bg-info bg-opacity-10 text-info">Định dạng: Bìa mềm</span>
            </div>

            <p class="text-muted" style="font-size:.9rem;">
                Mô tả sách sẽ hiển thị ở đây. Đây là trang placeholder cho chi tiết sách — dữ liệu thực sẽ được tải từ database sau khi module catalog hoàn thiện.
            </p>

            <!-- ── Price Comparison Table ── -->
            <h5 class="fw-semibold mt-4 mb-3"><i class="bi bi-bar-chart me-1"></i>So sánh giá từ các nhà bán lẻ</h5>
            <div class="table-responsive">
                <table class="table table-hover ds-price-table align-middle" id="priceComparisonTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nhà bán lẻ</th>
                            <th>Giá niêm yết</th>
                            <th>Giá bán</th>
                            <th>Tình trạng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-medium">Fahasa</span></td>
                            <td class="text-decoration-line-through text-muted">—</td>
                            <td class="ds-price-best">—</td>
                            <td><span class="ds-stock-in"><i class="bi bi-check-circle-fill me-1"></i>Còn hàng</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Mua
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-medium">Tiki</span></td>
                            <td class="text-decoration-line-through text-muted">—</td>
                            <td>—</td>
                            <td><span class="ds-stock-out"><i class="bi bi-x-circle me-1"></i>Hết hàng</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-secondary disabled">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Mua
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-medium">Shopee</span></td>
                            <td class="text-muted">—</td>
                            <td>—</td>
                            <td><span class="text-muted">Chưa có dữ liệu</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><span class="fw-medium">Phương Nam</span></td>
                            <td class="text-muted">—</td>
                            <td>—</td>
                            <td><span class="text-muted">Chưa có dữ liệu</span></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-muted mt-1" style="font-size:.78rem;">
                <i class="bi bi-clock me-1"></i>Cập nhật lần cuối: —
            </p>
        </div>
    </div>

    <!-- ════════════ Tracking Box ════════════ -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="ds-tracking-box" id="trackingSection">
                <h5 class="fw-semibold mb-2"><i class="bi bi-bell me-2"></i>Theo dõi giảm giá</h5>
                <p class="text-muted mb-3" style="font-size:.9rem;">
                    Nhập email để nhận thông báo khi sách này giảm giá. Chúng tôi sẽ gửi mã OTP để xác minh.
                </p>
                <form class="row g-2 align-items-end" id="trackingForm">
                    <div class="col-sm">
                        <label for="trackingEmail" class="form-label fw-medium" style="font-size:.85rem;">Email</label>
                        <input type="email" class="form-control" id="trackingEmail" name="email"
                               placeholder="example@email.com" required>
                    </div>
                    <div class="col-sm-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Gửi mã OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
