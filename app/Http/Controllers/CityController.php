<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de ciudades',
            'payload' => [
                'data' => DB::table('cities')->select('id', 'name', 'code')->orderBy('order')->get(),
            ],
        ];
    }
}
