<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductName;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\Gender;
use App\Models\SizeType;
use App\Http\Requests\SizeTypeRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(SizeTypeRequest $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de productos',
                'payload' => [
                    'data' => DB::table('product_names')->select('product_names.id', 'product_names.name', 'categories.id as category_id', 'categories.name as category_name')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->orderBy('product_names.name')->orderBy('categories.name')->get(),
                ],
            ];
        }

        $query = DB::table('products')->select('products.product_name_id', 'categories.name as category_name', 'product_names.name as product_name')->selectRaw('sum(products.stock) as total_stock')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null)->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id');
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('categories.name', 'ASC')->orderBy('product_names.name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(categories.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de productos',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function show(ProductName $product_name, SizeTypeRequest $request)
    {
        $query = DB::table('products')->select('products.id', 'products.product_name_id', 'products.brand_id', 'brands.name as brand_name', 'products.gender_id', 'genders.name as gender_name', 'products.color_id', 'colors.name as color_name')->selectRaw('sum(products.stock) as total_stock')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.product_name_id', $product_name->id)->where('size_types.id', (int)$request->size_type_id)->where('products.deleted_at', null)->groupBy('products.brand_id', 'products.gender_id', 'products.color_id');
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('colors.name', 'ASC')->orderBy('genders.name', 'ASC')->orderBy('brands.name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(brands.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(genders.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(colors.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista detallada de productos',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $category = Category::whereRaw("UPPER(name) LIKE '" . mb_strtoupper($request->category_name) . "'")->where('active', true)->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $request->category_name,
                ]);
            }
            $product_name = ProductName::whereRaw("UPPER(name) LIKE '" . mb_strtoupper($request->name) . "'")->where('category_id', $category->id)->first();
            if (!$product_name) {
                $product_name = ProductName::create([
                    'name' => $request->name,
                    'category_id' => $category->id,
                ]);
            }
            foreach($request->brands as $brand) {
                foreach($request->colors as $color) {
                    foreach($request->sizes as $size) {
                        Product::firstOrCreate([
                            'product_name_id' => $product_name->id,
                            'gender_id' => $request->gender_id,
                            'brand_id' => $brand,
                            'size_id' => $size,
                            'color_id' => $color,
                        ]);
                    }
                }
            }
            DB::commit();
            return [
                'message' => 'Producto registrado',
            ];
        } catch(Exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar producto'
            ], 500);
        }
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        $product->update($request->all());
        return [
            'message' => 'Datos de producto actualizados',
        ];
    }

    public function destroy(Product $product, Request $request)
    {
        if ($request->has('size')) {
            $product->delete();
        } else {
            Product::leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.product_name_id', $product->product_name_id)->where('products.brand_id', $product->brand_id)->where('products.gender_id', $product->gender_id)->where('products.color_id', $product->color_id)->where('size_types.id', $product->size->size_type_id)->delete();
        }
        return [
            'message' => 'Producto eliminado',
        ];
    }

    public function stock(ProductName $product_name, SizeTypeRequest $request)
    {
        $details = [];
        $total = 0;
        $colors = DB::table('products')->select('colors.id', 'colors.name')->distinct()->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->where('products.product_name_id', $product_name->id)->where('size_types.id', (int)$request->size_type_id)->where('products.deleted_at', null)->orderBy('colors.name')->get();
        foreach($colors as $color) {
            foreach([true, false] as $numeric) {
                $sizes = DB::table('products')->select('sizes.id', 'sizes.name')->selectRaw('sum(products.stock) as total_stock')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->where('products.product_name_id', $product_name->id)->where('size_types.id', (int)$request->size_type_id)->where('colors.id', $color->id)->where('sizes.numeric', $numeric)->where('products.deleted_at', null)->groupBy('products.size_id')->orderBy('sizes.order')->orderBy('sizes.id')->get();
                if (count($sizes) > 0) {
                    $details[] = [
                        'color' => $color->name,
                        'numeric' => $numeric,
                        'subtotal' => $sizes->sum('total_stock'),
                        'sizes' => $sizes,
                    ];
                }
            }
        }
        foreach($details as $detail) {
            $total += $detail['subtotal'];
        }
        return [
            'message' => 'Detalle de producto',
            'payload' => [
                'product_name' => $product_name->only(['id', 'name', 'active']),
                'category' => $product_name->category->only(['id', 'name', 'active']),
                'total' => $total,
                'details' => $details,
            ],
        ];
    }

    public function sizes(Product $product, SizeTypeRequest $request)
    {
        $query = DB::table('products')->select('products.id', 'products.size_id', 'sizes.name as size_name', 'products.active', 'products.stock')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.product_name_id', $product->product_name_id)->where('products.brand_id', $product->brand_id)->where('products.gender_id', $product->gender_id)->where('products.color_id', $product->color_id)->where('size_types.id', (int)$request->size_type_id)->where('products.deleted_at', null);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('sizes.order', 'ASC')->orderBy('sizes.id', 'ASC')->orderBy('sizes.name', 'ASC')->orderBy('products.stock', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(sizes.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de tallas de producto',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }
}
