<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $statistics = [];

        if ($user->can('USUARIOS')) {
            $statistics[] = [
                'title' => 'Usuarios',
                'color' => 'amber darken-2',
                'icon' => 'mdi-account',
                'link' => 'users',
                'total' => DB::table('users')->count(),
            ];
        }

        return [
            'message' => 'Estadísticas',
            'payload' => [
                'data' => $statistics,
            ],
        ];
    }
}
