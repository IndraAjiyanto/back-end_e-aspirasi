<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Models\PermissionModel;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = new PermissionModel();
        $permissions->skipValidation(true);

        $permissions->insert([
            'name' => 'manage-users',
            'description' => 'Manage all users'
        ]);
        
        $permissions->insert([
            'name' => 'create-aspirasi',
            'description' => 'Create Aspirasi'
        ]);
    }
}
