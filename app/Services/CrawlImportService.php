<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use RuntimeException;
use Throwable;

class CrawlImportService
{
    private const RETAILERS = [
        'fahasa' => [
            'slug' => 'fahasa',
            'file' => 'fahasa.json',
        ],
        'nhasachphuongnam' => [
            'slug' => 'nhasachphuongnam',
            'file' => 'nhasachphuongnam.json',
        ],
        'phuongnam' => [
            'slug' => 'nhasachphuongnam',
            'file' => 'nhasachphuongnam.json',
        ],
        'tiki' => [
            'slug' => 'tiki',
            'file' => 'tiki.json',
        ],
        'shopee' => [
            'slug' => 'shopee',
            'file' => 'shopee.json',
        ],
    ];

    private BaseConnection $db;
    private MatchingService $matching;
    private ComparisonService $comparison;

    public function __construct(?BaseConnection $db = null, ?MatchingService $matching = null, ?ComparisonService $comparison = null)
    {
        $this->db = $db ?? Database::connect();
        $this->matching = $matching ?? new MatchingService($this->db);
        $this->comparison = $comparison ?? new ComparisonService($this->db);
    }

    /**
     * @return list<string>
     */
    public function retailerCodes(): array
    {
        return ['fahasa', 'nhasachphuongnam', 'tiki', 'shopee'];
    }

    /**
     * @return array<string, int|string>
     */
    public function import(string $retailerCode): array
    {
        $config = $this->retailerConfig($retailerCode);
        $retailer = $this->retailerBySlug($config['slug']);
        $jobId = $this->createCrawlJob();

        $summary = [
            'retailer' => $config['slug'],
            'job_id' => $jobId,
            'processed' => 0,
            'matched' => 0,
            'created' => 0,
            'updated' => 0,
            'snapshots' => 0,
            'changes' => 0,
            'errors' => 0,
            'status' => 'running',
        ];

        try {
            $records = $this->readSourceFile($config['file']);

            foreach ($records as $index => $record) {
                $summary['processed']++;

                try {
                    $result = $this->processRecord((array) $record, (int) $retailer['id'], $jobId);
                    $summary['matched'] += $result['matched'];
                    $summary['created'] += $result['created'];
                    $summary['updated'] += $result['updated'];
                    $summary['snapshots'] += $result['snapshots'];
                    $summary['changes'] += $result['changes'];
                } catch (Throwable $exception) {
                    $summary['errors']++;
                    $this->logRowError($jobId, $index, 'row_error', $exception->getMessage(), $record);
                }
            }

            $summary['status'] = $summary['errors'] > 0 ? 'completed_with_errors' : 'completed';
        } catch (Throwable $exception) {
            $summary['status'] = 'failed';
            $summary['errors']++;
            $this->logRowError($jobId, null, 'job_error', $exception->getMessage(), null);
        }

        $this->finishCrawlJob($jobId, (string) $summary['status'], (int) $summary['processed']);

        return $summary;
    }

    /**
     * @param array<string, mixed> $record
     *
     * @return array{matched: int, created: int, updated: int, snapshots: int, changes: int}
     */
    private function processRecord(array $record, int $retailerId, int $jobId): array
    {
        $required = ['original_id', 'url'];
        foreach ($required as $field) {
            if (trim((string) ($record[$field] ?? '')) === '') {
                throw new RuntimeException("Missing required field: {$field}");
            }
        }

        $match = $this->matching->match($record);
        if ($match['book_id'] === null) {
            throw new RuntimeException('No confident book match. confidence=' . $match['confidence']);
        }

        $listedPrice = $this->nullableInt($record['listed_price'] ?? null);
        $discountedPrice = $this->nullableInt($record['discounted_price'] ?? null);
        $effectivePrice = $this->comparison->effectivePrice($listedPrice, $discountedPrice);
        $inStock = $this->booleanValue($record['in_stock'] ?? true);
        $now = date('Y-m-d H:i:s');

        $existing = $this->db->table('retailer_items')
            ->where('retailer_id', $retailerId)
            ->where('original_id', (string) $record['original_id'])
            ->get()
            ->getRowArray();

        $payload = [
            'book_id' => (int) $match['book_id'],
            'retailer_id' => $retailerId,
            'url' => (string) $record['url'],
            'original_id' => (string) $record['original_id'],
            'current_listed_price' => $listedPrice,
            'current_discounted_price' => $discountedPrice,
            'current_effective_price' => $effectivePrice,
            'in_stock' => $inStock,
            'is_active' => ((float) $match['confidence']) >= 0.7,
            'last_crawled_at' => $now,
            'updated_at' => $now,
        ];

        $created = 0;
        $updated = 0;
        $changes = 0;

        if ($existing === null) {
            $payload['created_at'] = $now;
            $this->db->table('retailer_items')->insert($payload);
            $retailerItemId = (int) $this->db->insertID();
            $created = 1;
        } else {
            $retailerItemId = (int) $existing['id'];
            $changes = $this->recordChanges($retailerItemId, $jobId, $existing, $payload);
            $this->db->table('retailer_items')->where('id', $retailerItemId)->update($payload);
            $updated = 1;
        }

        $this->db->table('price_snapshots')->insert([
            'retailer_item_id' => $retailerItemId,
            'crawl_job_id' => $jobId,
            'listed_price' => $listedPrice,
            'discounted_price' => $discountedPrice,
            'effective_price' => $effectivePrice,
            'in_stock' => $inStock,
            'captured_at' => $now,
        ]);

        return [
            'matched' => 1,
            'created' => $created,
            'updated' => $updated,
            'snapshots' => 1,
            'changes' => $changes,
        ];
    }

    /**
     * @param array<string, mixed> $existing
     * @param array<string, mixed> $payload
     */
    private function recordChanges(int $retailerItemId, int $jobId, array $existing, array $payload): int
    {
        $fields = [
            'url',
            'book_id',
            'current_listed_price',
            'current_discounted_price',
            'current_effective_price',
            'in_stock',
        ];
        $rows = [];
        $now = date('Y-m-d H:i:s');

        foreach ($fields as $field) {
            $old = $existing[$field] ?? null;
            $new = $payload[$field] ?? null;

            if ($this->changeValue($old) === $this->changeValue($new)) {
                continue;
            }

            $rows[] = [
                'retailer_item_id' => $retailerItemId,
                'crawl_job_id' => $jobId,
                'field_name' => $field,
                'old_value' => $old === null ? null : $this->changeValue($old),
                'new_value' => $new === null ? null : $this->changeValue($new),
                'detected_at' => $now,
            ];
        }

        if ($rows !== []) {
            $this->db->table('retailer_item_changes')->insertBatch($rows);
        }

        return count($rows);
    }

    /**
     * @return list<mixed>
     */
    private function readSourceFile(string $file): array
    {
        $path = WRITEPATH . 'import' . DIRECTORY_SEPARATOR . $file;
        if (! is_file($path)) {
            throw new RuntimeException("Source file not found: {$path}");
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException("Unable to read source file: {$path}");
        }

        $records = json_decode($json, true);
        if (! is_array($records)) {
            throw new RuntimeException("Invalid JSON array in source file: {$path}");
        }

        return $records;
    }

    /**
     * @return array{slug: string, file: string}
     */
    private function retailerConfig(string $retailerCode): array
    {
        $key = strtolower(trim($retailerCode));
        if (! isset(self::RETAILERS[$key])) {
            throw new RuntimeException('Unknown retailer: ' . $retailerCode);
        }

        return self::RETAILERS[$key];
    }

    /**
     * @return array<string, mixed>
     */
    private function retailerBySlug(string $slug): array
    {
        $retailer = $this->db->table('retailers')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if ($retailer === null) {
            throw new RuntimeException('Retailer not found or inactive: ' . $slug);
        }

        return $retailer;
    }

    private function createCrawlJob(): int
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('crawl_jobs')->insert([
            'status' => 'running',
            'started_at' => $now,
            'total_items_processed' => 0,
            'created_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    private function finishCrawlJob(int $jobId, string $status, int $processed): void
    {
        $this->db->table('crawl_jobs')->where('id', $jobId)->update([
            'status' => $status,
            'completed_at' => date('Y-m-d H:i:s'),
            'total_items_processed' => $processed,
        ]);
    }

    /**
     * @param mixed $raw
     */
    private function logRowError(int $jobId, ?int $index, string $type, string $message, mixed $raw): void
    {
        $this->db->table('crawl_job_errors')->insert([
            'crawl_job_id' => $jobId,
            'source_row_index' => $index,
            'error_type' => $type,
            'error_message' => $message,
            'raw_data' => $raw === null ? null : json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return max(0, (int) $value);
    }

    private function changeValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }

    private function booleanValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'in_stock', 'available'], true);
    }
}
