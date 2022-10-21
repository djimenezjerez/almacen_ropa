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
        if ($this->store_type != null && $this->store_id != null) {
            $sub_query = DB::table('stock_transfers')->select('stock_transfer_details.product_id as product_id', DB::raw('sum(stock_transfer_details.stock) as stock'))->leftJoin('stock_transfer_details', 'stock_transfer_details.stock_transfer_id', '=', 'stock_transfers.id')->groupBy('stock_transfer_details.product_id');
            $in_stock = with(clone $sub_query)->where($this->store_type == 'store' ? 'stock_transfers.destiny_store_id' : 'stock_transfers.destiny_warehouse_id', $this->store_id);
            $out_stock = with(clone $sub_query)->where($this->store_type == 'store' ? 'stock_transfers.origin_store_id' : 'stock_transfers.origin_warehouse_id', $this->store_id);
        }
        foreach($size_types as $i => $size_type) {
            $data['size_types'][] = [
                'id' => $size_type->id,
                'name' => $size_type->name,
                'genders' => [],
            ];
            foreach($genders as $gender) {
                $select = ['products.id', 'products.brand_id', 'brands.name as brand_name', 'sizes.name as size_name', 'colors.name as color_name', 'products.stock as total_stock'];
                if ($this->store_type != null && $this->store_id != null) {
                    $select = array_merge($select, [DB::raw('coalesce(in_stock.stock, 0) as in_stock'), DB::raw('coalesce(out_stock.stock, 0) as out_stock'), DB::raw('coalesce((coalesce(in_stock.stock, 0) - coalesce(out_stock.stock, 0)), 0) as stock')]);
                }
                $products_query = DB::table('products')->select($select)->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id');
                if ($this->store_type != null && $this->store_id != null) {
                    $products_query->leftJoinSub($in_stock, 'in_stock', function($join) {
                        $join->on('in_stock.product_id', '=', 'products.id');
                    })->leftJoinSub($out_stock, 'out_stock', function($join) {
                        $join->on('out_stock.product_id', '=', 'products.id');
                    });
                }
                $products_query->where('sizes.size_type_id', $size_type->id)->where('products.gender_id', $gender->id)->where('products.product_name_id', $this->id);
                if ($request->has('except')) {
                    if (count($request->except) > 0) {
                        $products_query->whereNotIn('products.id', $request->except);
                    }
                }
                if ($this->store_type != null && $this->store_id != null) {
                    $products_query->havingRaw('stock > 0');
                }
                $products_query->orderBy('brands.name')->orderBy('sizes.name')->orderBy('colors.name');
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
