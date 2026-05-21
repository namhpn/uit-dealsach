<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $bucket = (string) ($arguments[0] ?? 'default');
        $key = preg_replace('/[^A-Za-z0-9_.-]/', '_', $bucket . '_' . $request->getIPAddress()) ?? 'default';

        if (! service('throttler')->check($key, 30, 60)) {
            return service('response')->setStatusCode(429)->setJSON([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh. Vui lòng thử lại sau.',
                'csrf' => csrf_hash(),
            ]);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
