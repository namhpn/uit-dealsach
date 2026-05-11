<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Frontend smoke checks against the running Docker/nginx app.
 *
 * Run after:
 * docker compose up -d
 * docker exec dealsach-app php spark migrate:refresh
 * docker exec dealsach-app php spark db:seed DemoSeeder
 *
 * @internal
 */
final class FrontendSmokeTest extends CIUnitTestCase
{
    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = rtrim((string) (getenv('FRONTEND_TEST_BASE_URL') ?: 'http://nginx'), '/');
    }

    public function testHomepageRendersPublicCatalogEntryPoints(): void
    {
        $response = $this->httpGet('/');

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('DealSach tìm giá tốt nhất', $response['body']);
        $this->assertStringContainsString('Sách có giá tốt', $response['body']);
        $this->assertStringContainsString('Danh mục sách', $response['body']);
        $this->assertStringContainsString('Fahasa', $response['body']);
        $this->assertNoFrontendFailure($response['body']);
    }

    public function testCatalogRendersSearchFiltersAndPagination(): void
    {
        $response = $this->httpGet('/sach?q=%C4%90%E1%BA%AFc&retailer%5B%5D=fahasa&category=van-hoc&stock=in_stock');

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Danh mục sách', $response['body']);
        $this->assertStringContainsString('Bộ lọc', $response['body']);
        $this->assertStringContainsString('Nhà bán', $response['body']);
        $this->assertStringContainsString('Tình trạng', $response['body']);
        $this->assertStringContainsString('Kết quả', $response['body']);
        $this->assertNoFrontendFailure($response['body']);
    }

    public function testBookDetailRendersComparisonAndOtpTrackingUi(): void
    {
        $response = $this->httpGet('/sach/dac-nhan-tam');

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Đắc Nhân Tâm', $response['body']);
        $this->assertStringContainsString('So sánh giá', $response['body']);
        $this->assertStringContainsString('Giá tốt nhất', $response['body']);
        $this->assertStringContainsString('Theo dõi giảm giá', $response['body']);
        $this->assertStringContainsString('Gửi mã OTP', $response['body']);
        $this->assertStringContainsString('Đến nhà bán', $response['body']);
        $this->assertNoFrontendFailure($response['body']);
    }

    public function testAdminLoginRenders(): void
    {
        $response = $this->httpGet('/ds-admin/login');

        $this->assertSame(200, $response['status']);
        $this->assertStringContainsString('Quản trị DealSach', $response['body']);
        $this->assertStringContainsString('Đăng nhập', $response['body']);
        $this->assertStringContainsString('Tên đăng nhập', $response['body']);
        $this->assertStringContainsString('Mật khẩu', $response['body']);
        $this->assertNoFrontendFailure($response['body']);
    }

    public function testAdminBookScreensAndCsvRenderAfterLogin(): void
    {
        $cookieJar = tempnam(sys_get_temp_dir(), 'dealsach_frontend_');
        $login = $this->httpGet('/ds-admin/login', $cookieJar);
        $csrf = $this->extractCsrf($login['body']);

        $loggedIn = $this->httpPost('/ds-admin/login', [
            $csrf['name'] => $csrf['value'],
            'username' => 'admin',
            'password' => '123456',
        ], $cookieJar);
        $this->assertContains($loggedIn['status'], [302, 303]);

        $books = $this->httpGet('/ds-admin/books', $cookieJar);
        $this->assertSame(200, $books['status']);
        $this->assertStringContainsString('Quản lý sách', $books['body']);
        $this->assertStringContainsString('Thêm sách', $books['body']);
        $this->assertStringContainsString('Đắc Nhân Tâm', $books['body']);
        $this->assertNoFrontendFailure($books['body']);

        $form = $this->httpGet('/ds-admin/books/new', $cookieJar);
        $this->assertSame(200, $form['status']);
        $this->assertStringContainsString('Thêm sách', $form['body']);
        $this->assertStringContainsString('Nhà xuất bản', $form['body']);
        $this->assertStringContainsString('Hiển thị trên trang công khai', $form['body']);
        $this->assertNoFrontendFailure($form['body']);

        $csv = $this->httpGet('/ds-admin/exports/books.csv', $cookieJar);
        $this->assertSame(200, $csv['status']);
        $this->assertStringContainsString('id,title,slug,isbn,publisher,is_active,updated_at', $csv['body']);
        $this->assertStringContainsString('Đắc Nhân Tâm', $csv['body']);

        @unlink($cookieJar);
    }

    public function testOtpFrontendFlowReturnsVisibleDevOtpAndCreatesRule(): void
    {
        $cookieJar = tempnam(sys_get_temp_dir(), 'dealsach_otp_');
        $detail = $this->httpGet('/sach/dac-nhan-tam', $cookieJar);
        $csrf = $this->extractCsrf($detail['body']);
        $email = 'frontend-smoke-' . bin2hex(random_bytes(4)) . '@example.com';

        $request = $this->httpPost('/theo-doi/gui-otp', [
            $csrf['name'] => $csrf['value'],
            'book_id' => '1',
            'email' => $email,
        ], $cookieJar);
        $requestJson = json_decode($request['body'], true);

        $this->assertSame(200, $request['status']);
        $this->assertTrue((bool) $requestJson['success']);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $requestJson['dev_otp']);

        $verify = $this->httpPost('/theo-doi/xac-thuc-otp', [
            $csrf['name'] => $requestJson['csrf'],
            'book_id' => '1',
            'email' => $email,
            'otp' => $requestJson['dev_otp'],
        ], $cookieJar);
        $verifyJson = json_decode($verify['body'], true);
        $this->assertTrue((bool) $verifyJson['success']);

        $create = $this->httpPost('/theo-doi/tao', [
            $csrf['name'] => $verifyJson['csrf'],
            'book_id' => '1',
            'email' => $email,
            'target_price' => '99000',
        ], $cookieJar);
        $createJson = json_decode($create['body'], true);

        $this->assertSame(200, $create['status']);
        $this->assertTrue((bool) $createJson['success']);
        $this->assertGreaterThan(0, (int) $createJson['rule_id']);
        $this->assertSame(64, strlen((string) $createJson['disable_token']));

        @unlink($cookieJar);
    }

    /**
     * @return array{status: int, body: string}
     */
    private function httpGet(string $path, ?string $cookieJar = null): array
    {
        return $this->http('GET', $path, [], $cookieJar);
    }

    /**
     * @param array<string, string> $data
     *
     * @return array{status: int, body: string}
     */
    private function httpPost(string $path, array $data, string $cookieJar): array
    {
        return $this->http('POST', $path, $data, $cookieJar);
    }

    /**
     * @param array<string, string> $data
     *
     * @return array{status: int, body: string}
     */
    private function http(string $method, string $path, array $data = [], ?string $cookieJar = null): array
    {
        $handle = curl_init($this->baseUrl . $path);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($cookieJar !== null) {
            $options[CURLOPT_COOKIEJAR] = $cookieJar;
            $options[CURLOPT_COOKIEFILE] = $cookieJar;
        }

        if ($method === 'POST') {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/x-www-form-urlencoded'];
        }

        curl_setopt_array($handle, $options);
        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        $this->assertSame('', $error, 'HTTP request failed for ' . $path . ': ' . $error);

        return ['status' => $status, 'body' => (string) $body];
    }

    /**
     * @return array{name: string, value: string}
     */
    private function extractCsrf(string $body): array
    {
        $matched = preg_match('/name="(csrf_[^"]+|csrf_token_name)" value="([^"]+)"/', $body, $matches);
        $this->assertSame(1, $matched, 'CSRF field not found in rendered form.');

        return ['name' => $matches[1], 'value' => $matches[2]];
    }

    private function assertNoFrontendFailure(string $body): void
    {
        $this->assertStringNotContainsString('CodeIgniter\Exceptions', $body);
        $this->assertStringNotContainsString('ErrorException', $body);
        $this->assertStringNotContainsString('Whoops', $body);
        $this->assertStringNotContainsString('Ã', $body, 'Rendered HTML contains mojibake marker Ã.');
        $this->assertStringNotContainsString('áº', $body, 'Rendered HTML contains mojibake marker áº.');
        $this->assertStringNotContainsString('Ä', $body, 'Rendered HTML contains mojibake marker Ä.');
    }
}
