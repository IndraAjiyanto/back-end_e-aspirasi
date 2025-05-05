<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Models\GroupModel;
use Myth\Auth\Models\PermissionModel;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $group = new GroupModel();
        $permissions = new PermissionModel();

        // Insert Superadmin
        $group->insert([
            'name' => 'admin',
            'description' => 'Level Dewa',
        ]);
        $superadminID = $group->getInsertID();

        // Insert Admin
        $group->insert([
            'name' => 'mahasiswa',
            'description' => 'Level Raja',
        ]);
        $adminID = $group->getInsertID();

        // Assign semua permission ke Superadmin
        $superadminPerms = $permissions->findAll();
        foreach ($superadminPerms as $perm) {
            $group->addPermissionToGroup($perm->id, $superadminID);
        }

        // Assign hanya 'user-module' ke Admin
        $adminPerms = $permissions->where('name', 'user-module')->findAll();
        foreach ($adminPerms as $perm) {
            $group->addPermissionToGroup($perm->id, $adminID);
        }
    }
}
