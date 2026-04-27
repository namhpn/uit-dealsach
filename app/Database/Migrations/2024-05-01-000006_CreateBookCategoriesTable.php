<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateBookCategoriesTable extends Migration {
    public function up() {
        $this->forge->addField([
            'book_id' => ['type' => 'INT', 'unsigned' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true],
            'is_primary' => ['type' => 'BOOLEAN', 'default' => false],
        ]);
        $this->forge->addKey(['book_id', 'category_id'], true);
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('book_categories');
    }
    public function down() { $this->forge->dropTable('book_categories'); }
}
