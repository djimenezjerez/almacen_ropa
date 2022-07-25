<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\Client;
use App\Models\City;
use App\Models\DocumentType;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Sin Nombre',
                'document' => '0',
                'document_type' => 'NIT',
                'email' => null,
                'phone' => null,
                'city' => null,
            ], [
                'name' => 'Pedro Ramos',
                'document' => '44556677',
                'document_type' => 'CI',
                'email' => null,
                'phone' => null,
                'city' => null,
            ],
        ];

        foreach($data as $item) {
            $city = City::where('code', $item['city'])->first();
            $document_type = DocumentType::where('code', $item['document_type'])->first();

            $person = Person::updateOrCreate([
                'name' => $item['name'],
                'document' => $item['document'],
                'document_type_id' => $document_type->id,
            ], [
                'email' => $item['email'],
                'phone' => $item['phone'],
                'city_id' => $city ? $city->id : null,
            ]);

            Client::firstOrCreate([
                'person_id' => $person->id,
            ]);
        }
    }
}
