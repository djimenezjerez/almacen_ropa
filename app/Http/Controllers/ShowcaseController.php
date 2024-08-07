<?php

namespace App\Http\Controllers;

use App\Models\ProductName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // TODO: agregar imagen de cada color de producto a la base de datos
        $query = DB::table('movement_details')->select('products.product_name_id', 'product_names.name as product_name',  'products.brand_id', 'brands.name as brand_name', 'product_names.category_id', 'categories.name as category_name', 'sizes.size_type_id', 'size_types.name as size_type_name', 'products.gender_id', 'genders.name as gender_name', 'product_names.sell_price')->selectRaw('cast(sum(movement_details.stock) as INTEGER) as total_stock, "https://cdn.pixabay.com/photo/2024/02/25/13/30/shoes-8595773_1280.jpg" as image')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->where('products.deleted_at', null)->where('movement_details.store_id', $request->store_id)->where('sizes.size_type_id', $request->size_type_id)->where('product_names.category_id', $request->category_id)->groupBy('products.product_name_id')->groupBy('products.brand_id')->groupBy('sizes.size_type_id')->groupBy('product_names.category_id')->groupBy('products.gender_id');

        if ($request->has('gender_id')) {
            if ((int)$request->gender_id > 0) {
                $query->where('products.gender_id', $request->gender_id);
            }
        }
        if ($request->has('brand_id')) {
            if ((int)$request->brand_id > 0) {
                $query->where('products.brand_id', $request->brand_id);
            }
        }

        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('product_names.name')->orderBy('brands.name');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->orWhere(DB::raw('upper(product_names.name)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('upper(brands.name)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%');
            }
        }

        return [
            'message' => 'Lista de productos',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProductName $product_name, Request $request)
    {
        // TODO: agregar imagen de cada color de producto a la base de datos
        return [
            'message' => 'Detalle de producto',
            'payload' => DB::table('movement_details')->select('products.product_name_id', 'product_names.name as product_name',  'products.brand_id', 'brands.name as brand_name', 'product_names.category_id', 'categories.name as category_name', 'sizes.size_type_id', 'size_types.name as size_type_name', 'products.gender_id', 'genders.name as gender_name', 'product_names.sell_price')->selectRaw('"https://cdn.pixabay.com/photo/2024/02/25/13/30/shoes-8595773_1280.jpg" as image')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', 'products.product_name_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->where('products.deleted_at', null)->where('products.product_name_id', $product_name->id)->where('movement_details.store_id', $request->store_id)->where('products.brand_id', $request->brand_id)->where('sizes.size_type_id', $request->size_type_id)->where('products.gender_id', $request->gender_id)->first(),
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sizes(ProductName $product_name, Request $request)
    {
        return [
            'message' => 'Tallas de producto',
            'payload' => [
                'data' => DB::table('movement_details')->distinct()->select('sizes.id', 'sizes.name', 'sizes.numeric')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', 'products.product_name_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->where('products.deleted_at', null)->where('products.product_name_id', $product_name->id)->where('movement_details.store_id', $request->store_id)->where('products.brand_id', $request->brand_id)->where('sizes.size_type_id', $request->size_type_id)->where('products.gender_id', $request->gender_id)->get(),
            ],
        ];
    }

    public function colors(ProductName $product_name, Request $request)
    {
        // TODO: agregar imagen de cada color de producto a la base de datos
        return [
            'message' => 'Colores de producto',
            'payload' => [
                'data' => DB::table('movement_details')->distinct()->select('colors.id', 'colors.name')->selectRaw('"https://cdn.pixabay.com/photo/2024/02/25/13/30/shoes-8595773_1280.jpg" as image')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', 'products.product_name_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->where('products.deleted_at', null)->where('products.product_name_id', $product_name->id)->where('movement_details.store_id', $request->store_id)->where('products.brand_id', $request->brand_id)->where('sizes.size_type_id', $request->size_type_id)->where('products.gender_id', $request->gender_id)->get(),
            ],
        ];
    }

    public function stock(ProductName $product_name, Request $request)
    {
        $stock = DB::table('movement_details')->selectRaw('cast(sum(movement_details.stock) as INTEGER) as stock')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->where('products.deleted_at', null)->where('products.product_name_id', $product_name->id)->where('movement_details.store_id', $request->store_id)->where('products.brand_id', $request->brand_id)->where('products.gender_id', $request->gender_id)->where('products.size_id', $request->size_id)->where('products.color_id', $request->color_id)->first();
        if ($stock) {
            $stock = $stock->stock;
        } else {
            $stock = 0;
        }

        return [
            'message' => 'Stock de producto',
            'payload' => [
                'data' => $stock,
            ],
        ];
    }

    public function categories(Request $request)
    {
        $query = DB::table('movement_details')->distinct()->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->where('products.deleted_at', null)->where('movement_details.store_id', $request->store_id);
        $size_types = $query->clone()->select('size_types.id', 'size_types.name')->orderBy('size_types.order');
        if ($request->has('size_type_id')) {
            $size_types->where('sizes.size_type_id', $request->size_type_id);
        }
        $size_types = $size_types->get();
        foreach ($size_types as $size_type) {
            $size_type->groups = [];
            $genders = $query->clone()->select('genders.id', 'genders.name')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->orderBy('genders.order')->where('sizes.size_type_id', $size_type->id);
            if ($request->has('category_id')) {
                $genders->where('product_names.category_id', $request->category_id);
            }
            $genders = $genders->get();
            foreach ($genders as $gender) {
                $categories = $query->clone()->select('categories.id as category_id', 'categories.name as category_name')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->where('sizes.size_type_id', $size_type->id)->where('categories.active', true)->orderBy('categories.name')->get();
                foreach ($categories as $category) {
                    $category->gender_id = $gender->id;
                    $category->size_type_id = $size_type->id;
                    $category->brand_id = -1;
                }
                $gender->categories = $categories;
                $size_type->groups[] = $gender;
            }
            $brands = $query->clone()->select('brands.id as brand_id', 'brands.name')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->where('sizes.size_type_id', $size_type->id)->orderBy('brands.name')->get();
            $size_type->groups[] = (object)[
                'id' => -1,
                'name' => 'Marcas',
                'categories' => $brands,
            ];
        }
        return [
            'message' => 'Clasificación de productos',
            'payload' => [
                'data' => $size_types,
            ],
        ];
    }
}
