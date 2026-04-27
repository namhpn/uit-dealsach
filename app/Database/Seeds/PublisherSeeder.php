<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class PublisherSeeder extends Seeder {
    public function run() {
        $data = [
            ['name' => 'NXB Trẻ', 'slug' => 'nxb-tre', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'NXB Kim Đồng', 'slug' => 'nxb-kim-dong', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'NXB Tổng Hợp TP.HCM', 'slug' => 'nxb-tong-hop-tphcm', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'NXB Thế Giới', 'slug' => 'nxb-the-gioi', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Alpha Books', 'slug' => 'alpha-books', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('publishers')->insertBatch($data);
    }
}
