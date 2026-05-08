<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ReportService;

class ExportController extends BaseController
{
    public function booksCsv()
    {
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="dealsach-books.csv"')
            ->setBody("\xEF\xBB\xBF" . (new ReportService())->booksCsv());
    }

    public function activityCsv()
    {
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="dealsach-activity.csv"')
            ->setBody("\xEF\xBB\xBF" . (new ReportService())->activityCsv());
    }
}
