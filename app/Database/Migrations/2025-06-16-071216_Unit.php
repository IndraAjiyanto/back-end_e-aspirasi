<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Unit extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
	        'user_id' =>  ['type'=> 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'=> ['type' => 'DATETIME', 'null' => true],
            'updated_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
	$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('unit');
    }

    public function down()
    {
        $this->forge->dropTable('unit');
    }
}