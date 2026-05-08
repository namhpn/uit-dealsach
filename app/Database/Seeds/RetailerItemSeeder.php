<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class RetailerItemSeeder extends Seeder {
    public function run() {
        $retailer_items = [];
        $snapshots = [];
        
        // Add crawl job
        $this->db->table('crawl_jobs')->insert([
            'status' => 'completed',
            'started_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'completed_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'total_items_processed' => 80,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
        $crawl_job_id = $this->db->insertID();

        $item_id = 1;
        for ($book_id = 1; $book_id <= 20; $book_id++) {
            for ($retailer_id = 1; $retailer_id <= 4; $retailer_id++) {
                $listed_price = 100000 + rand(0, 50) * 1000;
                $discounted_price = $listed_price - rand(10, 30) * 1000;
                $in_stock = rand(1, 10) > 1; // 90% in stock
                
                // Fallback to listed price if no discount
                if (rand(1, 10) > 8) {
                    $discounted_price = null;
                }
                
                $effective_price = $discounted_price ?? $listed_price;
                
                $retailer_items[] = [
                    'book_id' => $book_id,
                    'retailer_id' => $retailer_id,
                    'url' => "https://example.com/item-" . $book_id . "-" . $retailer_id,
                    'original_id' => "ORG-" . $book_id . "-" . $retailer_id,
                    'current_listed_price' => $listed_price,
                    'current_discounted_price' => $discounted_price,
                    'current_effective_price' => $effective_price,
                    'in_stock' => $in_stock,
                    'last_crawled_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                
                $snapshots[] = [
                    'retailer_item_id' => $item_id++,
                    'crawl_job_id' => $crawl_job_id,
                    'listed_price' => $listed_price,
                    'discounted_price' => $discounted_price,
                    'effective_price' => $effective_price,
                    'in_stock' => $in_stock,
                    'captured_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        $this->db->table('retailer_items')->insertBatch($retailer_items);
        $this->db->table('price_snapshots')->insertBatch($snapshots);
    }
}
