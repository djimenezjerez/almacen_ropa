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
                'data' => DB::table('sizes')->select('id', 'name', 'size_type_id', 'numeric')->orderBy('numeric')->orderBy('order')->orderBy('id')->get(),
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
            if ($request->order) {
                $order = $request->order;
            } else {
                if (Size::whereNumeric($request->numeric)->count() > 0) {
                    if ($request->numeric) {
                        $sizes = Size::select('order')->selectRaw('CAST(name AS INTEGER) as name')->whereSizeTypeId($request->size_type_id)->whereNumeric($request->numeric)->orderBy('name')->get();
                        $size = $sizes->where('name', '>', (int)$request->name)->first();
                        if ($size) {
                            $order = $size->order;
                        } else {
                            $order = Size::whereSizeTypeId($request->size_type_id)->whereNumeric($request->numeric)->max('order') + 1;
                        }
                    } else {
                        $order = Size::whereSizeTypeId($request->size_type_id)->whereNumeric($request->numeric)->max('order') + 1;
                    }
                } else {
                    $order = 1;
                }
            }
            $size = Size::create([
                'name' => $request->name,
                'size_type_id' => $request->size_type_id,
                'numeric' => $request->numeric,
                'order' => $order,
            ]);
            return [
                'message' => 'Talla registrada',
                'size' => $size,
            ];
        }
    }
}
