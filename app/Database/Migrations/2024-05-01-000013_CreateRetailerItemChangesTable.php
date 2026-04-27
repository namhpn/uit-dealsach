<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateRetailerItemChangesTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'retailer_item_id' => ['type' => 'INT', 'unsigned' => true],
            'crawl_job_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'field_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'old_value' => ['type' => 'TEXT', 'null' => true],
            'new_value' => ['type' => 'TEXT', 'null' => true],
            'detected_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('retailer_item_id', 'retailer_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('crawl_job_id', 'crawl_jobs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('retailer_item_changes');
    }
    public function down() { $this->forge->dropTable('retailer_item_changes'); }
}
