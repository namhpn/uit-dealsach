<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateCategoriesTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'parent_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('categories');
    }
    public function down() { $this->forge->dropTable('categories'); }
}
