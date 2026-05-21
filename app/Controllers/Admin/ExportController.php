<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class ExportController extends BaseController
{
    public function booksCsv()
    {
        $rows = Database::connect()->table('books')
            ->select('books.id, books.title, books.slug, books.isbn, publishers.name AS publisher, books.is_active, books.updated_at')
            ->join('publishers', 'publishers.id = books.publisher_id', 'left')
            ->where('books.deleted_at', null)
            ->orderBy('books.id', 'ASC')
            ->get()
            ->getResultArray();

        return $this->csv('dealsach-books.csv', ['id', 'title', 'slug', 'isbn', 'publisher', 'is_active', 'updated_at'], $rows);
    }

    public function activityCsv()
    {
        $rows = Database::connect()->table('crawl_jobs')
            ->select('id, status, started_at, completed_at, total_items_processed, created_at')
            ->orderBy('id', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        return $this->csv('dealsach-activity.csv', ['id', 'status', 'started_at', 'completed_at', 'total_items_processed', 'created_at'], $rows);
    }

    /**
     * @param list<string> $headers
     * @param list<array<string, mixed>> $rows
     */
    private function csv(string $filename, array $headers, array $rows)
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, array_map(static fn ($key) => $row[$key] ?? '', $headers));
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody("\xEF\xBB\xBF" . $content);
    }
}
