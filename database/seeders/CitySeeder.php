<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Santa Cruz',
                'code' => 'SC',
                'order' => 1,
            ], [
                'name' => 'La Paz',
                'code' => 'LP',
                'order' => 2,
            ], [
                'name' => 'Cochabamba',
                'code' => 'CB',
                'order' => 3,
            ], [
                'name' => 'Beni',
                'code' => 'BE',
                'order' => 4,
            ], [
                'name' => 'Pando',
                'code' => 'PD',
                'order' => 5,
            ], [
                'name' => 'Oruro',
                'code' => 'OR',
                'order' => 6,
            ], [
                'name' => 'Potosí',
                'code' => 'PT',
                'order' => 7,
            ], [
                'name' => 'Chuquisaca',
                'code' => 'CH',
                'order' => 8,
            ], [
                'name' => 'Tarija',
                'code' => 'TJ',
                'order' => 9,
            ],
        ];

        foreach($data as $item) {
            City::firstOrCreate([
                'code' => $item['code'],
            ], [
                'name' => $item['name'],
                'order' => $item['order'],
            ]);
        }
    }
}
