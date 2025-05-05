<?php

namespace App\Database\Seeds;

use Myth\Auth\Password;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;
use Myth\Auth\Models\GroupModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = new UserModel();
        $group = new GroupModel();

        $user->skipValidation(true);

        $user->insert([
            'username' => 'indraKece',
            'email' => 'indra@gmail.com',
            'password_hash' => Password::hash('12345678'),
            'active' => 1
        ]);

        $group->addUserToGroup($user->getInsertId(), 1);
        
        $user->insert([
            'username' => 'indraGanteng',
            'email' => 'indraG@gmail.com',
            'password_hash' => Password::hash('12345678'),
            'active' => 1
        ]);

        $group->addUserToGroup($user->getInsertId(), 2);

    }
}
