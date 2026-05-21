<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOperationalColumnsForP8 extends Migration
{
    public function up()
    {
        $this->forge->addColumn('alert_events', [
            'previous_crawl_job_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'tracking_rule_id'],
            'new_crawl_job_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'previous_crawl_job_id'],
        ]);

        $this->forge->addColumn('outbound_clicks', [
            'book_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'retailer_item_id'],
            'referrer' => ['type' => 'TEXT', 'null' => true, 'after' => 'user_agent'],
            'ip_hash' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true, 'after' => 'ip_address'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('outbound_clicks', ['book_id', 'referrer', 'ip_hash']);
        $this->forge->dropColumn('alert_events', ['previous_crawl_job_id', 'new_crawl_job_id']);
    }
}
