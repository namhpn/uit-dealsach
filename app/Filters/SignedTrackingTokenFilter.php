<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SignedTrackingTokenFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ruleId = (int) $request->getPost('rule_id');
        $email = trim((string) $request->getPost('email'));
        $token = trim((string) $request->getPost('token'));

        if ($ruleId <= 0 || $email === '' || $token === '') {
            return service('response')->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Thiếu token tắt theo dõi.',
                'csrf' => csrf_hash(),
            ]);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
