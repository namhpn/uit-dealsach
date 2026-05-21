<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class TrackingService
{
    private BaseConnection $db;
    private OtpService $otpService;

    public function __construct(?BaseConnection $db = null, ?OtpService $otpService = null)
    {
        $this->db = $db ?? Database::connect();
        $this->otpService = $otpService ?? new OtpService($this->db);
    }

    /**
     * @return array{success: bool, message: string, rule_id?: int, disable_token?: string}
     */
    public function createRule(string $email, int $bookId, int $targetPrice): array
    {
        $email = $this->otpService->normalizeEmail($email);

        if (session()->get('tracking_verified_email') !== $email || (int) session()->get('tracking_verified_book_id') !== $bookId) {
            return ['success' => false, 'message' => 'Email chưa được xác thực OTP cho sách này.'];
        }

        if ($targetPrice <= 0) {
            return ['success' => false, 'message' => 'Giá mục tiêu phải lớn hơn 0.'];
        }

        $duplicate = $this->db->table('tracking_rules')
            ->where('book_id', $bookId)
            ->where('email', $email)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if ($duplicate !== null) {
            return ['success' => false, 'message' => 'Email này đã theo dõi cuốn sách này rồi.'];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('tracking_rules')->insert([
            'book_id' => $bookId,
            'email' => $email,
            'target_price' => $targetPrice,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ruleId = (int) $this->db->insertID();
        session()->remove(['tracking_otp_id', 'tracking_email', 'tracking_book_id', 'tracking_verified_email', 'tracking_verified_book_id']);

        return [
            'success' => true,
            'message' => 'Đã tạo theo dõi giá. DealSach sẽ báo khi giá giảm đến mức bạn chọn.',
            'rule_id' => $ruleId,
            'disable_token' => $this->signRule($ruleId, $email, $bookId),
        ];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function disableRule(int $ruleId, string $email, string $token): array
    {
        $email = $this->otpService->normalizeEmail($email);
        $rule = $this->db->table('tracking_rules')
            ->where('id', $ruleId)
            ->where('email', $email)
            ->get()
            ->getRowArray();

        if ($rule === null) {
            return ['success' => false, 'message' => 'Không tìm thấy theo dõi giá phù hợp.'];
        }

        if (! hash_equals($this->signRule($ruleId, $email, (int) $rule['book_id']), $token)) {
            return ['success' => false, 'message' => 'Token tắt theo dõi không hợp lệ.'];
        }

        if (! (bool) $rule['is_active']) {
            return ['success' => true, 'message' => 'Theo dõi giá đã được tắt trước đó.'];
        }

        $this->db->table('tracking_rules')->where('id', $ruleId)->update([
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Đã tắt theo dõi giá.'];
    }

    public function signRule(int $ruleId, string $email, int $bookId): string
    {
        $secret = env('encryption.key') ?: env('app.baseURL', 'dealsach-demo-signing-key');

        return hash_hmac('sha256', $ruleId . '|' . $email . '|' . $bookId, (string) $secret);
    }
}
