<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RetailerSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            ['name' => 'Fahasa', 'slug' => 'fahasa', 'website_url' => 'https://www.fahasa.com'],
            ['name' => 'Nhà sách Phương Nam', 'slug' => 'nhasachphuongnam', 'website_url' => 'https://nhasachphuongnam.com'],
            ['name' => 'Tiki', 'slug' => 'tiki', 'website_url' => 'https://tiki.vn'],
            ['name' => 'Shopee', 'slug' => 'shopee', 'website_url' => 'https://shopee.vn'],
        ];

        foreach ($data as &$retailer) {
            $retailer['is_active'] = true;
            $retailer['created_at'] = $now;
            $retailer['updated_at'] = $now;
        }

        $this->db->table('retailers')->insertBatch($data);
    }
}
