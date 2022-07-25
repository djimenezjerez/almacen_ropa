<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\Supplier;
use App\Models\City;
use App\Models\DocumentType;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Hansa',
                'document' => '11223344',
                'document_type' => 'NIT',
                'email' => 'contacto@hansa.com',
                'phone' => 76543210,
                'city' => 'CB',
            ], [
                'name' => 'Lider',
                'document' => '123123123',
                'document_type' => 'NIT',
                'email' => 'contacto@lider.com',
                'phone' => 6543540,
                'city' => 'SC',
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

            Supplier::firstOrCreate([
                'person_id' => $person->id,
            ]);
        }
    }
}
