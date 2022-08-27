<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Varón',
                'order' => 1,
            ],[
                'name' => 'Mujer',
                'order' => 2,
            ],[
                'name' => 'Unisex',
                'order' => 3,
            ],
        ];

        foreach($data as $item) {
            Gender::firstOrCreate([
                'name' => $item['name'],
            ], [
                'order' => $item['order'],
            ]);
        }
    }
}
