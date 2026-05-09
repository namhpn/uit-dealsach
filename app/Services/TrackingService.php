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
     * @return array{success: bool, message: string, rule_id?: int}
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
        ];
    }
}
