<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Http\Requests\StoreColorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de colores',
                'payload' => [
                    'data' => DB::table('colors')->select('id', 'name')->orderBy('name')->get(),
                ],
            ];
        }

        $query = DB::table('colors');
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
            'message' => 'Lista de colores',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function show(Color $color)
    {
        return [
            'message' => 'Detalle de color',
            'payload' => $color,
        ];
    }

    public function store(StoreColorRequest $request)
    {
        $color = Color::whereName($request->name)->exists();
        if ($color) {
            return response()->json([
                'message' => 'Error al guardar el color',
                'errors' => [
                    'name' => ['El color ya existe']
                ]
            ], 422);
        } else {
            $color = Color::create([
                'name' => $request->name,
            ]);
            return [
                'message' => 'Color registrado',
                'color' => $color,
            ];
        }
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
