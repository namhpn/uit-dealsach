<?php

namespace App\Commands;

use App\Services\AlertService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DealSachAlertsCommand extends BaseCommand
{
    protected $group = 'DealSach';
    protected $name = 'dealsach:alerts';
    protected $description = 'Evaluate active tracking rules and send simulated price-drop alerts.';
    protected $usage = 'dealsach:alerts';

    public function run(array $params)
    {
        $stats = (new AlertService())->sendDailyAlerts();

        CLI::write('DealSach alert job', 'yellow');
        if (isset($stats['previous_job_id'], $stats['new_job_id'])) {
            CLI::write(sprintf('Snapshot batches: #%d -> #%d', $stats['previous_job_id'], $stats['new_job_id']));
        }

        CLI::write(sprintf('Processed: %d', $stats['processed']));
        CLI::write(sprintf('Sent: %d', $stats['sent']), $stats['sent'] > 0 ? 'green' : 'light_gray');
        CLI::write(sprintf('Skipped: %d', $stats['skipped']));
        CLI::write($stats['message']);

        return EXIT_SUCCESS;
    }
}
