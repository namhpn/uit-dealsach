<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class RetailerSeeder extends Seeder {
    public function run() {
        $data = [
            ['name' => 'Fahasa', 'slug' => 'fahasa', 'website_url' => 'https://www.fahasa.com', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Nhasachphuongnam', 'slug' => 'nhasachphuongnam', 'website_url' => 'https://nhasachphuongnam.com', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Tiki', 'slug' => 'tiki', 'website_url' => 'https://tiki.vn', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Shopee', 'slug' => 'shopee', 'website_url' => 'https://shopee.vn', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('retailers')->insertBatch($data);
    }
}
