<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use Illuminate\Support\Facades\DB;

class SizeController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de tallas',
            'payload' => [
                'data' => DB::table('sizes')->select('id', 'name')->where('deleted_at', '=', null)->orderBy('name')->get(),
            ],
        ];
    }
}
