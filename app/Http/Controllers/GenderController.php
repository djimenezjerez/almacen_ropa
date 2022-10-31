<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Support\Facades\DB;

class GenderController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de géneros',
            'payload' => [
                'data' => DB::table('genders')->select('id', 'name')->orderBy('order')->get(),
            ],
        ];
    }

    public function show(Gender $gender)
    {
        return [
            'message' => 'Detalle de género',
            'payload' => $gender,
        ];
    }
}
