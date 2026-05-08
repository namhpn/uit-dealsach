<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $authors = [
            ['name' => 'Dale Carnegie', 'slug' => 'dale-carnegie'],
            ['name' => 'Paulo Coelho', 'slug' => 'paulo-coelho'],
            ['name' => 'Nguyễn Nhật Ánh', 'slug' => 'nguyen-nhat-anh'],
            ['name' => 'Napoleon Hill', 'slug' => 'napoleon-hill'],
            ['name' => 'Robert T. Kiyosaki', 'slug' => 'robert-t-kiyosaki'],
            ['name' => 'Yuval Noah Harari', 'slug' => 'yuval-noah-harari'],
            ['name' => 'Tony Buổi Sáng', 'slug' => 'tony-buoi-sang'],
            ['name' => 'Nguyên Phong', 'slug' => 'nguyen-phong'],
        ];

        foreach ($authors as &$author) {
            $author['created_at'] = $now;
            $author['updated_at'] = $now;
        }

        $this->db->table('authors')->insertBatch($authors);

        $books = [
            ['Đắc Nhân Tâm', 'dac-nhan-tam', 3, 1, 3, '9786045876412'],
            ['Nhà Giả Kim', 'nha-gia-kim', 4, 2, 1, '9786047787303'],
            ['Mắt Biếc', 'mat-biec', 1, 3, 1, '9786041143112'],
            ['Nghĩ Giàu Làm Giàu', 'nghi-giau-lam-giau', 5, 4, 2, '9786047793052'],
            ['Dạy Con Làm Giàu', 'day-con-lam-giau', 5, 5, 2, '9786047769392'],
            ['Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'cho-toi-xin-mot-ve-di-tuoi-tho', 1, 3, 4, '9786041171986'],
            ['Cô Gái Đến Từ Hôm Qua', 'co-gai-den-tu-hom-qua', 1, 3, 1, '9786041172266'],
            ['Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'toi-thay-hoa-vang-tren-co-xanh', 1, 3, 4, '9786041172259'],
            ['Cây Chuối Non Đi Giày Kiểu Tây', 'cay-chuoi-non-di-giay-kieu-tay', 1, 3, 4, '9786041172242'],
            ['Ngồi Khóc Trên Cây', 'ngoi-khoc-tren-cay', 1, 3, 1, '9786041172235'],
            ['Sự Im Lặng Của Bầy Cừu', 'su-im-lang-cua-bay-cuu', 4, 1, 1, '9786047784029'],
            ['Tội Ác Và Hình Phạt', 'toi-ac-va-hinh-phat', 4, 1, 1, '9786047784012'],
            ['Suối Nguồn', 'suoi-nguon', 5, 1, 1, '9786047784036'],
            ['Giết Con Chim Nhại', 'giet-con-chim-nhai', 4, 1, 1, '9786047784043'],
            ['Lược Sử Loài Người', 'luoc-su-loai-nguoi', 4, 6, 5, '9786047784050'],
            ['Tuổi Trẻ Đáng Giá Bao Nhiêu', 'tuoi-tre-dang-gia-bao-nhieu', 3, 7, 3, '9786047784067'],
            ['Cà Phê Cùng Tony', 'ca-phe-cung-tony', 1, 7, 3, '9786047784074'],
            ['Trên Đường Băng', 'tren-duong-bang', 1, 7, 3, '9786047784081'],
            ['Hành Trình Về Phương Đông', 'hanh-trinh-ve-phuong-dong', 3, 8, 5, '9786047784098'],
            ['Muôn Kiếp Nhân Sinh', 'muon-kiep-nhan-sinh', 3, 8, 5, '9786047784104'],
        ];

        $bookRows = [];
        foreach ($books as $book) {
            $bookRows[] = [
                'title' => $book[0],
                'slug' => $book[1],
                'isbn' => $book[5],
                'format' => 'Bìa mềm',
                'language' => 'Tiếng Việt',
                'description' => 'Ấn phẩm tiếng Việt trong bộ dữ liệu demo của DealSach, dùng để minh họa tìm kiếm, lọc và so sánh giá giữa nhiều nhà bán.',
                'publisher_id' => $book[2],
                'cover_image_url' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->db->table('books')->insertBatch($bookRows);

        $bookAuthors = [];
        $bookCategories = [];
        foreach ($books as $index => $book) {
            $bookId = $index + 1;
            $bookAuthors[] = ['book_id' => $bookId, 'author_id' => $book[3]];
            $bookCategories[] = ['book_id' => $bookId, 'category_id' => $book[4], 'is_primary' => true];
        }

        $this->db->table('book_authors')->insertBatch($bookAuthors);
        $this->db->table('book_categories')->insertBatch($bookCategories);
    }
}
