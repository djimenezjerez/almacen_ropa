<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use App\Models\Client;
use App\Models\Person;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Sin Nombre',
                'document' => '0',
                'document_type' => 'NIT',
                'email' => 'cliente1@gmail.com',
                'phone' => null,
                'city' => null,
                'password' => 'cliente1',
            ],
            [
                'name' => 'Pedro Ramos',
                'document' => '44556677',
                'document_type' => 'CI',
                'email' => 'cliente2@gmail.com',
                'phone' => null,
                'city' => null,
                'password' => 'cliente2',
            ],
        ];

        foreach ($data as $item) {
            $city = City::where('code', $item['city'])->first();
            $document_type = DocumentType::where('code', $item['document_type'])->first();
            $user = User::updateOrCreate([
                'username' => $item['email'],
                'password' => $item['password'],
                'active' => true,
            ]);
            $user->person()->updateOrCreate([
                'name' => $item['name'],
                'document' => $item['document'],
                'document_type_id' => $document_type->id,
            ], [
                'email' => $item['email'],
                'phone' => $item['phone'],
                'city_id' => $city ? $city->id : null,
            ]);
            $user->client()->firstOrCreate();
        }
    }
}
