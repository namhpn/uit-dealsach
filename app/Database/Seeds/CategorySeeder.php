<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class CategorySeeder extends Seeder {
    public function run() {
        $data = [
            ['name' => 'Văn học', 'slug' => 'van-hoc', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Kinh tế', 'slug' => 'kinh-te', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Kỹ năng sống', 'slug' => 'ky-nang-song', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Thiếu nhi', 'slug' => 'thieu-nhi', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Khoa học', 'slug' => 'khoa-hoc', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Lịch sử', 'slug' => 'lich-su', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Ngoại ngữ', 'slug' => 'ngoai-ngu', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Công nghệ', 'slug' => 'cong-nghe', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('categories')->insertBatch($data);
    }
}
