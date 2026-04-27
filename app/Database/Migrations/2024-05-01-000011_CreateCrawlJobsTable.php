<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateCrawlJobsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'started_at' => ['type' => 'DATETIME', 'null' => true],
            'completed_at' => ['type' => 'DATETIME', 'null' => true],
            'total_items_processed' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('crawl_jobs');
    }
    public function down() { $this->forge->dropTable('crawl_jobs'); }
}
