<?php

namespace App\Http\Controllers;

use App\Models\SizeType;
use App\Http\Requests\StoreSizeTypeRequest;
use App\Http\Requests\UpdateSizeTypeRequest;
use Illuminate\Support\Facades\DB;

class SizeTypeController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de tipos de tallas',
            'payload' => [
                'data' => DB::table('size_types')->select('id', 'name')->orderBy('order')->get(),
            ],
        ];
    }
}
