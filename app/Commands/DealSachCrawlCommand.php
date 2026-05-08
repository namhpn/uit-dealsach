<?php

namespace App\Commands;

use App\Services\CrawlImportService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DealSachCrawlCommand extends BaseCommand
{
    protected $group = 'DealSach';
    protected $name = 'dealsach:crawl';
    protected $description = 'Import retailer JSON data and create price snapshots.';
    protected $usage = 'dealsach:crawl [retailer|all]';
    protected $arguments = [
        'retailer' => 'One of: fahasa, nhasachphuongnam, phuongnam, tiki, shopee, all.',
    ];

    public function run(array $params)
    {
        $target = strtolower((string) ($params[0] ?? 'all'));
        $service = new CrawlImportService();
        $retailers = $target === 'all' ? $service->retailerCodes() : [$target];
        $exitCode = 0;

        CLI::write('DealSach import started: ' . implode(', ', $retailers), 'green');

        foreach ($retailers as $retailer) {
            try {
                $summary = $service->import($retailer);
                CLI::write(sprintf(
                    '[%s] job=%s status=%s processed=%d matched=%d created=%d updated=%d snapshots=%d changes=%d errors=%d',
                    $summary['retailer'],
                    $summary['job_id'],
                    $summary['status'],
                    $summary['processed'],
                    $summary['matched'],
                    $summary['created'],
                    $summary['updated'],
                    $summary['snapshots'],
                    $summary['changes'],
                    $summary['errors'],
                ), ((int) $summary['errors'] > 0 || $summary['status'] === 'failed') ? 'yellow' : 'green');

                if ($summary['status'] === 'failed') {
                    $exitCode = 1;
                }
            } catch (\Throwable $exception) {
                CLI::error("[{$retailer}] " . $exception->getMessage());
                $exitCode = 1;
            }
        }

        CLI::write('DealSach import finished.', $exitCode === 0 ? 'green' : 'yellow');

        return $exitCode;
    }
}
