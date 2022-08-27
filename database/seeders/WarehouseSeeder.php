<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Person;
use App\Models\City;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Almacén principal',
                'address' => 'Av Prueba 123',
                'phone' => 76543210,
                'city' => 'SC',
            ], [
                'name' => 'Almacén sucursal',
                'address' => 'Av Prueba 456',
                'phone' => 76543210,
                'city' => 'SC',
            ],
        ];

        $user = User::first();
        foreach($data as $item) {
            $city = City::where('code', $item['city'])->firstOrFail();

            $person = Person::updateOrCreate([
                'name' => $item['name'],
            ], [
                'phone' => $item['phone'],
                'city_id' => $city->id,
                'address' => $item['address'],
            ]);

            Warehouse::firstOrCreate([
                'person_id' => $person->id,
            ], [
                'user_id' => $user->id,
            ]);
        }
    }
}
