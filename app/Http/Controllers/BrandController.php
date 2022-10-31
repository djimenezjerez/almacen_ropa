<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de marcas',
            'payload' => [
                'data' => DB::table('brands')->select('id', 'name')->orderBy('name')->get(),
            ],
        ];
    }

    public function show(Brand $brand)
    {
        return [
            'message' => 'Detalle de marca',
            'payload' => $brand,
        ];
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::whereName($request->name)->exists();
        if ($brand) {
            return response()->json([
                'message' => 'Error al guardar la marca',
                'errors' => [
                    'name' => ['La marca ya existe']
                ]
            ], 422);
        } else {
            $brand = Brand::create([
                'name' => $request->name,
            ]);
            return [
                'message' => 'Marca registrada',
                'brand' => $brand,
            ];
        }
    }
}
