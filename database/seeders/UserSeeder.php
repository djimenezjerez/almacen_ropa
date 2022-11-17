<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Person;
use App\Models\Store;
use App\Models\User;
use App\Models\City;
use App\Models\DocumentType;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Pablo Ugarte',
                'document' => '987654321',
                'email' => 'pugarte@ropa.com',
                'phone' => 76543210,
                'city' => 'SC',
                'document_type' => 'CI',
                'username' => 'admin',
                'password' => 'admin',
                'stores' => [
                    [
                        'name' => 'Moda Bella',
                        'role' => 'ADMINISTRADOR',
                    ], [
                        'name' => 'Casa Moda',
                        'role' => 'ADMINISTRADOR',
                    ], [
                        'name' => 'Almacén principal',
                        'role' => 'ADMINISTRADOR',
                    ], [
                        'name' => 'Almacén sucursal',
                        'role' => 'ADMINISTRADOR',
                    ]
                ],
            ], [
                'name' => 'Pedro Ramos',
                'document' => '987654321',
                'email' => 'pramos@ropa.com',
                'phone' => 76543210,
                'city' => 'SC',
                'document_type' => 'CI',
                'username' => 'gerente',
                'password' => 'gerente',
                'stores' => [
                    [
                        'name' => 'Moda Bella',
                        'role' => 'GERENTE',
                    ], [
                        'name' => 'Casa Moda',
                        'role' => 'GERENTE',
                    ]
                ],
            ], [
                'name' => 'Jorge Medina',
                'document' => '987654321',
                'email' => 'prios@ropa.com',
                'phone' => 76543210,
                'city' => 'SC',
                'document_type' => 'CI',
                'username' => 'cajero',
                'password' => 'cajero',
                'stores' => [
                    [
                        'name' => 'Moda Bella',
                        'role' => 'CAJERO',
                    ], [
                        'name' => 'Casa Moda',
                        'role' => 'CAJERO',
                    ]
                ],
            ], [
                'name' => 'Laura Flores',
                'document' => '987654321',
                'email' => 'prios@ropa.com',
                'phone' => 76543210,
                'city' => 'CB',
                'document_type' => 'CI',
                'username' => 'supervisor',
                'password' => 'supervisor',
                'stores' => [
                    [
                        'name' => 'Almacén principal',
                        'role' => 'SUPERVISOR',
                    ], [
                        'name' => 'Almacén sucursal',
                        'role' => 'SUPERVISOR',
                    ]
                ],
            ],
        ];

        foreach($data as $item) {
            $city = City::where('code', $item['city'])->firstOrFail();
            $document_type = DocumentType::where('code', $item['document_type'])->firstOrFail();
            $person = Person::updateOrCreate([
                'name' => $item['name'],
                'document' => $item['document'],
                'document_type_id' => $document_type->id,
            ], [
                'email' => $item['email'],
                'phone' => $item['phone'],
                'city_id' => $city->id,
            ]);

            $user = User::updateOrCreate([
                'username' => $item['username'],
            ], [
                'password' => $item['password'],
                'person_id' => $person->id,
            ]);

            foreach ($item['stores'] as $register) {
                $role = Role::where('name', $register['role'])->firstOrFail();
                $store = Store::whereRelation('person', 'name', '=', $register['name'])->firstOrFail();
                $query = DB::table('model_has_roles')->where('model_type', '=', 'App\Models\User')->where('model_id', $user->id)->where('store_id', $store->id);
                $count = $query->where('role_id', $role->id)->count();
                if ($count == 0) {
                    DB::table('model_has_roles')->insert([
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                        'store_id' => $store->id,
                        'role_id' => $role->id,
                    ]);
                } else {
                    $query->update([
                        'role_id' => $role->id,
                    ]);
                }
            }
        }
    }
}
