<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SizeType;
use App\Models\Size;

class SizeTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Adultos',
                'alphabetic' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'numeric' => ['33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'order' => 1,
            ],[
                'name' => 'Infantes',
                'alphabetic' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'numeric' => ['16', '17', '18', '19', '20', '21', '22', '23', '24', '25'],
                'order' => 2,
            ],
        ];

        foreach($data as $item) {
            $size_type = SizeType::firstOrCreate([
                'name' => $item['name'],
            ], [
                'order' => $item['order'],
            ]);

            foreach($item['numeric'] as $size) {
                Size::firstOrCreate([
                    'name' => $size,
                    'size_type_id' => $size_type->id,
                    'numeric' => true,
                ]);
            }
            foreach($item['alphabetic'] as $size) {
                Size::firstOrCreate([
                    'name' => $size,
                    'size_type_id' => $size_type->id,
                    'numeric' => false,
                ]);
            }
        }
    }
}
