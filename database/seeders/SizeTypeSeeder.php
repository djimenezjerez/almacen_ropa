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
                'alphabetic' => ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'numeric' => ['32', '34', '36', '38', '40', '42', '44', '46', '48', '50', '52'],
                'order' => 1,
            ],[
                'name' => 'Infantes',
                'alphabetic' => ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'numeric' => ['8', '10', '12', '14', '16', '18', '20', '22', '24', '26'],
                'order' => 2,
            ],
        ];

        foreach($data as $item) {
            $size_type = SizeType::firstOrCreate([
                'name' => $item['name'],
            ], [
                'order' => $item['order'],
            ]);

            foreach($item['numeric'] as $i => $size) {
                Size::firstOrCreate([
                    'name' => $size,
                    'size_type_id' => $size_type->id,
                    'numeric' => true,
                ], [
                    'order' => $i + 1,
                ]);
            }
            foreach($item['alphabetic'] as $i => $size) {
                Size::firstOrCreate([
                    'name' => $size,
                    'size_type_id' => $size_type->id,
                    'numeric' => false,
                ], [
                    'order' => $i + 1,
                ]);
            }
        }
    }
}
