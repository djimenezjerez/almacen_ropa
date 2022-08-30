<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductName;
use App\Models\Color;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Gender;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Jean clásico',
                'category' => 'Jeans',
                'genders' => ['Unisex'],
                'brands' => ['American Colt', 'Tommy Hilfiger'],
                'sizes' => ['40', '42', '44', '46', '48', '50'],
                'size_type' => 'Adultos',
                'colors' => ['Negro', 'Azul'],
            ], [
                'name' => 'Jean prelavado',
                'category' => 'Jeans',
                'genders' => ['Varón', 'Mujer'],
                'brands' => ['American Colt', 'Tommy Hilfiger'],
                'sizes' => ['42', '44', '46', '48'],
                'size_type' => 'Adultos',
                'colors' => ['Negro', 'Azul'],
            ], [
                'name' => 'Camiseta deportiva',
                'category' => 'Camisetas',
                'genders' => ['Varón', 'Mujer'],
                'brands' => ['Nike', 'Tommy Hilfiger'],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'size_type' => 'Adultos',
                'colors' => ['Negro', 'Blanco', 'Azul'],
            ], [
                'name' => 'Camiseta estampada',
                'category' => 'Camisetas',
                'genders' => ['Varón', 'Mujer'],
                'brands' => ['Nike', 'Tommy Hilfiger'],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'size_type' => 'Adultos',
                'colors' => ['Negro', 'Blanco', 'Azul'],
            ], [
                'name' => 'Polerón deportivo',
                'category' => 'Polerones',
                'genders' => ['Unisex'],
                'brands' => ['Chico', 'Nike', 'Adidas'],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'size_type' => 'Infantes',
                'colors' => ['Negro', 'Blanco', 'Azul'],
            ], [
                'name' => 'Zapatillas ortopédicas',
                'category' => 'Zapatillas',
                'genders' => ['Varón', 'Mujer'],
                'brands' => ['Chico', 'Nike', 'Adidas'],
                'sizes' => ['20', '22', '24', '26'],
                'size_type' => 'Infantes',
                'colors' => ['Negro', 'Blanco', 'Azul'],
            ],
        ];

        foreach($data as $item) {
            $category = Category::firstOrCreate([
                'name' => $item['category'],
            ]);
            $product_name = ProductName::firstOrCreate([
                'name' => $item['name'],
                'category_id' => $category->id,
            ]);
            $size_type = SizeType::firstOrCreate([
                'name' => $item['size_type'],
            ]);
            foreach ($item['sizes'] as $item_size) {
                $size = Size::firstOrCreate([
                    'name' => $item_size,
                    'size_type_id' => $size_type->id,
                ], [
                    'numeric' => is_numeric($item_size),
                ]);
                foreach ($item['brands'] as $item_brand) {
                    $brand = Brand::firstOrCreate([
                        'name' => $item_brand,
                    ]);
                    foreach ($item['colors'] as $item_color) {
                        $color = Color::firstOrCreate([
                            'name' => $item_color,
                        ]);
                        foreach ($item['genders'] as $item_gender) {
                            $gender = Gender::firstOrCreate([
                                'name' => $item_gender,
                            ]);
                            Product::firstOrCreate([
                                'product_name_id' => $product_name->id,
                                'brand_id' => $brand->id,
                                'gender_id' => $gender->id,
                                'size_id' => $size->id,
                                'color_id' => $color->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
