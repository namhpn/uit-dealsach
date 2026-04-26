<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    .ds-metric-card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        transition: transform .2s, box-shadow .2s;
    }
    .ds-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,.1);
    }
    .ds-metric-icon {
        width: 48px;
        height: 48px;
        border-radius: .6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .ds-metric-value { font-weight: 700; font-size: 1.5rem; line-height: 1.2; }
    .ds-metric-label { font-size: .78rem; color: #6c757d; }

    .ds-activity-item {
        padding: .6rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: .85rem;
    }
    .ds-activity-item:last-child { border-bottom: none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ════════════ Welcome Banner ════════════ -->
<div class="alert alert-primary border-0 rounded-3 d-flex align-items-center gap-3 mb-4" role="alert"
     style="background: linear-gradient(135deg, #e0e7ff, #ede9fe);">
    <i class="bi bi-speedometer2 fs-3 text-primary"></i>
    <div>
        <strong>Xin chào, <?= esc(session()->get('admin_display_name') ?? 'Admin') ?>!</strong><br>
        <span class="text-muted" style="font-size:.85rem;">
            Bảng điều khiển DealSach — tổng quan hệ thống và dữ liệu mới nhất.
        </span>
    </div>
</div>

<!-- ════════════ Metric Cards ════════════ -->
<div class="row g-3 mb-4" id="dashboardMetrics">
    <!-- Total books -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="ds-metric-value">0</div>
                    <div class="ds-metric-label">Tổng đầu sách</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Retailers -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <div class="ds-metric-value">4</div>
                    <div class="ds-metric-label">Nhà bán lẻ</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tracking rules -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-bell"></i>
                </div>
                <div>
                    <div class="ds-metric-value">0</div>
                    <div class="ds-metric-label">Theo dõi giá</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Outbound clicks -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-cursor"></i>
                </div>
                <div>
                    <div class="ds-metric-value">0</div>
                    <div class="ds-metric-label">Lượt chuyển hướng</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ════════════ Second Row Metrics ════════════ -->
<div class="row g-3 mb-4">
    <!-- Latest crawl -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-cloud-download"></i>
                </div>
                <div>
                    <div class="ds-metric-value" style="font-size:1rem;">—</div>
                    <div class="ds-metric-label">Crawl gần nhất</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Alerts today -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-envelope-check"></i>
                </div>
                <div>
                    <div class="ds-metric-value">0</div>
                    <div class="ds-metric-label">Cảnh báo hôm nay</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Failed jobs -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon" style="background:rgba(220,53,69,.1);color:#dc3545;">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="ds-metric-value">0</div>
                    <div class="ds-metric-label">Job lỗi (24h)</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent sign-ins -->
    <div class="col-6 col-lg-3">
        <div class="card ds-metric-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="ds-metric-icon" style="background:rgba(13,110,253,.1);color:#0d6efd;">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <div class="ds-metric-value">0</div>
                    <div class="ds-metric-label">Đăng nhập gần đây</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ════════════ Tables Row ════════════ -->
<div class="row g-4">
    <!-- Recent Crawl Results -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-cloud-download me-1"></i>Kết quả crawl gần đây</h6>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" id="recentCrawlTable">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:.8rem;">Nhà bán lẻ</th>
                                <th style="font-size:.8rem;">Thời gian</th>
                                <th style="font-size:.8rem;">Trạng thái</th>
                                <th style="font-size:.8rem;">Sản phẩm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3" style="font-size:.85rem;">
                                    <i class="bi bi-inbox me-1"></i>Chưa có dữ liệu crawl
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-1"></i>Hoạt động gần đây</h6>
            </div>
            <div class="card-body pt-0">
                <div class="ds-activity-item d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle text-muted mt-1"></i>
                    <div>
                        <span>Hệ thống đã sẵn sàng.</span><br>
                        <small class="text-muted">Hãy chạy crawl và thêm sách để bắt đầu.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ════════════ Quick Actions ════════════ -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-lightning me-1"></i>Thao tác nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php $adminPath = env('dealsach.adminPath', 'ds-admin'); ?>
                    <a href="<?= site_url($adminPath . '/books/new') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Thêm sách mới
                    </a>
                    <a href="<?= site_url($adminPath . '/books') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-journal-bookmark-fill me-1"></i>Danh sách sách
                    </a>
                    <a href="<?= site_url($adminPath . '/exports/books.csv') ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-filetype-csv me-1"></i>Xuất CSV sách
                    </a>
                    <a href="<?= site_url($adminPath . '/exports/activity.csv') ?>" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-activity me-1"></i>Xuất hoạt động
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
