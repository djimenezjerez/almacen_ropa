<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Person;
use App\Models\Store;
use App\Models\City;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
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
                'name' => 'Moda Bella',
                'document' => '987654321',
                'email' => 'modabella@ropa.com',
                'phone' => 76543210,
                'city' => 'SC',
                'document_type' => 'NIT',
            ],
        ];

        foreach($data as $item) {
            $city = City::where('code', $item['city'])->firstOrFail();
            $document_type = DocumentType::where('code', $item['document_type'])->firstOrFail();

            $person = Person::updateOrCreate([
                'document' => $item['document'],
                'document_type_id' => $document_type->id,
            ], [
                'name' => $item['name'],
                'email' => $item['email'],
                'phone' => $item['phone'],
                'city_id' => $city->id,
            ]);

            Store::firstOrCreate([
                'person_id' => $person->id,
            ]);
        }
    }
}
