<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Aspirasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mahasiswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'isi'      => ['type' => 'VARCHAR', 'constraint' => 250],
            'unit_id'      => ['type' => 'INT', 'unsigned' => true],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'=> ['type' => 'DATETIME', 'null' => true],
            'updated_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('aspirasi');
    }

    public function down()
    {
        $this->forge->dropTable('aspirasi');
    }
}