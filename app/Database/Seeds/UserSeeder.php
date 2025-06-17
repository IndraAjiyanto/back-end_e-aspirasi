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

    $defaultPassword = Password::hash('12345678');

    $groups = [
        'akademik' => null,
        'ppks'     => null,
        'sarpras'  => null,
        'mahasiswa'=> null,
    ];

    // Ambil semua ID grup
    foreach ($groups as $name => &$id) {
        $group = $this->db->table('auth_groups')->where('name', $name)->get()->getRow();
        if (!$group) throw new \Exception("Grup '$name' tidak ditemukan!");
        $id = $group->id;
    }

    // Insert unit users
    $unitAccounts = [
        ['username' => 'akademik', 'email' => 'akademik@example.com', 'group' => $groups['akademik']],
        ['username' => 'ppks',     'email' => 'ppks@example.com',     'group' => $groups['ppks']],
        ['username' => 'sarpras',  'email' => 'sarpras@example.com',  'group' => $groups['sarpras']],
    ];

    foreach ($unitAccounts as $account) {
        $userModel->insert([
            'username'      => $account['username'],
            'email'         => $account['email'],
            'password_hash' => $defaultPassword,
            'active'        => 1
        ]);
        $groupModel->addUserToGroup($userModel->getInsertID(), $account['group']);
    }

    // Mahasiswa
    $mahasiswaList = [
        [
            'username' => 'mawar01',
            'email'    => 'mawar01@example.com',
            'mahasiswa'=> [
                'nim'     => '230001',
                'nama'    => 'Mawar',
                'kelas'   => 'TI-1A',
                'prodi'   => 'Teknik Informatika',
                'jurusan' => 'Informatika',
            ]
        ],
        [
            'username' => 'budi02',
            'email'    => 'budi02@example.com',
            'mahasiswa'=> [
                'nim'     => '230002',
                'nama'    => 'Budi',
                'kelas'   => 'TI-1B',
                'prodi'   => 'Teknik Informatika',
                'jurusan' => 'Informatika',
            ]
        ]
    ];

    foreach ($mahasiswaList as $mhs) {
        $userModel->insert([
            'username'      => $mhs['username'],
            'email'         => $mhs['email'],
            'password_hash' => $defaultPassword,
            'active'        => 1
        ]);

        $userID = $userModel->getInsertID();
        $groupModel->addUserToGroup($userID, $groups['mahasiswa']);

        $mhsData = $mhs['mahasiswa'];
        $mhsData['user_id'] = $userID;

        $this->db->table('mahasiswa')->insert($mhsData);
    }

    echo "Seeder berhasil dijalankan! 🎉\n";
}

}
