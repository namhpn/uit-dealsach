<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateRetailerItemsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'book_id' => ['type' => 'INT', 'unsigned' => true],
            'retailer_id' => ['type' => 'INT', 'unsigned' => true],
            'url' => ['type' => 'TEXT'],
            'original_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'current_listed_price' => ['type' => 'INT', 'null' => true],
            'current_discounted_price' => ['type' => 'INT', 'null' => true],
            'current_effective_price' => ['type' => 'INT', 'null' => true],
            'in_stock' => ['type' => 'BOOLEAN', 'default' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'last_crawled_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['book_id', 'retailer_id']);
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('retailer_id', 'retailers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('retailer_items');
    }
    public function down() { $this->forge->dropTable('retailer_items'); }
}
