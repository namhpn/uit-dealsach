<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<style>
    .ds-detail-head {
        background: var(--ds-paper);
        border-bottom: 1px solid var(--ds-line);
    }

    .ds-detail-cover {
        width: 100%;
        max-height: 520px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--ds-line);
        background: var(--ds-paper);
    }

    .ds-meta-badge {
        border: 1px solid var(--ds-line);
        background: #fff;
        color: var(--ds-muted);
    }

    .ds-offer-best {
        outline: 2px solid rgba(17, 97, 73, .22);
        background: rgba(17, 97, 73, .04);
    }

    .ds-tracking-box {
        background: #eef4ef;
        border: 1px solid #cbded2;
        border-radius: 8px;
    }

    .ds-otp-panel[hidden] {
        display: none !important;
    }

    .ds-otp-code {
        letter-spacing: .22rem;
        font-weight: 700;
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="ds-detail-head py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('sach') ?>">Danh mục sách</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($book['title']) ?></li>
            </ol>
        </nav>
    </div>
</header>

<div class="container py-4">
    <div class="row g-4 align-items-start">
        <div class="col-md-4 col-xl-3">
            <?php if (! empty($book['cover_image_url'])): ?>
                <img class="ds-detail-cover" src="<?= esc($book['cover_image_url']) ?>" alt="<?= esc($book['title']) ?>">
            <?php else: ?>
                <div class="ds-detail-cover ds-empty-cover d-flex align-items-center justify-content-center" style="aspect-ratio:3/4;">
                    <i class="bi bi-book fs-1"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8 col-xl-9">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h1 class="fw-bold mb-2"><?= esc($book['title']) ?></h1>
                    <p class="text-muted mb-3">Tác giả: <span class="fw-semibold"><?= esc($book['authors']) ?></span></p>
                </div>
                <div class="text-lg-end">
                    <div class="small text-muted">Giá thấp nhất còn hàng</div>
                    <?php if ($book['lowest_price'] !== null): ?>
                        <div class="ds-price fs-3"><?= format_vnd($book['lowest_price']) ?></div>
                    <?php else: ?>
                        <div class="fw-bold text-muted fs-5">Chưa có giá</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge ds-meta-badge px-3 py-2">ISBN: <?= esc($book['isbn'] ?: 'Chưa có') ?></span>
                <span class="badge ds-meta-badge px-3 py-2">NXB: <?= esc($book['publisher_name']) ?></span>
                <span class="badge ds-meta-badge px-3 py-2">Ngôn ngữ: <?= esc($book['language'] ?: 'Tiếng Việt') ?></span>
                <span class="badge ds-meta-badge px-3 py-2">Định dạng: <?= esc($book['format'] ?: 'Chưa rõ') ?></span>
            </div>

            <?php if (! empty($book['categories'])): ?>
                <p class="small text-muted mb-3">Danh mục: <?= esc($book['categories']) ?></p>
            <?php endif; ?>

            <p class="text-muted">
                <?= esc($book['description'] ?: 'Sách đang được cập nhật mô tả. Bạn vẫn có thể xem các mức giá hiện có từ nhà bán trong bảng so sánh bên dưới.') ?>
            </p>
        </div>
    </div>

    <section class="mt-5">
        <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
            <div>
                <p class="text-uppercase fw-bold mb-1" style="color: var(--ds-green); font-size:.78rem;">So sánh giá</p>
                <h2 class="h4 fw-bold mb-0">Các nhà bán đang có dữ liệu</h2>
            </div>
            <p class="text-muted small mb-0">
                <i class="bi bi-clock me-1"></i>Cập nhật: <?= esc($book['last_crawled_at'] ?: 'Chưa có') ?>
            </p>
        </div>

        <?php if ($book['offers'] === []): ?>
            <div class="ds-card p-5 text-center">
                <i class="bi bi-tags fs-1 text-muted"></i>
                <p class="text-muted mt-3 mb-0">Chưa có offer nào cho sách này.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive ds-card">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Nhà bán</th>
                        <th>Giá niêm yết</th>
                        <th>Giá bán</th>
                        <th>Tình trạng</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($book['offers'] as $offer): ?>
                        <?php
                        $isBest = (bool) $offer['in_stock']
                            && $book['lowest_price'] !== null
                            && (int) $offer['current_effective_price'] === (int) $book['lowest_price'];
                        ?>
                        <tr class="<?= $isBest ? 'ds-offer-best' : '' ?>">
                            <td>
                                <div class="fw-bold"><?= esc($offer['retailer_name']) ?></div>
                                <?php if ($isBest): ?>
                                    <span class="badge text-bg-success">Giá tốt nhất</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($offer['current_listed_price'] !== null): ?>
                                    <span class="<?= $offer['current_discounted_price'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                        <?= format_vnd($offer['current_listed_price']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($offer['current_effective_price'] !== null): ?>
                                    <span class="<?= $isBest ? 'ds-price' : 'fw-bold' ?>"><?= format_vnd($offer['current_effective_price']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((bool) $offer['in_stock']): ?>
                                    <span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Còn hàng</span>
                                <?php else: ?>
                                    <span class="text-muted"><i class="bi bi-x-circle me-1"></i>Hết hàng</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ((bool) $offer['in_stock'] && ! empty($offer['url'])): ?>
                                    <a class="btn btn-sm ds-search-btn" href="<?= site_url('go/' . $offer['id']) ?>" rel="sponsored">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Đến nhà bán
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" disabled>Không khả dụng</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="ds-tracking-box p-4"
                 id="trackingBox"
                 data-book-id="<?= (int) $book['id'] ?>"
                 data-request-url="<?= site_url('theo-doi/gui-otp') ?>"
                 data-verify-url="<?= site_url('theo-doi/xac-thuc-otp') ?>"
                 data-create-url="<?= site_url('theo-doi/tao') ?>">
                <h2 class="h5 fw-bold mb-2"><i class="bi bi-bell me-2"></i>Theo dõi giảm giá</h2>
                <p class="text-muted mb-3">Nhập email và mức giá mong muốn, DealSach sẽ gửi mã OTP để xác thực trước khi tạo theo dõi.</p>

                <div class="alert mb-3 d-none" id="trackingMessage" role="alert"></div>

                <form class="row g-2" id="otpRequestForm" method="post">
                    <?= csrf_field() ?>
                    <div class="col-md">
                        <label class="form-label small fw-semibold" for="trackingEmail">Email</label>
                        <input class="form-control" id="trackingEmail" type="email" name="email" placeholder="ban@example.com" required>
                    </div>
                    <div class="col-md">
                        <label class="form-label small fw-semibold" for="targetPrice">Giá mục tiêu</label>
                        <input class="form-control" id="targetPrice" type="number" min="1000" step="1000" name="target_price"
                               value="<?= esc($book['lowest_price'] ?? '') ?>" placeholder="Ví dụ: 89000" required>
                    </div>
                    <div class="col-md-auto d-flex align-items-end">
                        <button class="btn ds-search-btn w-100" type="submit" data-default-label="Gửi mã OTP">
                            <i class="bi bi-send me-1"></i><span>Gửi mã OTP</span>
                        </button>
                    </div>
                </form>

                <form class="row g-2 mt-3 ds-otp-panel" id="otpVerifyForm" method="post" hidden>
                    <div class="col-md">
                        <label class="form-label small fw-semibold" for="otpCode">Mã OTP</label>
                        <input class="form-control ds-otp-code" id="otpCode" type="text" name="otp"
                               inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code"
                               placeholder="000000" required>
                        <div class="form-text" id="devOtpHint"></div>
                    </div>
                    <div class="col-md-auto d-flex align-items-end">
                        <button class="btn btn-outline-success w-100" type="submit" data-default-label="Xác thực">
                            <i class="bi bi-shield-check me-1"></i><span>Xác thực</span>
                        </button>
                    </div>
                </form>

                <form class="mt-3 ds-otp-panel" id="trackingCreateForm" method="post" hidden>
                    <button class="btn ds-search-btn" type="submit" data-default-label="Tạo theo dõi giá">
                        <i class="bi bi-bell-fill me-1"></i><span>Tạo theo dõi giá</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
    const box = document.getElementById('trackingBox');
    if (!box) {
        return;
    }

    const bookId = box.dataset.bookId;
    const urls = {
        request: box.dataset.requestUrl,
        verify: box.dataset.verifyUrl,
        create: box.dataset.createUrl,
    };
    const requestForm = document.getElementById('otpRequestForm');
    const verifyForm = document.getElementById('otpVerifyForm');
    const createForm = document.getElementById('trackingCreateForm');
    const messageBox = document.getElementById('trackingMessage');
    const emailInput = document.getElementById('trackingEmail');
    const priceInput = document.getElementById('targetPrice');
    const otpInput = document.getElementById('otpCode');
    const devOtpHint = document.getElementById('devOtpHint');
    const csrfInput = requestForm.querySelector('input[type="hidden"]');

    const setMessage = (type, text) => {
        messageBox.className = `alert alert-${type} mb-3`;
        messageBox.textContent = text;
    };

    const setLoading = (button, isLoading) => {
        const label = button.querySelector('span');
        button.disabled = isLoading;
        label.textContent = isLoading ? 'Đang xử lý...' : button.dataset.defaultLabel;
    };

    const postJson = async (url, data) => {
        const body = new FormData();
        body.append(csrfInput.name, csrfInput.value);
        body.append('book_id', bookId);
        Object.entries(data).forEach(([key, value]) => body.append(key, value));

        const response = await fetch(url, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body,
        });
        const json = await response.json();
        if (json.csrf) {
            csrfInput.value = json.csrf;
        }
        return json;
    };

    requestForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = requestForm.querySelector('button[type="submit"]');
        setLoading(button, true);
        try {
            const json = await postJson(urls.request, {
                email: emailInput.value,
                target_price: priceInput.value,
            });
            setMessage(json.success ? 'success' : 'danger', json.message);
            if (json.success) {
                emailInput.readOnly = true;
                verifyForm.hidden = false;
                otpInput.focus();
                devOtpHint.textContent = json.dev_otp ? `Mã OTP dev: ${json.dev_otp}` : '';
            }
        } catch (error) {
            setMessage('danger', 'Không thể gửi OTP. Vui lòng thử lại sau.');
        } finally {
            setLoading(button, false);
        }
    });

    verifyForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = verifyForm.querySelector('button[type="submit"]');
        setLoading(button, true);
        try {
            const json = await postJson(urls.verify, {
                email: emailInput.value,
                otp: otpInput.value,
            });
            setMessage(json.success ? 'success' : 'danger', json.message);
            if (json.success) {
                otpInput.readOnly = true;
                createForm.hidden = false;
            }
        } catch (error) {
            setMessage('danger', 'Không thể xác thực OTP. Vui lòng thử lại.');
        } finally {
            setLoading(button, false);
        }
    });

    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = createForm.querySelector('button[type="submit"]');
        setLoading(button, true);
        try {
            const json = await postJson(urls.create, {
                email: emailInput.value,
                target_price: priceInput.value,
            });
            setMessage(json.success ? 'success' : 'danger', json.message);
            if (json.success) {
                requestForm.hidden = true;
                verifyForm.hidden = true;
                createForm.hidden = true;
            }
        } catch (error) {
            setMessage('danger', 'Không thể tạo theo dõi giá. Vui lòng thử lại.');
        } finally {
            setLoading(button, false);
        }
    });
})();
</script>
<?= $this->endSection() ?>
