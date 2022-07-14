<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            [
                'name' => 'Chuquisaca',
                'code' => 'CH',
            ],[
                'name' => 'La Paz',
                'code' => 'LP',
            ],[
                'name' => 'Cochabamba',
                'code' => 'CB',
            ],[
                'name' => 'Oruro',
                'code' => 'OR',
            ],[
                'name' => 'Potosí',
                'code' => 'PT',
            ],[
                'name' => 'Tarija',
                'code' => 'TJ',
            ],[
                'name' => 'Santa Cruz',
                'code' => 'SC',
            ],[
                'name' => 'Beni',
                'code' => 'BE',
            ],[
                'name' => 'Pando',
                'code' => 'PD',
            ],
        ];

        foreach($cities as $city) {
            City::firstOrCreate([
                'code' => $city['code'],
            ], [
                'name' => $city['name'],
            ]);
        }
    }
}
