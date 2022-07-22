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
                'total' => DB::table('users')->where('deleted_at', '=', null)->count(),
            ];
        }
        if ($user->can('TIENDAS')) {
            $statistics[] = [
                'title' => 'Tiendas',
                'color' => 'grey darken-1',
                'icon' => 'mdi-store',
                'link' => 'stores',
                'total' => DB::table('stores')->where('deleted_at', '=', null)->count(),
            ];
        }
        if ($user->can('ALMACENES')) {
            $statistics[] = [
                'title' => 'Almacenes',
                'color' => 'blue accent-2',
                'icon' => 'mdi-package-variant',
                'link' => 'warehouses',
                'total' => DB::table('warehouses')->where('deleted_at', '=', null)->count(),
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
