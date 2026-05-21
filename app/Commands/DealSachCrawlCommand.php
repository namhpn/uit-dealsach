<?php

namespace App\Commands;

use App\Services\ImportService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DealSachCrawlCommand extends BaseCommand
{
    protected $group = 'DealSach';
    protected $name = 'dealsach:crawl';
    protected $description = 'Import retailer JSON data into DealSach comparison tables.';
    protected $usage = 'dealsach:crawl [fahasa|phuongnam|nhasachphuongnam|tiki|shopee|all]';

    public function run(array $params)
    {
        $target = strtolower((string) ($params[0] ?? 'all'));
        $retailers = match ($target) {
            'all' => ['fahasa', 'nhasachphuongnam', 'tiki', 'shopee'],
            'phuongnam' => ['nhasachphuongnam'],
            'fahasa', 'nhasachphuongnam', 'tiki', 'shopee' => [$target],
            default => null,
        };

        if ($retailers === null) {
            CLI::error('Nhà bán không hợp lệ. Dùng: fahasa, phuongnam, tiki, shopee hoặc all.');
            return EXIT_ERROR;
        }

        $service = new ImportService();
        $totalErrors = 0;

        foreach ($retailers as $retailer) {
            try {
                $stats = $service->importRetailer($retailer);
                $totalErrors += (int) $stats['errors'];
                CLI::write(sprintf(
                    '%s: job #%d, processed=%d, created=%d, updated=%d, snapshots=%d, changes=%d, errors=%d',
                    $stats['retailer'],
                    $stats['job_id'],
                    $stats['processed'],
                    $stats['created'],
                    $stats['updated'],
                    $stats['snapshots'],
                    $stats['changes'],
                    $stats['errors']
                ), 'green');
            } catch (\Throwable $exception) {
                $totalErrors++;
                CLI::error($retailer . ': ' . $exception->getMessage());
            }
        }

        return $totalErrors > 0 ? EXIT_ERROR : EXIT_SUCCESS;
    }
}
