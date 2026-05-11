<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlertScenarioSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $rules = [];
        for ($bookId = 1; $bookId <= 5; $bookId++) {
            $rules[] = [
                'book_id' => $bookId,
                'email' => 'demo' . $bookId . '@example.com',
                'target_price' => 120000,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db->table('tracking_rules')->insertBatch($rules);

        $alerts = [];
        $emails = [];
        for ($ruleId = 1; $ruleId <= 5; $ruleId++) {
            $alerts[] = [
                'tracking_rule_id' => $ruleId,
                'previous_price' => 130000 + ($ruleId * 1000),
                'new_price' => 94000 + ($ruleId * 1500),
                'status' => $ruleId <= 3 ? 'sent' : 'queued',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $emails[] = [
                'recipient_email' => 'demo' . $ruleId . '@example.com',
                'email_type' => 'alert',
                'subject' => 'DealSach báo giảm giá sách',
                'body_preview' => 'Sách bạn theo dõi đã giảm xuống dưới giá mục tiêu.',
                'status' => $ruleId <= 3 ? 'sent' : 'queued',
                'provider_message_id' => null,
                'error_message' => null,
                'sent_at' => $ruleId <= 3 ? $now : null,
                'created_at' => $now,
            ];
        }

        $this->db->table('alert_events')->insertBatch($alerts);
        $this->db->table('email_logs')->insertBatch($emails);
    }
}
