<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();

        $metrics = [
            'books' => (int) $db->table('books')->where('deleted_at', null)->countAllResults(),
            'retailers' => (int) $db->table('retailers')->where('is_active', 1)->countAllResults(),
            'offers' => (int) $db->table('retailer_items')->where('is_active', 1)->countAllResults(),
            'trackingRules' => (int) $db->table('tracking_rules')->where('is_active', 1)->countAllResults(),
            'clicks' => (int) $db->table('outbound_clicks')->countAllResults(),
            'alertsToday' => (int) $db->table('alert_events')->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'failedJobs' => (int) $db->table('crawl_jobs')->where('status', 'failed')->where('created_at >=', date('Y-m-d H:i:s', time() - 86400))->countAllResults(),
            'recentSignIns' => (int) $db->table('admin_auth_logs')->where('status', 'success')->where('created_at >=', date('Y-m-d H:i:s', time() - 86400))->countAllResults(),
        ];

        $recentCrawls = $db->table('crawl_jobs')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('admin/dashboard', [
            'pageTitle' => 'Bảng điều khiển',
            'metrics' => $metrics,
            'recentCrawls' => $recentCrawls,
        ]);
    }
}
