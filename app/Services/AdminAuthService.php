<?php

namespace App\Services;

use App\Models\AdminAuthLogModel;
use App\Models\AdminUserModel;
use CodeIgniter\HTTP\IncomingRequest;

class AdminAuthService
{
    public function attempt(string $username, string $password, IncomingRequest $request): bool
    {
        $userModel = new AdminUserModel();
        $user = $userModel->where('username', trim($username))->first();

        $success = $user !== null && password_verify($password, (string) $user['password_hash']);
        $this->log($request, $user['id'] ?? null, $success ? 'success' : 'failed');

        if (! $success) {
            return false;
        }

        session()->regenerate(true);
        session()->set([
            'admin_id' => (int) $user['id'],
            'admin_username' => (string) $user['username'],
            'admin_display_name' => (string) $user['display_name'],
            'is_admin_logged_in' => true,
        ]);

        return true;
    }

    public function logout(IncomingRequest $request): void
    {
        $adminId = session()->get('admin_id');
        if ($adminId !== null) {
            $this->log($request, (int) $adminId, 'logout');
        }

        session()->remove(['admin_id', 'admin_username', 'admin_display_name', 'is_admin_logged_in']);
        session()->regenerate(true);
    }

    private function log(IncomingRequest $request, int|string|null $adminId, string $status): void
    {
        (new AdminAuthLogModel())->insert([
            'admin_id' => $adminId === null ? null : (int) $adminId,
            'ip_address' => (string) $request->getIPAddress(),
            'user_agent' => substr((string) $request->getUserAgent(), 0, 1000),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
