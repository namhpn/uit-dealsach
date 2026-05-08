<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateCrawlJobErrorsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'crawl_job_id' => ['type' => 'INT', 'unsigned' => true],
            'source_row_index' => ['type' => 'INT', 'null' => true],
            'error_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'error_message' => ['type' => 'TEXT'],
            'raw_data' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('crawl_job_id', 'crawl_jobs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('crawl_job_errors');
    }
    public function down() { $this->forge->dropTable('crawl_job_errors'); }
}
