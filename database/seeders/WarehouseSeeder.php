<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
            ], [
                'name' => 'Almacén sucursal',
                'address' => 'Av Prueba 456',
            ],
        ];

        $user = User::first();
        $city = City::first();
        foreach($data as $item) {
            Warehouse::updateOrCreate([
                'name' => $item['name'],
            ], [
                'address' => $item['address'],
                'user_id' => $user->id,
                'city_id' => $city->id,
            ]);
        }
    }
}
