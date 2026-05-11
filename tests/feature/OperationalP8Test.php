<?php

use App\Services\AlertService;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

/**
 * P8 operational checks against the running Docker/nginx app and default DB.
 *
 * @internal
 */
final class OperationalP8Test extends CIUnitTestCase
{
    public function testAlertJobDeduplicatesSnapshotPairAndLogsEmail(): void
    {
        $db = Database::connect('default');
        $first = (new AlertService($db))->sendDailyAlerts();
        $afterFirstCount = (int) $db->table('alert_events')->countAllResults();

        $second = (new AlertService($db))->sendDailyAlerts();
        $afterSecondCount = (int) $db->table('alert_events')->countAllResults();

        $this->assertGreaterThan(0, $first['processed']);
        $this->assertArrayHasKey('previous_job_id', $first);
        $this->assertArrayHasKey('new_job_id', $first);
        $this->assertSame(0, $second['sent']);
        $this->assertSame($afterFirstCount, $afterSecondCount);

        $latestEmail = $db->table('email_logs')
            ->where('email_type', 'alert')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $this->assertNotNull($latestEmail);
        $this->assertStringContainsString('DealSach', (string) $latestEmail['subject']);
        $this->assertStringContainsString('http', (string) $latestEmail['body_preview']);
    }

    public function testRedirectUsesStoredRetailerUrlAndLogsClick(): void
    {
        $db = Database::connect('default');
        $item = $db->table('retailer_items')
            ->select('id, url')
            ->where('is_active', 1)
            ->where('url !=', '')
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        $this->assertNotNull($item);

        $before = (int) $db->table('outbound_clicks')->countAllResults();
        $response = $this->httpGet('/go/' . (int) $item['id']);
        $after = (int) $db->table('outbound_clicks')->countAllResults();

        $this->assertSame(302, $response['status']);
        $this->assertSame((string) $item['url'], $response['location']);
        $this->assertSame($before + 1, $after);

        $latestClick = $db->table('outbound_clicks')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $this->assertSame((int) $item['id'], (int) $latestClick['retailer_item_id']);
        $this->assertSame(64, strlen((string) $latestClick['ip_hash']));
    }

    /**
     * @return array{status: int, location: string, body: string}
     */
    private function httpGet(string $path): array
    {
        $baseUrl = rtrim((string) (getenv('FRONTEND_TEST_BASE_URL') ?: 'http://nginx'), '/');
        $handle = curl_init($baseUrl . $path);
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 15,
        ]);

        $raw = (string) curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $error = curl_error($handle);
        curl_close($handle);

        $this->assertSame('', $error, 'HTTP request failed: ' . $error);

        $headers = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);
        preg_match('/^Location:\s*(.+)$/mi', $headers, $matches);

        return [
            'status' => $status,
            'location' => isset($matches[1]) ? trim($matches[1]) : '',
            'body' => $body,
        ];
    }
}
