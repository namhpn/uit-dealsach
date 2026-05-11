<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RetailerItemSeeder extends Seeder
{
    public function run()
    {
        $jobs = [];
        for ($batch = 1; $batch <= 3; $batch++) {
            $time = date('Y-m-d H:i:s', strtotime('-' . (4 - $batch) . ' days'));
            $jobs[] = [
                'status' => 'completed',
                'started_at' => $time,
                'completed_at' => date('Y-m-d H:i:s', strtotime($time . ' +12 minutes')),
                'total_items_processed' => 96,
                'created_at' => $time,
            ];
        }
        $this->db->table('crawl_jobs')->insertBatch($jobs);

        $items = [];
        for ($bookId = 1; $bookId <= 24; $bookId++) {
            for ($retailerId = 1; $retailerId <= 4; $retailerId++) {
                $listed = 88000 + ($bookId * 2700) + ($retailerId * 1900);
                $discounted = (($bookId + $retailerId) % 5 === 0) ? null : $listed - (7000 + ($retailerId * 1200));
                $effective = $discounted ?? $listed;
                $inStock = !(($bookId + $retailerId) % 11 === 0);

                $items[] = [
                    'book_id' => $bookId,
                    'retailer_id' => $retailerId,
                    'url' => 'https://example.com/dealsach/book-' . $bookId . '/retailer-' . $retailerId,
                    'original_id' => 'DS-' . $bookId . '-' . $retailerId,
                    'current_listed_price' => $listed,
                    'current_discounted_price' => $discounted,
                    'current_effective_price' => $effective,
                    'in_stock' => $inStock,
                    'is_active' => true,
                    'last_crawled_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                ];
            }
        }
        $this->db->table('retailer_items')->insertBatch($items);

        $snapshots = [];
        $changes = [];
        $itemId = 1;
        for ($bookId = 1; $bookId <= 24; $bookId++) {
            for ($retailerId = 1; $retailerId <= 4; $retailerId++, $itemId++) {
                $listed = 88000 + ($bookId * 2700) + ($retailerId * 1900);
                $discounted = (($bookId + $retailerId) % 5 === 0) ? null : $listed - (7000 + ($retailerId * 1200));
                $effective = $discounted ?? $listed;
                $inStock = !(($bookId + $retailerId) % 11 === 0);

                for ($batch = 1; $batch <= 3; $batch++) {
                    $delta = (3 - $batch) * 5000;
                    $captured = date('Y-m-d H:i:s', strtotime('-' . (4 - $batch) . ' days +15 minutes'));
                    $snapshots[] = [
                        'retailer_item_id' => $itemId,
                        'crawl_job_id' => $batch,
                        'listed_price' => $listed + $delta,
                        'discounted_price' => $discounted === null ? null : $discounted + $delta,
                        'effective_price' => $effective + $delta,
                        'in_stock' => $batch === 3 ? $inStock : true,
                        'captured_at' => $captured,
                    ];
                }

                if ($itemId <= 12) {
                    $changes[] = [
                        'retailer_item_id' => $itemId,
                        'crawl_job_id' => 3,
                        'field_name' => 'current_effective_price',
                        'old_value' => (string) ($effective + 5000),
                        'new_value' => (string) $effective,
                        'detected_at' => date('Y-m-d H:i:s', strtotime('-1 day +15 minutes')),
                    ];
                }
                if (! $inStock && count($changes) < 20) {
                    $changes[] = [
                        'retailer_item_id' => $itemId,
                        'crawl_job_id' => 3,
                        'field_name' => 'in_stock',
                        'old_value' => '1',
                        'new_value' => '0',
                        'detected_at' => date('Y-m-d H:i:s', strtotime('-1 day +15 minutes')),
                    ];
                }
            }
        }
        $this->db->table('price_snapshots')->insertBatch($snapshots);
        $this->db->table('retailer_item_changes')->insertBatch($changes);

        $errors = [
            ['crawl_job_id' => 1, 'source_row_index' => 12, 'error_type' => 'missing_price', 'error_message' => 'Dòng nguồn thiếu giá bán.', 'raw_data' => '{"title":"Dòng lỗi giá"}'],
            ['crawl_job_id' => 2, 'source_row_index' => 27, 'error_type' => 'missing_url', 'error_message' => 'Dòng nguồn thiếu liên kết sản phẩm.', 'raw_data' => '{"title":"Dòng lỗi URL"}'],
            ['crawl_job_id' => 3, 'source_row_index' => 41, 'error_type' => 'unknown_book', 'error_message' => 'Không khớp được sách từ ISBN hoặc tiêu đề.', 'raw_data' => '{"isbn":"000"}'],
        ];
        foreach ($errors as &$error) {
            $error['created_at'] = date('Y-m-d H:i:s');
        }
        $this->db->table('crawl_job_errors')->insertBatch($errors);
    }
}
