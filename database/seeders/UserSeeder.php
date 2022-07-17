<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Person;
use App\Models\Store;
use App\Models\User;
use App\Models\City;
use App\Models\DocumentType;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'SUPER ADMINISTRADOR',
                'document' => '987654321',
                'email' => 'ADMINISTRADOR@ropa.com',
                'phone' => 76543210,
                'city' => 'SC',
                'document_type' => 'CI',
                'role' => 'ADMINISTRADOR',
                'username' => 'admin',
                'password' => 'password',
                'store_document' => '987654321',
            ],
        ];

        foreach($data as $item) {
            $city = City::where('code', $item['city'])->firstOrFail();
            $document_type = DocumentType::where('code', $item['document_type'])->firstOrFail();
            $role = Role::where('name', $item['role'])->firstOrFail();
            $store = Store::whereRelation('person', 'document', '=', $item['store_document'])->firstOrFail();

            $person = Person::updateOrCreate([
                'document' => $item['document'],
                'document_type_id' => $document_type->id,
            ], [
                'name' => $item['name'],
                'email' => $item['email'],
                'phone' => $item['phone'],
                'city_id' => $city->id,
            ]);

            $new_user = User::updateOrCreate([
                'username' => $item['username'],
            ], [
                'password' => $item['password'],
                'person_id' => $person->id,
            ]);
            $new_user->roles()->syncWithPivotValues([$role->id], ['store_id' => $store->id]);
        }
    }
}
