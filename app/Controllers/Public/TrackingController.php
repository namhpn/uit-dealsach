<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Services\OtpService;
use App\Services\TrackingService;

class TrackingController extends BaseController
{
    public function requestOtp()
    {
        if (! $this->validate(['book_id' => 'required|integer', 'email' => 'required|valid_email'])) {
            return $this->json(false, 'Vui lòng nhập email hợp lệ.');
        }

        $result = (new OtpService())->request(
            (string) $this->request->getPost('email'),
            (int) $this->request->getPost('book_id')
        );

        return $this->json($result['success'], $result['message'], $result);
    }

    public function verifyOtp()
    {
        if (! $this->validate(['book_id' => 'required|integer', 'email' => 'required|valid_email', 'otp' => 'required|exact_length[6]|numeric'])) {
            return $this->json(false, 'OTP phải gồm đúng 6 chữ số.');
        }

        $result = (new OtpService())->verify(
            (string) $this->request->getPost('email'),
            (int) $this->request->getPost('book_id'),
            (string) $this->request->getPost('otp')
        );

        return $this->json($result['success'], $result['message'], $result);
    }

    public function createRule()
    {
        if (! $this->validate(['book_id' => 'required|integer', 'email' => 'required|valid_email', 'target_price' => 'required|integer|greater_than[0]'])) {
            return $this->json(false, 'Vui lòng nhập đầy đủ email và giá mục tiêu.');
        }

        $result = (new TrackingService())->createRule(
            (string) $this->request->getPost('email'),
            (int) $this->request->getPost('book_id'),
            (int) $this->request->getPost('target_price')
        );

        return $this->json($result['success'], $result['message'], $result);
    }

    public function disableRule()
    {
        return $this->json(false, 'Tắt theo dõi bằng token sẽ được hoàn thiện ở P8.');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(bool $success, string $message, array $data = [])
    {
        unset($data['success'], $data['message']);

        return $this->response->setStatusCode($success ? 200 : 422)->setJSON(array_merge([
            'success' => $success,
            'message' => $message,
            'csrf' => csrf_hash(),
        ], $data));
    }
}
