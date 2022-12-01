<?php

namespace App\Http\Controllers;

use App\Models\MovementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementTypeController extends Controller
{
    public function index(Request $request)
    {
        $active = (int)$request->active ?? true;
        return [
            'message' => 'Lista de tipos de movimientos',
            'payload' => [
                'data' => DB::table('movement_types')->select('id', 'name', 'icon', 'code', 'entry')->where('active', $active)->orderBy('order')->get(),
            ],
        ];
    }

    public function show(MovementType $movement_type)
    {
        return [
            'message' => 'Tipo de movimientos',
            'payload' => $movement_type,
        ];
    }
}
