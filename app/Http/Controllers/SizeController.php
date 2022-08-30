<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Http\Requests\StoreSizeRequest;
use Illuminate\Support\Facades\DB;

class SizeController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de tallas',
            'payload' => [
                'data' => DB::table('sizes')->select('id', 'name', 'size_type_id', 'numeric')->orderBy('numeric')->orderBy('id')->get(),
            ],
        ];
    }

    public function store(StoreSizeRequest $request)
    {
        if ($request->numeric) {
            if (!is_numeric($request->name)) {
                return response()->json([
                    'message' => 'Error al guardar la talla',
                    'errors' => [
                        'name' => ['La talla debe ser de tipo numeral']
                    ]
                ], 422);
            }
            $size = floatval($request->name);
            if ($size <= 0 or $size > 300) {
                return response()->json([
                    'message' => 'Error al guardar la talla',
                    'errors' => [
                        'name' => ['La talla debe ser un número mayor a 0']
                    ]
                ], 422);
            }
        } else {
            if (is_numeric($request->name)) {
                return response()->json([
                    'message' => 'Error al guardar la talla',
                    'errors' => [
                        'name' => ['La talla no debe ser de tipo numeral']
                    ]
                ], 422);
            }
        }

        $size = Size::whereName($request->name)->whereSizeTypeId($request->size_type_id)->whereNumeric($request->numeric)->exists();
        if ($size) {
            return response()->json([
                'message' => 'Error al guardar la talla',
                'errors' => [
                    'name' => ['La talla ya existe']
                ]
            ], 422);
        } else {
            $size = Size::create([
                'name' => $request->name,
                'size_type_id' => $request->size_type_id,
                'numeric' => $request->numeric,
            ]);
            return [
                'message' => 'Talla registrada',
                'size' => $size,
            ];
        }
    }
}
