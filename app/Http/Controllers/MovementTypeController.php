<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MovementTypeController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de ciudades',
            'payload' => [
                'data' => DB::table('movement_types')->select('id', 'name', 'code', 'entry')->where('active', true)->orderBy('order')->get(),
            ],
        ];
    }
}
