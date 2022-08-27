<?php

namespace App\Http\Controllers;

use App\Models\ProductName;
use Illuminate\Support\Facades\DB;

class ProductNameController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de nombres de productos',
            'payload' => [
                'data' => DB::table('product_names')->select('id', 'name')->orderBy('name')->get(),
            ],
        ];
    }
}
