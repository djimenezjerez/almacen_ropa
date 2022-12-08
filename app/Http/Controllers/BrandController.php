<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de marcas',
                'payload' => [
                    'data' => DB::table('brands')->select('id', 'name')->orderBy('name')->get(),
                ],
            ];
        }

        $query = DB::table('brands');
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('name');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de marcas',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
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

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
