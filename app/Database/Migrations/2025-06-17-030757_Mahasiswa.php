<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Mahasiswa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'user_id' =>  ['type'=> 'INT', 'constraint' => 11, 'unsigned' => true],
            'nim'        => ['type' => 'VARCHAR', 'constraint' => 9],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'kelas'      => ['type' => 'VARCHAR', 'constraint' => 10],
            'prodi'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'jurusan'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
	    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey('nim');
        $this->forge->createTable('mahasiswa');
    }

    public function down()
    {
        $this->forge->dropTable('mahasiswa');
    }
}