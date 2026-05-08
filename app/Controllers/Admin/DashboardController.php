<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\DashboardService;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $dashboard = new DashboardService();

        return view('admin/dashboard', [
            'pageTitle' => 'Bảng điều khiển',
            'metrics' => $dashboard->metrics(),
            'recentCrawls' => $dashboard->recentCrawls(),
            'recentAuthLogs' => $dashboard->recentAuthLogs(),
        ]);
    }
}
