<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Mahasiswa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'nim'        => ['type' => 'VARCHAR', 'constraint' => 9],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'kelas'      => ['type' => 'VARCHAR', 'constraint' => 10],
            'prodi'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'jurusan'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('nim', true);
        $this->forge->createTable('mahasiswa');
    }

    public function down()
    {
        $this->forge->dropTable('mahasiswa');
    }
}
