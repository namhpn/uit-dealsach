<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            ['name' => 'Văn học', 'slug' => 'van-hoc', 'sort_order' => 1],
            ['name' => 'Kinh tế', 'slug' => 'kinh-te', 'sort_order' => 2],
            ['name' => 'Kỹ năng sống', 'slug' => 'ky-nang-song', 'sort_order' => 3],
            ['name' => 'Thiếu nhi', 'slug' => 'thieu-nhi', 'sort_order' => 4],
            ['name' => 'Khoa học', 'slug' => 'khoa-hoc', 'sort_order' => 5],
            ['name' => 'Lịch sử', 'slug' => 'lich-su', 'sort_order' => 6],
            ['name' => 'Ngoại ngữ', 'slug' => 'ngoai-ngu', 'sort_order' => 7],
            ['name' => 'Công nghệ', 'slug' => 'cong-nghe', 'sort_order' => 8],
        ];

        foreach ($data as &$category) {
            $category['is_active'] = true;
            $category['created_at'] = $now;
            $category['updated_at'] = $now;
        }

        $this->db->table('categories')->insertBatch($data);
    }
}
