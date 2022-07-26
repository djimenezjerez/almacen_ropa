<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Jean clásico',
                'category' => 'Jeans',
                'brand' => 'American Colt',
                'size' => '42',
                'color' => 'Negro',
            ], [
                'name' => 'Polera adulto',
                'category' => 'Poleras',
                'brand' => 'Tommy Hilfiger',
                'size' => 'L',
                'color' => 'Blanco',
            ], [
                'name' => 'Polera niño',
                'category' => 'Poleras',
                'brand' => 'Nike',
                'size' => 'XS',
                'color' => 'Azul',
            ], [
                'name' => 'Polerón adulto',
                'category' => 'Polerones',
                'brand' => 'Tommy Hilfiger',
                'size' => 'M',
                'color' => 'Verde',
            ],
        ];

        foreach($data as $item) {
            $size_type = SizeType::first();
            $color = Color::firstOrCreate([
                'name' => $item['color']
            ]);
            $size = Size::firstOrCreate([
                'name' => $item['size']
            ]);
            $brand = Brand::firstOrCreate([
                'name' => $item['brand']
            ]);
            $category = Category::firstOrCreate([
                'name' => $item['category']
            ]);
            Product::firstOrCreate([
                'name' => $item['name']
            ], [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'size_id' => $size->id,
                'size_type_id' => $size_type->id,
                'color_id' => $color->id,
            ]);
        }
    }
}
