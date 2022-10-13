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
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de productos',
                'payload' => [
                    'data' => DB::table('product_names')->select('product_names.id', 'product_names.name', 'categories.id as category_id', 'categories.name as category_name')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->orderBy('product_names.name')->orderBy('categories.name')->get(),
                ],
            ];
        }

        $query = DB::table('products')->select('products.product_name_id', 'categories.name as category_name', 'size_types.name as size_type_name', 'product_names.name as product_name')->selectRaw('sum(products.stock) as total_stock')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->groupBy('products.product_name_id')->where('products.deleted_at', '=', null);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('categories.name', 'ASC')->orderBy('product_names.name', 'ASC')->orderBy('size_types.name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(categories.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(size_types.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de productos',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function show(ProductName $product_name)
    {
        $product_name->store_type = null;
        $product_name->store_id = null;
        return [
            'message' => 'Detalle de producto',
            'product' => new ProductResource($product_name),
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
            foreach($request->size_types as $req_size_type) {
                $size_type = SizeType::find($req_size_type['id']);
                foreach($req_size_type['genders'] as $req_gender) {
                    $gender = Gender::find($req_gender['id']);
                    foreach($req_gender['attributes']['brands'] as $req_brand) {
                        $brand = Brand::whereName($req_brand)->first();
                        foreach($req_gender['attributes']['colors'] as $req_color) {
                            $color = Color::whereName($req_color)->first();
                            foreach($req_gender['attributes']['alphabetic_sizes'] as $req_size) {
                                $size = Size::whereName($req_size)->whereNumeric(false)->whereSizeTypeId($size_type->id)->first();
                                Product::firstOrCreate([
                                    'product_name_id' => $product_name->id,
                                    'brand_id' => $brand->id,
                                    'gender_id' => $gender->id,
                                    'size_id' => $size->id,
                                    'color_id' => $color->id,
                                ]);
                            }
                            foreach($req_gender['attributes']['numeric_sizes']  as $req_size) {
                                $size = Size::whereName($req_size)->whereNumeric(true)->whereSizeTypeId($size_type->id)->first();
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
            DB::commit();
            return [
                'message' => 'Producto registrado',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al registrar producto',
            ];
        }
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();
            $category = Category::whereRaw("UPPER(name) LIKE '" . mb_strtoupper($request->category_name) . "'")->where('active', true)->first();
            if (!$category) {
                $category = Category::create([ 'name' => $request->category_name ]);
            }
            $brand = Brand::whereRaw("UPPER(name) LIKE '" . mb_strtoupper($request->brand_name) . "'")->first();
            if (!$brand) {
                $brand = Brand::create([ 'name' => $request->brand_name ]);
            }
            $size = Size::whereRaw("UPPER(name) LIKE '" . mb_strtoupper($request->size_name) . "'")->first();
            if (!$size) {
                $size = Size::create([ 'name' => $request->size_name ]);
            }
            $color = Color::whereRaw("UPPER(name) LIKE '" . mb_strtoupper($request->color_name) . "'")->first();
            if (!$color) {
                $color = Color::create([ 'name' => $request->color_name ]);
            }
            $product->update([
                'name' => $request->name,
                'active' => $request->active,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'size_id' => $size->id,
                'size_type_id' => $request->size_type_id,
                'color_id' => $color->id,
            ]);
            DB::commit();
            return [
                'message' => 'Datos de producto actualizados',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al actualizar producto',
            ];
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return [
            'message' => 'Producto eliminado',
        ];
    }

    public function stock(ProductName $product_name, $store_type, $store_id)
    {
        $product_name->store_type = $store_type;
        $product_name->store_id = intval($store_id);
        return [
            'message' => 'Detalle de producto por ' . ($store_type == 'store' ? 'tienda' : 'almacén'),
            'product' => new ProductResource($product_name),
        ];
    }
}
