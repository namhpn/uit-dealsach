<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AdminAuthService;

class AuthController extends BaseController
{
    public function loginForm(): string
    {
        return view('admin/login', [
            'pageTitle' => 'Đăng nhập quản trị',
            'adminPath' => env('dealsach.adminPath', 'ds-admin'),
        ]);
    }

    public function login()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.');
        }

        if (! (new AdminAuthService())->attempt((string) $this->request->getPost('username'), (string) $this->request->getPost('password'), $this->request)) {
            return redirect()->back()->withInput()->with('error', 'Thông tin đăng nhập không đúng.');
        }

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin')))->with('success', 'Đăng nhập thành công.');
    }

    public function logout()
    {
        (new AdminAuthService())->logout($this->request);

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/login'))->with('success', 'Đã đăng xuất.');
    }
}
