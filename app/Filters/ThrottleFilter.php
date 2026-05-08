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
        $limit = 30;
        $seconds = 60;

        if (! service('throttler')->check($key, $limit, $seconds)) {
            return service('response')->setStatusCode(429)->setBody('Too many requests');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
