<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de marcas',
            'payload' => [
                'data' => DB::table('brands')->select('id', 'name')->where('deleted_at', '=', null)->orderBy('name')->get(),
            ],
        ];
    }
}
