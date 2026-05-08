<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class AdminUserSeeder extends Seeder {
    public function run() {
        $data = [
            'username' => 'admin',
            'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
            'display_name' => 'Quản trị viên',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('admin_users')->insert($data);
    }
}
