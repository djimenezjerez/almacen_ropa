<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Http\Requests\StoreColorRequest;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de colores',
            'payload' => [
                'data' => DB::table('colors')->select('id', 'name')->orderBy('name')->get(),
            ],
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
}
