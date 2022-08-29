<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SizeType;

class SizeTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Adultos',
                'order' => 1,
            ],[
                'name' => 'Infantes',
                'order' => 2,
            ],
        ];

        foreach($data as $item) {
            SizeType::firstOrCreate([
                'name' => $item['name'],
            ], [
                'order' => $item['order'],
            ]);
        }
    }
}