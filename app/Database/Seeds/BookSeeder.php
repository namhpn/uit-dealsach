<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $authors = [
            ['Dale Carnegie', 'dale-carnegie'],
            ['Paulo Coelho', 'paulo-coelho'],
            ['Nguyễn Nhật Ánh', 'nguyen-nhat-anh'],
            ['Napoleon Hill', 'napoleon-hill'],
            ['Robert T. Kiyosaki', 'robert-t-kiyosaki'],
            ['Yuval Noah Harari', 'yuval-noah-harari'],
            ['Tony Buổi Sáng', 'tony-buoi-sang'],
            ['Nguyên Phong', 'nguyen-phong'],
            ['Harper Lee', 'harper-lee'],
            ['Fyodor Dostoevsky', 'fyodor-dostoevsky'],
            ['Nguyễn Phong Việt', 'nguyen-phong-viet'],
            ['Rosie Nguyễn', 'rosie-nguyen'],
        ];

        $authorRows = [];
        foreach ($authors as [$name, $slug]) {
            $authorRows[] = [
                'name' => $name,
                'slug' => $slug,
                'biography' => 'Tác giả trong bộ dữ liệu demo DealSach.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db->table('authors')->insertBatch($authorRows);

        $books = [
            ['Đắc Nhân Tâm', 'dac-nhan-tam', 3, 1, 3, '9786045876412'],
            ['Nhà Giả Kim', 'nha-gia-kim', 4, 2, 1, '9786047787303'],
            ['Mắt Biếc', 'mat-biec', 1, 3, 1, '9786041143112'],
            ['Nghĩ Giàu Làm Giàu', 'nghi-giau-lam-giau', 6, 4, 2, '9786047793052'],
            ['Dạy Con Làm Giàu', 'day-con-lam-giau', 6, 5, 2, '9786047769392'],
            ['Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'cho-toi-xin-mot-ve-di-tuoi-tho', 1, 3, 4, '9786041171986'],
            ['Cô Gái Đến Từ Hôm Qua', 'co-gai-den-tu-hom-qua', 1, 3, 1, '9786041172266'],
            ['Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'toi-thay-hoa-vang-tren-co-xanh', 1, 3, 4, '9786041172259'],
            ['Cây Chuối Non Đi Giày Kiểu Tây', 'cay-chuoi-non-di-giay-kieu-tay', 1, 3, 4, '9786041172242'],
            ['Ngồi Khóc Trên Cây', 'ngoi-khoc-tren-cay', 1, 3, 1, '9786041172235'],
            ['Sự Im Lặng Của Bầy Cừu', 'su-im-lang-cua-bay-cuu', 4, 10, 1, '9786047784029'],
            ['Tội Ác Và Hình Phạt', 'toi-ac-va-hinh-phat', 4, 10, 1, '9786047784012'],
            ['Suối Nguồn', 'suoi-nguon', 4, 2, 1, '9786047784036'],
            ['Giết Con Chim Nhại', 'giet-con-chim-nhai', 4, 9, 1, '9786047784043'],
            ['Lược Sử Loài Người', 'luoc-su-loai-nguoi', 4, 6, 5, '9786047784050'],
            ['Tuổi Trẻ Đáng Giá Bao Nhiêu', 'tuoi-tre-dang-gia-bao-nhieu', 3, 12, 3, '9786047784067'],
            ['Cà Phê Cùng Tony', 'ca-phe-cung-tony', 1, 7, 3, '9786047784074'],
            ['Trên Đường Băng', 'tren-duong-bang', 1, 7, 3, '9786047784081'],
            ['Hành Trình Về Phương Đông', 'hanh-trinh-ve-phuong-dong', 3, 8, 8, '9786047784098'],
            ['Muôn Kiếp Nhân Sinh', 'muon-kiep-nhan-sinh', 3, 8, 8, '9786047784104'],
            ['Sapiens Lược Sử Loài Người', 'sapiens-luoc-su-loai-nguoi', 4, 6, 5, '9786047784111'],
            ['Tôi Tài Giỏi Bạn Cũng Thế', 'toi-tai-gioi-ban-cung-the', 5, 11, 3, '9786047784128'],
            ['Không Gia Đình', 'khong-gia-dinh', 2, 9, 4, '9786047784135'],
            ['Bí Mật Của May Mắn', 'bi-mat-cua-may-man', 5, 2, 3, '9786047784142'],
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
