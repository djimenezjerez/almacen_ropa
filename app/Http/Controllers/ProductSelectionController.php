<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSelectionController extends Controller
{
    public function size_types(Request $request)
    {
        $validated = $request->validate([
            'active' => 'nullable|sometimes|required|boolean',
            'store_id' => 'nullable|sometimes|required|exists:stores,id',
        ]);

        $store = false;
        if ($request->has('store_id') && $request->has('active')) {
            if ($request->store_id !== null && $request->active !== null) {
                $store = DB::table('stores')->where('id', (int)$request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }

        $query = DB::table('products')->select('size_types.id', 'size_types.name');

        if ($store) {
            $query->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        }

        $query->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null)->groupBy('size_types.id')->orderBy('size_types.order');;

        if ($request->has('active')) {
            if ($request->active !== null && (int)$request->active != 0) {
                $query->where('products.active', 1);
            }
        }

        return [
            'message' => 'Tipos de tallas',
            'payload' => [
                'data' => $query->get(),
            ],
        ];
    }

    public function product_names(Request $request)
    {
        $validated = $request->validate([
            'active' => 'nullable|sometimes|required|boolean',
            'store_id' => 'nullable|sometimes|required|exists:stores,id',
            'size_type_id' => 'required|exists:size_types,id',
        ]);

        $store = false;
        if ($request->has('store_id') && $request->has('active')) {
            if ($request->store_id !== null && $request->active !== null) {
                $store = DB::table('stores')->where('id', (int)$request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }

        $query = DB::table('products')->select('product_names.id as product_name_id', 'product_names.name as product_name', 'categories.id as category_id', 'categories.name as category_name');

        if ($store) {
            $query->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        }

        $query->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->where('products.deleted_at', null)->where('sizes.size_type_id', (int)$request->size_type_id)->groupBy('product_names.id')->orderBy('product_names.name');

        if ($request->has('active')) {
            if ($request->active !== null && $request->active != 0) {
                $query->where('products.active', 1);
            }
        }

        return [
            'message' => 'Tipos de tallas',
            'payload' => [
                'data' => $query->get(),
            ],
        ];
    }

    public function genders(Request $request)
    {
        $validated = $request->validate([
            'active' => 'nullable|sometimes|required|boolean',
            'store_id' => 'nullable|sometimes|required|exists:stores,id',
            'size_type_id' => 'required|exists:size_types,id',
            'product_name_id' => 'required|exists:product_names,id',
        ]);

        $store = false;
        if ($request->has('store_id') && $request->has('active')) {
            if ($request->store_id !== null && $request->active !== null) {
                $store = DB::table('stores')->where('id', $request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }

        $query = DB::table('products')->select('genders.id', 'genders.name');

        if ($store) {
            $query->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        }

        $query->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->where('products.deleted_at', null)->where('sizes.size_type_id', (int)$request->size_type_id)->where('products.product_name_id', (int)$request->product_name_id)->groupBy('genders.id')->orderBy('genders.order');

        if ($request->has('active')) {
            if ($request->active !== null && $request->active != 0) {
                $query->where('products.active', 1);
            }
        }

        return [
            'message' => 'Géneros',
            'payload' => [
                'data' => $query->get(),
            ],
        ];
    }

    public function brands(Request $request)
    {
        $validated = $request->validate([
            'active' => 'nullable|sometimes|required|boolean',
            'store_id' => 'nullable|sometimes|required|exists:stores,id',
            'size_type_id' => 'required|exists:size_types,id',
            'product_name_id' => 'required|exists:product_names,id',
            'gender_id' => 'required|exists:genders,id',
        ]);

        $store = false;
        if ($request->has('store_id') && $request->has('active')) {
            if ($request->store_id !== null && $request->active !== null) {
                $store = DB::table('stores')->where('id', $request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }

        $query = DB::table('products')->select('brands.id', 'brands.name');

        if ($store) {
            $query->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        }

        $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->where('products.deleted_at', null)->where('sizes.size_type_id', (int)$request->size_type_id)->where('products.product_name_id', (int)$request->product_name_id)->where('products.gender_id', (int)$request->gender_id)->groupBy('brands.id')->orderBy('brands.name');

        if ($request->has('active')) {
            if ($request->active !== null && $request->active != 0) {
                $query->where('products.active', 1);
            }
        }

        return [
            'message' => 'Géneros',
            'payload' => [
                'data' => $query->get(),
            ],
        ];
    }

    public function colors(Request $request)
    {
        $validated = $request->validate([
            'active' => 'nullable|sometimes|required|boolean',
            'store_id' => 'nullable|sometimes|required|exists:stores,id',
            'size_type_id' => 'required|exists:size_types,id',
            'product_name_id' => 'required|exists:product_names,id',
            'gender_id' => 'required|exists:genders,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $store = false;
        if ($request->has('store_id') && $request->has('active')) {
            if ($request->store_id !== null && $request->active !== null) {
                $store = DB::table('stores')->where('id', $request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }

        $query = DB::table('products')->select('colors.id', 'colors.name');

        if ($store) {
            $query->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        }

        $query->leftJoin('colors', 'colors.id', '=', 'products.color_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->where('products.deleted_at', null)->where('sizes.size_type_id', (int)$request->size_type_id)->where('products.product_name_id', (int)$request->product_name_id)->where('products.gender_id', (int)$request->gender_id)->where('products.brand_id', (int)$request->brand_id)->groupBy('colors.id')->orderBy('colors.name');

        if ($request->has('active')) {
            if ($request->active !== null && $request->active != 0) {
                $query->where('products.active', 1);
            }
        }

        return [
            'message' => 'Colores',
            'payload' => [
                'data' => $query->get(),
            ],
        ];
    }

    public function sizes(Request $request)
    {
        $validated = $request->validate([
            'active' => 'nullable|sometimes|required|boolean',
            'store_id' => 'nullable|sometimes|required|exists:stores,id',
            'size_type_id' => 'required|exists:size_types,id',
            'product_name_id' => 'required|exists:product_names,id',
            'gender_id' => 'required|exists:genders,id',
            'brand_id' => 'required|exists:brands,id',
            'color_id' => 'required|exists:colors,id',
            'excluded' => 'sometimes|required|array',
            'excluded.*' => 'required|integer',
            'available' => 'required|boolean'
        ]);

        if (!$request->excluded) {
            $request->merge(['excluded' => []]);
        }

        $store = false;
        if ($request->has('store_id') && $request->has('active')) {
            if ($request->store_id !== null && $request->active !== null) {
                $store = DB::table('stores')->where('id', $request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->selectRaw('cast(sum(stock) as UNSIGNED) as stock')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }

        $query = DB::table('products')->select('products.id', 'sizes.id as size_id', 'sizes.name as size_name');

        if ($store) {
            $query->selectRaw('coalesce(md.stock, 0) as total_stock')->selectRaw('1 as stock')->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
            if ($request->available) {
                $query->where('md.stock', '>', 0);
            }
        } else {
            $query->selectRaw('coalesce(products.stock, 0) as total_stock')->selectRaw('1 as stock');
            if ($request->available) {
                $query->where('products.stock', '>', 0);
            }
        }

        $query->leftJoin('colors', 'colors.id', '=', 'products.color_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->where('products.deleted_at', null)->whereNotIn('products.id', $request->excluded)->where('sizes.size_type_id', (int)$request->size_type_id)->where('products.product_name_id', (int)$request->product_name_id)->where('products.gender_id', (int)$request->gender_id)->where('products.brand_id', (int)$request->brand_id)->where('products.color_id', (int)$request->color_id)->orderBy('sizes.numeric')->orderBy('sizes.order');

        if ($request->has('active')) {
            if ($request->active !== null && $request->active != 0) {
                $query->where('products.active', 1);
            }
        }

        return [
            'message' => 'Colores',
            'payload' => [
                'data' => $query->get(),
            ],
        ];
    }
}
