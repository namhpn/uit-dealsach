<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreatePublishersTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'website_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'logo_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'country' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('publishers');
    }
    public function down() { $this->forge->dropTable('publishers'); }
}
