<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

use Myth\Auth\Models\PermissionModel;
use Myth\Auth\Models\GroupModel;

class GroupSeeder extends Seeder
{
    public function run()
    {

        $groups = new GroupModel();
        $groups->skipValidation(true); 

        $adminGroupID = $groups->insert([
            'name' => 'akademik',
            'description' => 'Akademik',
        ]);

        $adminGroupID = $groups->insert([
            'name' => 'ppks',
            'description' => 'PPKS',
        ]);
        $adminGroupID = $groups->insert([
            'name' => 'sarpras',
            'description' => 'Sarana & Prasarana',
        ]);
        
        $mahasiswaGroupID = $groups->insert([
            'name' => 'mahasiswa',
            'description' => 'Mahasiswa',
        ]);
        

        $permissions = new PermissionModel(); 
        $permissions->skipValidation(true);

        $allPermissions = $permissions->findAll();
        
        foreach ($allPermissions as $permission) {
            $groups->addPermissionToGroup($permission->id, $adminGroupID);
        }
        
        $adminPermission = $permissions->where('name', 'user-module')->first();
        if ($adminPermission) {
            $groups->addPermissionToGroup($adminPermission->id, $mahasiswaGroupID);
        }
        

    }
}
