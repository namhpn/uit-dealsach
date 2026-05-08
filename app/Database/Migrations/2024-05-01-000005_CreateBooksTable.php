<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateBooksTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'isbn' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'format' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'language' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Tiếng Việt'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'publisher_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'cover_image_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['title', 'isbn']);
        $this->forge->addForeignKey('publisher_id', 'publishers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('books');
    }
    public function down() { $this->forge->dropTable('books'); }
}
