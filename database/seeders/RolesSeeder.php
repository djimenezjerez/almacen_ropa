<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'ADMINISTRADOR',
                'permissions' => [
                    'USUARIOS',
                ],
            ], [
                'name' => 'CAJERO',
                'permissions' => [],
            ]
        ];

        foreach($roles as $role) {
            $new_role = Role::firstOrCreate([
                'name' => $role['name'],
            ]);

            foreach($role['permissions'] as $permission) {
                $new_permission = Permission::firstOrCreate([
                    'name' => $permission,
                ]);
                $new_role->givePermissionTo($new_permission);
            }
        }
    }
}
