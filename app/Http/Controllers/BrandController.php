<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de marcas',
            'payload' => [
                'data' => DB::table('brands')->select('id', 'name')->orderBy('name')->get(),
            ],
        ];
    }
}
