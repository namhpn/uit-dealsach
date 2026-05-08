<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateEmailLogsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'recipient_email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 255],
            'body_preview' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'provider_message_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'error_message' => ['type' => 'TEXT', 'null' => true],
            'sent_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('email_logs');
    }
    public function down() { $this->forge->dropTable('email_logs'); }
}
