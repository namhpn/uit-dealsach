<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('is_admin_logged_in')) {
            $adminPath = env('dealsach.adminPath', 'ds-admin');

            return redirect()->to(site_url($adminPath . '/login'))->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
