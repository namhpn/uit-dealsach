<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreatePriceSnapshotsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'retailer_item_id' => ['type' => 'INT', 'unsigned' => true],
            'crawl_job_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'listed_price' => ['type' => 'INT', 'null' => true],
            'discounted_price' => ['type' => 'INT', 'null' => true],
            'effective_price' => ['type' => 'INT', 'null' => true],
            'in_stock' => ['type' => 'BOOLEAN', 'default' => true],
            'captured_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('retailer_item_id', 'retailer_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('crawl_job_id', 'crawl_jobs', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('price_snapshots');
    }
    public function down() { $this->forge->dropTable('price_snapshots'); }
}
