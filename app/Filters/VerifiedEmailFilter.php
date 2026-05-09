<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class VerifiedEmailFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('tracking_verified_email')) {
            return service('response')->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Vui lòng xác thực OTP trước khi tạo theo dõi giá.',
                'csrf' => csrf_hash(),
            ]);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
