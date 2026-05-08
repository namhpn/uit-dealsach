<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class ReportService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function booksCsv(): string
    {
        $rows = $this->db->table('books')
            ->select([
                'books.title',
                'books.isbn',
                'publishers.name AS publisher',
                'books.format',
                'books.language',
                'books.is_active',
                'COUNT(DISTINCT retailer_items.id) AS retailer_count',
                'MIN(CASE WHEN retailer_items.in_stock = 1 THEN retailer_items.current_effective_price END) AS lowest_price',
                'MAX(retailer_items.current_effective_price) AS highest_price',
            ])
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->join('retailer_items', 'retailer_items.book_id = books.id AND retailer_items.is_active = 1', 'left')
            ->where('books.deleted_at', null)
            ->groupBy('books.id')
            ->orderBy('books.title', 'ASC')
            ->get()
            ->getResultArray();

        return $this->toCsv([
            'title',
            'isbn',
            'publisher',
            'format',
            'language',
            'status',
            'retailer_count',
            'lowest_price',
            'highest_price',
        ], array_map(static function (array $row): array {
            $row['status'] = (int) $row['is_active'] === 1 ? 'active' : 'inactive';
            unset($row['is_active']);

            return $row;
        }, $rows));
    }

    public function activityCsv(): string
    {
        $date = date('Y-m-d');
        $row = [
            'date' => $date,
            'crawl_count' => $this->db->table('crawl_jobs')->like('created_at', $date, 'after')->countAllResults(),
            'alert_count' => $this->db->table('alert_events')->like('created_at', $date, 'after')->countAllResults(),
            'click_count' => $this->db->table('outbound_clicks')->like('clicked_at', $date, 'after')->countAllResults(),
            'tracking_rule_count' => $this->db->table('tracking_rules')->where('is_active', 1)->countAllResults(),
        ];

        return $this->toCsv(array_keys($row), [$row]);
    }

    /**
     * @param list<string> $headers
     * @param list<array<string, mixed>> $rows
     */
    private function toCsv(array $headers, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $header) {
                $line[] = $row[$header] ?? '';
            }
            fputcsv($handle, $line);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv === false ? '' : $csv;
    }
}
