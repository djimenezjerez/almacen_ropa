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
                'warehouse' => false,
            ], [
                'name' => 'Casa Moda',
                'document' => '987654321',
                'document_type' => 'NIT',
                'email' => 'modabella@moda.com',
                'phone' => 65432100,
                'city' => 'LP',
                'address' => 'Av. Test 456',
                'warehouse' => false,
            ], [
                'name' => 'Almacén principal',
                'document' => null,
                'document_type' => null,
                'email' => null,
                'phone' => 76543210,
                'city' => 'SC',
                'address' => 'Av Prueba 123',
                'warehouse' => true,
            ], [
                'name' => 'Almacén sucursal',
                'document' => null,
                'document_type' => null,
                'email' => null,
                'phone' => 76543210,
                'city' => 'SC',
                'address' => 'Av Prueba 456',
                'warehouse' => true,
            ],
        ];

        foreach($data as $item) {
            $city = City::where('code', $item['city'])->firstOrFail();
            if ($item['document_type'] != null) {
                $document_type = DocumentType::where('code', $item['document_type'])->firstOrFail();
            } else {
                $document_type = (object)['id' => null];
            }

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
            ], [
                'warehouse' => $item['warehouse']
            ]);
        }
    }
}
