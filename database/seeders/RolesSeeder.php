<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'ADMINISTRADOR',
                'display_name' => 'Administrador',
                'warehouse' => null,
                'order' => 1,
                'permissions' => [
                    'USUARIOS',
                    'TIENDAS',
                    'ALMACENES',
                    'PROVEEDORES',
                    'CLIENTES',
                    'PRODUCTOS',
                    'TRANSFERENCIAS',
                    'VENTAS',
                    'REPORTES',
                    'CONFIGURACION',
                ],
            ], [
                'name' => 'CAJERO',
                'display_name' => 'Cajero',
                'warehouse' => 0,
                'order' => 4,
                'permissions' => [
                    'CLIENTES',
                    'TRANSFERENCIAS',
                    'VENTAS',
                    'REPORTES',
                ],
            ]
        ];

        foreach($roles as $role) {
            $new_role = Role::updateOrCreate([
                'name' => $role['name'],
            ], [
                'warehouse' => $role['warehouse'],
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
