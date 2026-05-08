<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class DemoSeeder extends Seeder {
    public function run() {
        $this->call('AdminUserSeeder');
        $this->call('RetailerSeeder');
        $this->call('PublisherSeeder');
        $this->call('CategorySeeder');
        $this->call('BookSeeder');
        $this->call('RetailerItemSeeder');
    }
}
