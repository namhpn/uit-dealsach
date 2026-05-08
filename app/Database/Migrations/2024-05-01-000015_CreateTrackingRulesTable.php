<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateTrackingRulesTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'book_id' => ['type' => 'INT', 'unsigned' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'target_price' => ['type' => 'INT'],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['book_id', 'email'], false); // to help with duplicates
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tracking_rules');
    }
    public function down() { $this->forge->dropTable('tracking_rules'); }
}
