<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class OtpService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * @return array{success: bool, message: string, otp_id?: int, dev_otp?: string}
     */
    public function request(string $email, int $bookId): array
    {
        $email = $this->normalizeEmail($email);
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ.'];
        }

        if (! $this->bookExists($bookId)) {
            return ['success' => false, 'message' => 'Không tìm thấy sách cần theo dõi.'];
        }

        $cooldownKey = 'otp_resend_' . md5($email . ':' . $bookId);
        if (! service('throttler')->check($cooldownKey, 1, 60)) {
            return ['success' => false, 'message' => 'Vui lòng chờ 60 giây trước khi gửi lại OTP.'];
        }

        $otp = (string) random_int(100000, 999999);
        $now = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', time() + 600);

        $this->db->table('otp_requests')->insert([
            'email' => $email,
            'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'status' => 'pending',
            'attempt_count' => 0,
            'resend_count' => 0,
            'expires_at' => $expires,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $otpId = (int) $this->db->insertID();
        session()->set([
            'tracking_otp_id' => $otpId,
            'tracking_email' => $email,
            'tracking_book_id' => $bookId,
        ]);

        $this->logEmail($email, $otp, 'sent');
        log_message('info', 'DealSach dev OTP for {email} book {book}: {otp}', [
            'email' => $email,
            'book' => $bookId,
            'otp' => $otp,
        ]);

        $response = [
            'success' => true,
            'message' => 'Mã OTP đã được tạo. Vui lòng kiểm tra email hoặc log dev.',
            'otp_id' => $otpId,
        ];

        if (ENVIRONMENT === 'development') {
            $response['dev_otp'] = $otp;
            $response['message'] = 'Mã OTP dev đã được tạo và ghi vào log.';
        }

        return $response;
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function verify(string $email, int $bookId, string $otp): array
    {
        $email = $this->normalizeEmail($email);
        $otpId = (int) session()->get('tracking_otp_id');

        $row = $this->db->table('otp_requests')
            ->where('id', $otpId)
            ->where('email', $email)
            ->where('status', 'pending')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if ($row === null || (int) session()->get('tracking_book_id') !== $bookId) {
            return ['success' => false, 'message' => 'Không tìm thấy yêu cầu OTP phù hợp.'];
        }

        if (strtotime((string) $row['expires_at']) < time()) {
            $this->markOtp((int) $row['id'], 'expired', (int) $row['attempt_count']);
            return ['success' => false, 'message' => 'Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.'];
        }

        if ((int) $row['attempt_count'] >= 5) {
            $this->markOtp((int) $row['id'], 'locked', (int) $row['attempt_count']);
            return ['success' => false, 'message' => 'Bạn đã nhập sai quá 5 lần. Vui lòng gửi lại OTP.'];
        }

        if (! password_verify(trim($otp), (string) $row['otp_hash'])) {
            $attempts = (int) $row['attempt_count'] + 1;
            $this->markOtp((int) $row['id'], $attempts >= 5 ? 'locked' : 'pending', $attempts);

            return ['success' => false, 'message' => 'Mã OTP không đúng. Số lần thử: ' . $attempts . '/5.'];
        }

        $this->markOtp((int) $row['id'], 'verified', (int) $row['attempt_count']);
        session()->set([
            'tracking_verified_email' => $email,
            'tracking_verified_book_id' => $bookId,
        ]);

        return ['success' => true, 'message' => 'Xác thực OTP thành công.'];
    }

    private function markOtp(int $id, string $status, int $attempts): void
    {
        $this->db->table('otp_requests')->where('id', $id)->update([
            'status' => $status,
            'attempt_count' => $attempts,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function logEmail(string $email, string $otp, string $status): void
    {
        $body = 'Mã OTP DealSach của bạn là ' . $otp . '. Mã có hiệu lực trong 10 phút.';
        $this->db->table('email_logs')->insert([
            'recipient_email' => $email,
            'email_type' => 'otp',
            'subject' => 'Mã OTP theo dõi giá sách DealSach',
            'body_preview' => $body,
            'status' => $status,
            'provider_message_id' => null,
            'error_message' => null,
            'sent_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function bookExists(int $bookId): bool
    {
        return $this->db->table('books')
            ->where('id', $bookId)
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->countAllResults() > 0;
    }

    public function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email), 'UTF-8');
    }
}
