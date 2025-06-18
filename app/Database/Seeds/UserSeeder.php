<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $defaultPassword = password_hash('12345678', PASSWORD_BCRYPT);

        // Akun unit (role = akademik, ppks, sarpras)
        $unitAccounts = [
            ['username' => 'Akademik', 'email' => 'akademik@example.com', 'role' => 'akademik'],
            ['username' => 'PPKS',     'email' => 'ppks@example.com',     'role' => 'ppks'],
            ['username' => 'Sarpras',  'email' => 'sarpras@example.com',  'role' => 'sarpras'],
        ];

        foreach ($unitAccounts as $unit) {
            $this->db->table('users')->insert([
                'username'       => $unit['username'],
                'email'      => $unit['email'],
                'password'   => $defaultPassword,
                'role'       => $unit['role'],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ]);

            $userID = $this->db->insertID();

            $this->db->table('unit')->insert([
                'user_id' => $userID,
                'nama'    => $unit['username']
            ]);
        }

        // Akun mahasiswa
        $mahasiswaList = [
            [
                'username'     => 'Mawar',
                'email'    => 'mawar01@example.com',
                'role'     => 'mahasiswa',
                'mahasiswa'=> [
                    'nim'     => '230001',
                    'nama'    => 'Mawar',
                    'kelas'   => 'TI-1A',
                    'prodi'   => 'Teknik Informatika',
                    'jurusan' => 'Informatika',
                ]
            ],
            [
                'username'     => 'Budi',
                'email'    => 'budi02@example.com',
                'role'     => 'mahasiswa',
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
            $this->db->table('users')->insert([
                'username'       => $mhs['username'],
                'email'      => $mhs['email'],
                'password'   => $defaultPassword,
                'role'       => $mhs['role'],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ]);

            $userID = $this->db->insertID();

            $mhsData = $mhs['mahasiswa'];
            $mhsData['user_id'] = $userID;

            $this->db->table('mahasiswa')->insert($mhsData);
        }

        echo "Seeder JWT manual berhasil dijalankan! ✅\n";
    }
}
