<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $tables = [
            'outbound_clicks',
            'email_logs',
            'alert_events',
            'otp_requests',
            'tracking_rules',
            'crawl_job_errors',
            'retailer_item_changes',
            'price_snapshots',
            'crawl_jobs',
            'retailer_items',
            'retailers',
            'book_authors',
            'authors',
            'book_categories',
            'books',
            'categories',
            'publishers',
            'admin_auth_logs',
            'admin_users',
        ];

        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            $this->db->table($table)->truncate();
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        $this->call('AdminUserSeeder');
        $this->call('RetailerSeeder');
        $this->call('PublisherSeeder');
        $this->call('CategorySeeder');
        $this->call('BookSeeder');
        $this->call('RetailerItemSeeder');
        $this->call('AlertScenarioSeeder');
    }
}
