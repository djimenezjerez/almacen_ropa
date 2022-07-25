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
                'display_name' => 'Administrador',
                'order' => 1,
                'permissions' => [
                    'USUARIOS',
                    'TIENDAS',
                    'ALMACENES',
                    'PROVEEDORES',
                    'CLIENTES',
                ],
            ], [
                'name' => 'GERENTE',
                'display_name' => 'Gerente',
                'order' => 2,
                'permissions' => [
                    'USUARIOS',
                    'TIENDAS',
                    'ALMACENES',
                    'PROVEEDORES',
                    'CLIENTES',
                ],
            ], [
                'name' => 'CAJERO',
                'display_name' => 'Cajero',
                'order' => 3,
                'permissions' => [],
            ]
        ];

        foreach($roles as $role) {
            $new_role = Role::updateOrCreate([
                'name' => $role['name'],
            ], [
                'display_name' => $role['display_name'],
                'order' => $role['order'],
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
