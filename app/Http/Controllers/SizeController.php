<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        return [
            'message' => 'Lista de tallas',
            'payload' => [
                'data' => DB::table('sizes')->select('id', 'name', 'numeric')->where('size_type_id', '=', $request->size_type_id)->orderBy('name')->get(),
            ],
        ];
    }
}
