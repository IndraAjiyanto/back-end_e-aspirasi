<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Jawaban extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'isi'      => ['type' => 'VARCHAR', 'constraint' => 250],
            'aspirasi_id'      => ['type' => 'INT', 'unsigned' => true],
            'created_at'=> ['type' => 'DATETIME', 'null' => true],
            'updated_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('aspirasi_id', 'aspirasi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jawaban');
    }

    public function down()
    {
        $this->forge->dropTable('jawaban');
    }
}
