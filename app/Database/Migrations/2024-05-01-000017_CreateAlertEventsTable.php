<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateAlertEventsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tracking_rule_id' => ['type' => 'INT', 'unsigned' => true],
            'previous_price' => ['type' => 'INT', 'null' => true],
            'new_price' => ['type' => 'INT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'queued'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tracking_rule_id', 'tracking_rules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('alert_events');
    }
    public function down() { $this->forge->dropTable('alert_events'); }
}
