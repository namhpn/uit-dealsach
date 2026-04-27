<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateBookAuthorsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'book_id' => ['type' => 'INT', 'unsigned' => true],
            'author_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addKey(['book_id', 'author_id'], true);
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('author_id', 'authors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('book_authors');
    }
    public function down() { $this->forge->dropTable('book_authors'); }
}
