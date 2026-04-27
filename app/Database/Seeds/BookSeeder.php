<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class BookSeeder extends Seeder {
    public function run() {
        $authors = [
            ['name' => 'Dale Carnegie', 'slug' => 'dale-carnegie', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Paulo Coelho', 'slug' => 'paulo-coelho', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Nguyễn Nhật Ánh', 'slug' => 'nguyen-nhat-anh', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Napoleon Hill', 'slug' => 'napoleon-hill', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Robert T. Kiyosaki', 'slug' => 'robert-t-kiyosaki', 'created_at' => date('Y-m-d H:i:s')]
        ];
        $this->db->table('authors')->insertBatch($authors);
        
        $books = [
            ['title' => 'Đắc Nhân Tâm', 'slug' => 'dac-nhan-tam', 'publisher_id' => 3, 'created_at' => date('Y-m-d H:i:s')],
            ['title' => 'Nhà Giả Kim', 'slug' => 'nha-gia-kim', 'publisher_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['title' => 'Mắt Biếc', 'slug' => 'mat-biec', 'publisher_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['title' => 'Nghĩ Giàu Làm Giàu', 'slug' => 'nghi-giau-lam-giau', 'publisher_id' => 3, 'created_at' => date('Y-m-d H:i:s')],
            ['title' => 'Dạy Con Làm Giàu', 'slug' => 'day-con-lam-giau', 'publisher_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            // Add more books to reach at least 20...
        ];
        // Complete the 20 books logic here. Let's add 15 more books.
        $more_books = [
            'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'Cô Gái Đến Từ Hôm Qua', 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Cây Chuối Non Đi Giày Kiểu Tây', 'Ngồi Khóc Trên Cây',
            'Sự Im Lặng Của Bầy Cừu', 'Tội Ác Và Hình Phạt', 'Suối Nguồn', 'Giết Con Chim Nhại', 'Lược Sử Loài Người',
            'Tuổi Trẻ Đáng Giá Bao Nhiêu', 'Cà Phê Cùng Tony', 'Trên Đường Băng', 'Hành Trình Về Phương Đông', 'Muôn Kiếp Nhân Sinh'
        ];
        foreach ($more_books as $index => $title) {
            $books[] = [
                'title' => $title,
                'slug' => strtolower(str_replace(' ', '-', $title)) . '-' . $index,
                'publisher_id' => ($index % 5) + 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        $this->db->table('books')->insertBatch($books);
        
        // Link authors
        $bookAuthors = [];
        $bookCategories = [];
        for ($i = 1; $i <= 20; $i++) {
            $bookAuthors[] = ['book_id' => $i, 'author_id' => ($i % 5) + 1];
            $bookCategories[] = ['book_id' => $i, 'category_id' => ($i % 8) + 1, 'is_primary' => true];
        }
        $this->db->table('book_authors')->insertBatch($bookAuthors);
        $this->db->table('book_categories')->insertBatch($bookCategories);
    }
}
