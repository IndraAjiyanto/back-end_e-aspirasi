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

        // 1. Insert grup jika belum ada
        $this->db->table('auth_groups')->ignore(true)->insertBatch([
            ['name' => 'admin',     'description' => 'Administrator'],
            ['name' => 'akademik',  'description' => 'Akademik'],
            ['name' => 'ppks',      'description' => 'PPKS'],
            ['name' => 'sarpras',   'description' => 'Sarana & Prasarana'],
            ['name' => 'mahasiswa', 'description' => 'Mahasiswa'],
        ]);

        // 2. Ambil ID grup
        $adminGroupId    = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow()->id;
        $akademikGroupId = $this->db->table('auth_groups')->where('name', 'akademik')->get()->getRow()->id;
        $ppksGroupId     = $this->db->table('auth_groups')->where('name', 'ppks')->get()->getRow()->id;
        $sarprasGroupId  = $this->db->table('auth_groups')->where('name', 'sarpras')->get()->getRow()->id;
        $mhsGroupId      = $this->db->table('auth_groups')->where('name', 'mahasiswa')->get()->getRow()->id;

        // 3. Insert akun unit
        $unitAccounts = [
            ['username' => 'admin',     'email' => 'admin@example.com',     'group' => $adminGroupId],
            ['username' => 'akademik',  'email' => 'akademik@example.com',  'group' => $akademikGroupId],
            ['username' => 'ppks',      'email' => 'ppks@example.com',      'group' => $ppksGroupId],
            ['username' => 'sarpras',   'email' => 'sarpras@example.com',   'group' => $sarprasGroupId],
        ];

        foreach ($unitAccounts as $account) {
            $userModel->insert([
                'username'      => $account['username'],
                'email'         => $account['email'],
                'password_hash' => Password::hash('12345678'),
                'active'        => 1
            ]);
            $groupModel->addUserToGroup($userModel->getInsertID(), $account['group']);
        }

        // 4. Insert akun Mahasiswa 1: Mawar
        $userModel->insert([
            'username'      => 'mawar01',
            'email'         => 'mawar01@example.com',
            'password_hash' => Password::hash('12345678'),
            'active'        => 1
        ]);
        $mawarUserID = $userModel->getInsertID();
        $groupModel->addUserToGroup($mawarUserID, $mhsGroupId);

        $this->db->table('mahasiswa')->insert([
            'user_id' => $mawarUserID,
            'nim'     => '230001',
            'nama'    => 'Mawar',
            'kelas'   => 'TI-1A',
            'prodi'   => 'Teknik Informatika',
            'jurusan' => 'Informatika',
        ]);

        // 5. Insert akun Mahasiswa 2: Budi
        $userModel->insert([
            'username'      => 'budi02',
            'email'         => 'budi02@example.com',
            'password_hash' => Password::hash('12345678'),
            'active'        => 1
        ]);
        $budiUserID = $userModel->getInsertID();
        $groupModel->addUserToGroup($budiUserID, $mhsGroupId);

        $this->db->table('mahasiswa')->insert([
            'user_id' => $budiUserID,
            'nim'     => '230002',
            'nama'    => 'Budi',
            'kelas'   => 'TI-1B',
            'prodi'   => 'Teknik Informatika',
            'jurusan' => 'Informatika',
        ]);

        echo "Seeder berhasil dijalankan! 🎉\n";
    }
}
