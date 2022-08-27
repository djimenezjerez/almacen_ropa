<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\Store;
use App\Models\City;
use App\Models\DocumentType;

class StoreSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Moda Bella',
                'document' => '987654321',
                'email' => 'modabella@ropa.com',
                'phone' => 76543210,
                'city' => 'SC',
                'document_type' => 'NIT',
                'address' => 'Av. Prueba 123',
            ], [
                'name' => 'Casa Moda',
                'document' => '987654321',
                'document_type' => 'NIT',
                'email' => 'modabella@moda.com',
                'phone' => 65432100,
                'city' => 'LP',
                'address' => 'Av. Test 456',
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
                'address' => $item['address'],
            ]);

            Store::firstOrCreate([
                'person_id' => $person->id,
            ]);
        }
    }
}
