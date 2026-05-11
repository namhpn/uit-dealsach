<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class ImportService
{
    private BaseConnection $db;

    /** @var array<string, int> */
    private array $fallbackBooks = [
        'fahasa' => 1,
        'nhasachphuongnam' => 2,
        'phuongnam' => 2,
        'tiki' => 3,
        'shopee' => 4,
    ];

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * @return array<string, int|string>
     */
    public function importRetailer(string $retailerSlug): array
    {
        $retailerSlug = $retailerSlug === 'phuongnam' ? 'nhasachphuongnam' : $retailerSlug;
        $retailer = $this->db->table('retailers')->where('slug', $retailerSlug)->where('is_active', 1)->get()->getRowArray();
        if ($retailer === null) {
            throw new \InvalidArgumentException('Nhà bán không tồn tại: ' . $retailerSlug);
        }

        $path = WRITEPATH . 'import/' . $retailerSlug . '.json';
        if (! is_file($path)) {
            throw new \RuntimeException('Không tìm thấy file import: ' . $path);
        }

        $rows = json_decode((string) file_get_contents($path), true);
        if (! is_array($rows)) {
            throw new \RuntimeException('File JSON không hợp lệ: ' . $path);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('crawl_jobs')->insert([
            'status' => 'running',
            'started_at' => $now,
            'total_items_processed' => 0,
            'created_at' => $now,
        ]);
        $jobId = (int) $this->db->insertID();

        $stats = [
            'retailer' => $retailerSlug,
            'job_id' => $jobId,
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'snapshots' => 0,
            'changes' => 0,
            'errors' => 0,
        ];

        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                $this->recordError($jobId, $index, 'invalid_row', 'Dòng import không phải object.', $row);
                $stats['errors']++;
                continue;
            }

            $bookId = $this->resolveBookId($row, $retailerSlug);
            $url = trim((string) ($row['url'] ?? ''));
            $originalId = trim((string) ($row['original_id'] ?? ''));
            $listed = isset($row['listed_price']) ? (int) $row['listed_price'] : null;
            $discounted = isset($row['discounted_price']) && $row['discounted_price'] !== null ? (int) $row['discounted_price'] : null;
            $effective = $discounted ?? $listed;

            if ($bookId === null || $url === '' || $listed === null || $listed <= 0) {
                $this->recordError($jobId, $index, 'validation', 'Thiếu book, url hoặc listed_price hợp lệ.', $row);
                $stats['errors']++;
                continue;
            }

            $existing = $this->db->table('retailer_items')
                ->where('retailer_id', (int) $retailer['id'])
                ->where('original_id', $originalId !== '' ? $originalId : md5($url))
                ->get()
                ->getRowArray();

            $payload = [
                'book_id' => $bookId,
                'retailer_id' => (int) $retailer['id'],
                'url' => $url,
                'original_id' => $originalId !== '' ? $originalId : md5($url),
                'current_listed_price' => $listed,
                'current_discounted_price' => $discounted,
                'current_effective_price' => $effective,
                'in_stock' => (bool) ($row['in_stock'] ?? true),
                'is_active' => true,
                'last_crawled_at' => $now,
                'updated_at' => $now,
            ];

            if ($existing === null) {
                $payload['created_at'] = $now;
                $this->db->table('retailer_items')->insert($payload);
                $itemId = (int) $this->db->insertID();
                $stats['created']++;
            } else {
                $itemId = (int) $existing['id'];
                $stats['changes'] += $this->recordChanges($itemId, $jobId, $existing, $payload);
                $this->db->table('retailer_items')->where('id', $itemId)->update($payload);
                $stats['updated']++;
            }

            $this->db->table('price_snapshots')->insert([
                'retailer_item_id' => $itemId,
                'crawl_job_id' => $jobId,
                'listed_price' => $listed,
                'discounted_price' => $discounted,
                'effective_price' => $effective,
                'in_stock' => (bool) ($row['in_stock'] ?? true),
                'captured_at' => $now,
            ]);

            $stats['processed']++;
            $stats['snapshots']++;
        }

        $this->db->table('crawl_jobs')->where('id', $jobId)->update([
            'status' => $stats['errors'] > 0 && $stats['processed'] === 0 ? 'failed' : 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'total_items_processed' => $stats['processed'],
        ]);

        return $stats;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function resolveBookId(array $row, string $retailerSlug): ?int
    {
        if (! empty($row['book_id'])) {
            return (int) $row['book_id'];
        }

        $builder = $this->db->table('books')->select('id')->where('deleted_at', null);
        if (! empty($row['isbn'])) {
            $found = $builder->where('isbn', (string) $row['isbn'])->get()->getRow('id');
            return $found ? (int) $found : null;
        }

        if (! empty($row['book_slug'])) {
            $found = $builder->where('slug', (string) $row['book_slug'])->get()->getRow('id');
            return $found ? (int) $found : null;
        }

        if (! empty($row['title'])) {
            $found = $builder->like('title', (string) $row['title'])->get()->getRow('id');
            return $found ? (int) $found : null;
        }

        return $this->fallbackBooks[$retailerSlug] ?? 1;
    }

    /**
     * @param array<string, mixed> $old
     * @param array<string, mixed> $new
     */
    private function recordChanges(int $itemId, int $jobId, array $old, array $new): int
    {
        $count = 0;
        foreach (['current_listed_price', 'current_discounted_price', 'current_effective_price', 'in_stock', 'url'] as $field) {
            if ((string) ($old[$field] ?? '') === (string) ($new[$field] ?? '')) {
                continue;
            }

            $this->db->table('retailer_item_changes')->insert([
                'retailer_item_id' => $itemId,
                'crawl_job_id' => $jobId,
                'field_name' => $field,
                'old_value' => $old[$field] === null ? null : (string) $old[$field],
                'new_value' => $new[$field] === null ? null : (string) $new[$field],
                'detected_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * @param mixed $row
     */
    private function recordError(int $jobId, int $index, string $type, string $message, $row): void
    {
        $this->db->table('crawl_job_errors')->insert([
            'crawl_job_id' => $jobId,
            'source_row_index' => $index,
            'error_type' => $type,
            'error_message' => $message,
            'raw_data' => json_encode($row, JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
