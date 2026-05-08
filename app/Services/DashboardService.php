<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class DashboardService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * @return array<string, mixed>
     */
    public function metrics(): array
    {
        $latestCrawl = $this->db->table('crawl_jobs')
            ->select('completed_at')
            ->whereIn('status', ['completed', 'completed_with_errors'])
            ->orderBy('completed_at', 'DESC')
            ->get(1)
            ->getRow('completed_at');

        return [
            'books' => $this->db->table('books')->where('deleted_at', null)->where('is_active', 1)->countAllResults(),
            'retailers' => $this->db->table('retailers')->where('is_active', 1)->countAllResults(),
            'latestCrawl' => $latestCrawl ?: 'Chưa có',
            'trackingRules' => $this->db->table('tracking_rules')->where('is_active', 1)->countAllResults(),
            'alerts' => $this->db->table('alert_events')->countAllResults(),
            'clicks' => $this->db->table('outbound_clicks')->countAllResults(),
            'failedJobs' => $this->db->table('crawl_jobs')->where('status', 'failed')->countAllResults(),
            'snapshots' => $this->db->table('price_snapshots')->countAllResults(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recentCrawls(int $limit = 8): array
    {
        return $this->db->table('crawl_jobs')
            ->select('id, status, started_at, completed_at, total_items_processed')
            ->orderBy('id', 'DESC')
            ->get($limit)
            ->getResultArray();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recentAuthLogs(int $limit = 8): array
    {
        return $this->db->table('admin_auth_logs')
            ->select('admin_auth_logs.*, admin_users.username')
            ->join('admin_users', 'admin_users.id = admin_auth_logs.admin_id', 'left')
            ->orderBy('admin_auth_logs.id', 'DESC')
            ->get($limit)
            ->getResultArray();
    }
}
