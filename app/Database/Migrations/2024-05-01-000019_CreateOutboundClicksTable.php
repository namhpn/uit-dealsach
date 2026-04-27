<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateOutboundClicksTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'retailer_item_id' => ['type' => 'INT', 'unsigned' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'clicked_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('retailer_item_id', 'retailer_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('outbound_clicks');
    }
    public function down() { $this->forge->dropTable('outbound_clicks'); }
}
