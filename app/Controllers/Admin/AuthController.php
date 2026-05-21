<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class AuthController extends BaseController
{
    public function loginForm(): string
    {
        return view('admin/login');
    }

    public function login()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng nhập đầy đủ thông tin đăng nhập.');
        }

        $db = Database::connect();
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $admin = $db->table('admin_users')->where('username', $username)->get()->getRowArray();

        if ($admin === null || ! password_verify($password, (string) $admin['password_hash'])) {
            $this->logAuth(null, 'failed');

            return redirect()->back()->withInput()->with('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
        }

        session()->regenerate(true);
        session()->set([
            'admin_logged_in' => true,
            'admin_id' => (int) $admin['id'],
            'admin_username' => (string) $admin['username'],
            'admin_display_name' => (string) $admin['display_name'],
        ]);
        $this->logAuth((int) $admin['id'], 'success');

        $intended = (string) session()->get('admin_intended_url');
        session()->remove('admin_intended_url');

        return redirect()->to($intended !== '' ? $intended : site_url(env('dealsach.adminPath', 'ds-admin')))
            ->with('success', 'Đăng nhập quản trị thành công.');
    }

    public function logout()
    {
        $adminId = session()->get('admin_id');
        if ($adminId !== null) {
            $this->logAuth((int) $adminId, 'logout');
        }

        session()->remove([
            'admin_logged_in',
            'admin_id',
            'admin_username',
            'admin_display_name',
            'admin_intended_url',
        ]);
        session()->regenerate(true);

        return redirect()->to(site_url(env('dealsach.adminPath', 'ds-admin') . '/login'))
            ->with('success', 'Đã đăng xuất.');
    }

    private function logAuth(?int $adminId, string $status): void
    {
        Database::connect()->table('admin_auth_logs')->insert([
            'admin_id' => $adminId,
            'ip_address' => (string) $this->request->getIPAddress(),
            'user_agent' => (string) $this->request->getUserAgent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
