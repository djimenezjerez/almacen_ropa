<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de colores',
            'payload' => [
                'data' => DB::table('colors')->select('id', 'name')->where('deleted_at', '=', null)->orderBy('name')->get(),
            ],
        ];
    }
}
