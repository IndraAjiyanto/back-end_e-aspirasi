<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Models\GroupModel;
use Myth\Auth\Password;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel  = new UserModel();
        $groupModel = new GroupModel();

        // Insert grup jika belum ada
        $this->db->table('auth_groups')->ignore(true)->insertBatch([
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'mahasiswa', 'description' => 'Mahasiswa'],
        ]);

        // Ambil ID grup dari nama
        $adminGroupId = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow()->id;
        $mhsGroupId   = $this->db->table('auth_groups')->where('name', 'mahasiswa')->get()->getRow()->id;

        // Insert mahasiswa
        $this->db->table('mahasiswa')->insertBatch([
            [
                'nim'    => '230001',
                'nama'   => 'Mawar',
                'kelas'  => 'TI-1A',
                'prodi'  => 'Teknik Informatika',
                'jurusan'=> 'Informatika',
            ],
            [
                'nim'    => '230002',
                'nama'   => 'Budi',
                'kelas'  => 'TI-1B',
                'prodi'  => 'Teknik Informatika',
                'jurusan'=> 'Informatika',
            ]
        ]);

        // Ambil data mahasiswa
        $mawar = $this->db->table('mahasiswa')->where('nim', '230001')->get()->getRow();
        $budi  = $this->db->table('mahasiswa')->where('nim', '230002')->get()->getRow();

        // Insert user Admin (tanpa mahasiswa_id)
        $userModel->insert([
            'username'      => 'admin',
            'email'         => 'admin@example.com',
            'password_hash' => Password::hash('12345678'),
            'active'        => 1
        ]);
        $adminID = $userModel->getInsertID();
        $groupModel->addUserToGroup($adminID, $adminGroupId);

        // Insert Mahasiswa 1 (dengan mahasiswa_id)
        $userModel->insert([
            'username'      => 'mawar01',
            'email'         => 'mawar01@example.com',
            'password_hash' => Password::hash('12345678'),
            'active'        => 1,
            'mahasiswa_id'  => $mawar->id,
        ]);
        $mhs1ID = $userModel->getInsertID();
        $groupModel->addUserToGroup($mhs1ID, $mhsGroupId);

        // Insert Mahasiswa 2 (dengan mahasiswa_id)
        $userModel->insert([
            'username'      => 'budi02',
            'email'         => 'budi02@example.com',
            'password_hash' => Password::hash('12345678'),
            'active'        => 1,
            'mahasiswa_id'  => $budi->id,
        ]);
        $mhs2ID = $userModel->getInsertID();
        $groupModel->addUserToGroup($mhs2ID, $mhsGroupId);

        echo "Seeder berhasil dijalankan! 🎉\n";
    }
}
