<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateOtpRequestsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'otp_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'pending'],
            'attempt_count' => ['type' => 'INT', 'default' => 0],
            'resend_count' => ['type' => 'INT', 'default' => 0],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('otp_requests');
    }
    public function down() { $this->forge->dropTable('otp_requests'); }
}
