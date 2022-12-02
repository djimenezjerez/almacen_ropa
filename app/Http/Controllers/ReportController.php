<?php

namespace App\Http\Controllers;

use App\Http\Requests\SizeTypeRequest;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function products(SizeTypeRequest $request)
    {
        $sizes = DB::table('sizes')->select('sizes.id', 'sizes.name', 'sizes.numeric')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('size_types.id', (int)$request->size_type_id)->orderBy('sizes.numeric')->orderBy('sizes.order')->orderBy('sizes.name')->get();

        $products = DB::table('products')->select('products.product_name_id', 'categories.name as category_name', 'product_names.name as product_name')->selectRaw('cast(sum(products.stock) as UNSIGNED) as total_stock')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null)->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id')->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1)->toArray();

        $details = DB::table('products')->select('products.product_name_id', 'products.size_id')->selectRaw('cast(sum(products.stock) as UNSIGNED) as stock')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null)->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id')->groupBy('size_id')->get();

        foreach ($products['data'] as $i => $product) {
            $stock = [];
            $product_details = $details->where('product_name_id', $product->product_name_id);
            foreach ($sizes as $size) {
                $detail = $product_details->filter(function($item) use ($size) {
                    return $item->size_id == $size->id;
                })->first();
                if ($detail) {
                    $stock[] = $detail->stock;
                } else {
                    $stock[] = 0;
                }
            }
            $products['data'][$i]->stock = $stock;
        }
        return [
            'message' => 'Lista de productos',
            'payload' => [
                'sizes' => $sizes,
                'products' => $products,
            ],
        ];
    }
}
