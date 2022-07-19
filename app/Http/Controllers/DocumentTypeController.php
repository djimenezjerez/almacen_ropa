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
                'data' => DB::table('document_types')->orderBy('order')->get(),
            ],
        ];
    }
}
