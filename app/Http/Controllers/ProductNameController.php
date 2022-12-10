<?php

namespace App\Http\Controllers;

use App\Models\ProductName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductNameController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de nombres de productos',
            'payload' => [
                'data' => DB::table('product_names')->select('id', 'name', 'sell_price', 'category_id')->orderBy('name')->get(),
            ],
        ];
    }

    public function show(ProductName $product_name, Request $request)
    {
        $store = false;
        if ($request->has('store_id')) {
            $store = DB::table('stores')->where('id', (int)$request->store_id)->exists();
            if ($store) {
                $movements = DB::table('movement_details')->select('product_id')->selectRaw('cast(sum(stock) as INTEGER) as stock')->where('store_id', (int)$request->store_id)->groupBy('product_id');
            }
        }

        $query = DB::table('products');

        if ($store) {
            $query->selectRaw('cast(sum(md.stock) as INTEGER) as total_stock')->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        } else {
            $query->selectRaw('cast(sum(products.stock) as INTEGER) as total_stock');
        }

        $query->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->where('products.product_name_id', $product_name->id)->where('products.deleted_at', null)->groupBy('products.product_name_id');
        if ($request->has('size_type_id')) {
            $query->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('size_types.id', $request->size_type_id);
        }
        if ($request->has('brand_id')) {
            $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->where('products.brand_id', $request->brand_id);
        }
        if ($request->has('gender')) {
            $query->leftJoin('genders', 'genders.id', '=', 'products.gender')->where('products.gender', $request->gender);
        }
        if ($request->has('color_id')) {
            $query->leftJoin('colors', 'colors.id', '=', 'products.color_id')->where('products.color_id', $request->brand_id);
        }
        return [
            'message' => 'Nombre de producto',
            'payload' => [
                'id' => $product_name->id,
                'name' => $product_name->name,
                'category_id' => $product_name->category->id,
                'category_name' => $product_name->category->name,
                'total_stock' => $query->first() != null ? $query->first()->total_stock : 0,
            ],
        ];
    }
}
