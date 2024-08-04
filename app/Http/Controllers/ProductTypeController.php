<?php

namespace App\Http\Controllers;

use App\Models\SizeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $size_types = DB::table('size_types')->select('id', 'name')->whereIn('id', function ($query) {
            $query->select('sizes.size_type_id')->distinct()->from('products')->leftJoin('sizes', 'products.size_id', '=', 'sizes.id');
        })->orderBy('order')->get();
        foreach ($size_types as $size_type) {
            $size_type->groups = [];
            $genders = DB::table('genders')->select('id', 'name')->whereIn('id', function ($query) use ($size_type) {
                $query->select('products.gender_id')->distinct()->from('products')->leftJoin('sizes', 'products.size_id', '=', 'sizes.id')->where('sizes.size_type_id', $size_type->id);
            })->orderBy('order')->get();
            foreach ($genders as $gender) {
                $categories = DB::table('categories')->select('id', 'name')->whereIn('id', function ($query) use ($size_type, $gender) {
                    $query->select('product_names.category_id')->distinct()->from('products')->leftJoin('product_names', 'products.product_name_id', '=', 'product_names.id')->leftJoin('sizes', 'products.size_id', '=', 'sizes.id')->where('sizes.size_type_id', $size_type->id)->where('products.gender_id', $gender->id);
                })->where('active', true)->orderBy('name')->get();
                foreach ($categories as $category) {
                    $category->gender_id = $gender->id;
                    $category->size_type_id = $size_type->id;
                }
                $gender->categories = $categories;
                $size_type->groups[] = $gender;
            }
        }
        return [
            'message' => 'Clasificación de productos',
            'payload' => [
                'data' => $size_types,
            ],
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
    public function show($id)
    {
        //
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
}
