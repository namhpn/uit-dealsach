<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class AlertService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * @return array{processed: int, sent: int, skipped: int, previous_job_id?: int, new_job_id?: int, message: string}
     */
    public function sendDailyAlerts(): array
    {
        $jobs = $this->latestCompletedSnapshotJobs();
        if (count($jobs) < 2) {
            return [
                'processed' => 0,
                'sent' => 0,
                'skipped' => 0,
                'message' => 'Chưa có đủ 2 batch snapshot thành công để so sánh.',
            ];
        }

        $newJobId = (int) $jobs[0]['id'];
        $previousJobId = (int) $jobs[1]['id'];
        $rules = $this->activeRules();
        $sent = 0;
        $skipped = 0;

        foreach ($rules as $rule) {
            $ruleId = (int) $rule['id'];
            $bookId = (int) $rule['book_id'];
            $previous = $this->lowestAvailablePrice($bookId, $previousJobId);
            $new = $this->lowestAvailablePrice($bookId, $newJobId);

            if ($previous === null || $new === null || $new >= $previous || $new > (int) $rule['target_price']) {
                $skipped++;
                continue;
            }

            if ($this->alreadyAlerted($ruleId, $previousJobId, $newJobId)) {
                $skipped++;
                continue;
            }

            $this->createAlertAndEmail($rule, $previous, $new, $previousJobId, $newJobId);
            $sent++;
        }

        return [
            'processed' => count($rules),
            'sent' => $sent,
            'skipped' => $skipped,
            'previous_job_id' => $previousJobId,
            'new_job_id' => $newJobId,
            'message' => sprintf('Đã xử lý %d theo dõi giá, gửi %d cảnh báo, bỏ qua %d.', count($rules), $sent, $skipped),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function latestCompletedSnapshotJobs(): array
    {
        $fullBatches = $this->db->table('crawl_jobs')
            ->select('crawl_jobs.id, crawl_jobs.completed_at, crawl_jobs.created_at')
            ->join('price_snapshots', 'price_snapshots.crawl_job_id = crawl_jobs.id')
            ->where('crawl_jobs.status', 'completed')
            ->groupBy('crawl_jobs.id')
            ->having('COUNT(price_snapshots.id) >=', 2)
            ->orderBy('crawl_jobs.completed_at', 'DESC')
            ->orderBy('crawl_jobs.id', 'DESC')
            ->limit(2)
            ->get()
            ->getResultArray();

        if (count($fullBatches) >= 2) {
            return $fullBatches;
        }

        return $this->db->table('crawl_jobs')
            ->select('crawl_jobs.id, crawl_jobs.completed_at, crawl_jobs.created_at')
            ->join('price_snapshots', 'price_snapshots.crawl_job_id = crawl_jobs.id')
            ->where('crawl_jobs.status', 'completed')
            ->groupBy('crawl_jobs.id')
            ->orderBy('crawl_jobs.completed_at', 'DESC')
            ->orderBy('crawl_jobs.id', 'DESC')
            ->limit(2)
            ->get()
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function activeRules(): array
    {
        return $this->db->table('tracking_rules')
            ->select([
                'tracking_rules.id',
                'tracking_rules.book_id',
                'tracking_rules.email',
                'tracking_rules.target_price',
                'books.title AS book_title',
                'books.slug AS book_slug',
            ])
            ->join('books', 'books.id = tracking_rules.book_id')
            ->where('tracking_rules.is_active', 1)
            ->where('books.is_active', 1)
            ->where('books.deleted_at', null)
            ->orderBy('tracking_rules.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function lowestAvailablePrice(int $bookId, int $crawlJobId): ?int
    {
        $value = $this->db->table('price_snapshots')
            ->selectMin('price_snapshots.effective_price', 'lowest_price')
            ->join('retailer_items', 'retailer_items.id = price_snapshots.retailer_item_id')
            ->where('retailer_items.book_id', $bookId)
            ->where('retailer_items.is_active', 1)
            ->where('price_snapshots.crawl_job_id', $crawlJobId)
            ->where('price_snapshots.in_stock', 1)
            ->where('price_snapshots.effective_price IS NOT NULL', null, false)
            ->get()
            ->getRow('lowest_price');

        return $value === null ? null : (int) $value;
    }

    private function alreadyAlerted(int $ruleId, int $previousJobId, int $newJobId): bool
    {
        return $this->db->table('alert_events')
            ->where('tracking_rule_id', $ruleId)
            ->where('previous_crawl_job_id', $previousJobId)
            ->where('new_crawl_job_id', $newJobId)
            ->countAllResults() > 0;
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function createAlertAndEmail(array $rule, int $previous, int $new, int $previousJobId, int $newJobId): void
    {
        helper('currency');

        $now = date('Y-m-d H:i:s');
        $detailUrl = site_url('sach/' . $rule['book_slug']);
        $subject = 'DealSach báo giảm giá: ' . $rule['book_title'];
        $body = sprintf(
            '%s đã giảm từ %s xuống %s. Xem chi tiết: %s',
            (string) $rule['book_title'],
            format_vnd($previous),
            format_vnd($new),
            $detailUrl
        );

        $this->db->table('alert_events')->insert([
            'tracking_rule_id' => (int) $rule['id'],
            'previous_crawl_job_id' => $previousJobId,
            'new_crawl_job_id' => $newJobId,
            'previous_price' => $previous,
            'new_price' => $new,
            'status' => 'sent',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->db->table('email_logs')->insert([
            'recipient_email' => (string) $rule['email'],
            'email_type' => 'alert',
            'subject' => $subject,
            'body_preview' => $body,
            'status' => 'sent',
            'provider_message_id' => 'demo-alert-' . (int) $rule['id'] . '-' . $newJobId,
            'error_message' => null,
            'sent_at' => $now,
            'created_at' => $now,
        ]);
    }
}
