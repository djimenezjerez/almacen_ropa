<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'category_name' => $this->category->name,
            'size_types' => [],
        ];
        $size_types = DB::table('size_types')->orderBy('id')->get();
        $genders = DB::table('genders')->orderBy('order')->get();
        foreach($size_types as $i => $size_type) {
            $data['size_types'][] = [
                'id' => $size_type->id,
                'name' => $size_type->name,
                'genders' => [],
            ];
            foreach($genders as $gender) {
                $products_query = DB::table('products')->select('products.id', 'products.brand_id', 'brands.name as brand_name', 'sizes.name as size_name', 'colors.name as color_name', 'products.stock')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->where('sizes.size_type_id', $size_type->id)->where('products.gender_id', $gender->id)->where('products.product_name_id', $this->id)->orderBy('brands.name')->orderBy('sizes.name')->orderBy('colors.name');
                if ($products_query->count() > 0) {
                    $data['size_types'][$i]['genders'][] = [
                        'id' => $gender->id,
                        'name' => $gender->name,
                        'products' => $products_query->get(),
                    ];
                }
            }
            if (count($data['size_types'][$i]['genders']) == 0) {
                unset($data['size_types'][$i]);
            }
        }
        return $data;
    }
}
