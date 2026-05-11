<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            ['name' => 'NXB Trẻ', 'slug' => 'nxb-tre', 'country' => 'Việt Nam'],
            ['name' => 'NXB Kim Đồng', 'slug' => 'nxb-kim-dong', 'country' => 'Việt Nam'],
            ['name' => 'NXB Tổng Hợp TP.HCM', 'slug' => 'nxb-tong-hop-tphcm', 'country' => 'Việt Nam'],
            ['name' => 'NXB Thế Giới', 'slug' => 'nxb-the-gioi', 'country' => 'Việt Nam'],
            ['name' => 'NXB Lao Động', 'slug' => 'nxb-lao-dong', 'country' => 'Việt Nam'],
            ['name' => 'Alpha Books', 'slug' => 'alpha-books', 'country' => 'Việt Nam'],
        ];

        foreach ($data as &$publisher) {
            $publisher['website_url'] = null;
            $publisher['logo_url'] = null;
            $publisher['is_active'] = true;
            $publisher['created_at'] = $now;
            $publisher['updated_at'] = $now;
        }

        $this->db->table('publishers')->insertBatch($data);
    }
}
