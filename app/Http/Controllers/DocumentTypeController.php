<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DocumentTypeController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Lista de tipos de documento',
            'payload' => [
                'data' => DB::table('document_types')->select('id', 'name', 'code')->orderBy('order')->get(),
            ],
        ];
    }
}
