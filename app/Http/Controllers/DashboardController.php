<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
        $permissions = $role->permissions->pluck('name');

        $statistics = [];

        if ($permissions->contains('USUARIOS')) {
            $statistics[] = [
                'title' => 'Usuarios',
                'color' => 'amber darken-2',
                'icon' => 'mdi-account',
                'link' => 'users',
                'total' => DB::table('users')->where('deleted_at', null)->count(),
            ];
        }
        if ($permissions->contains('TIENDAS')) {
            $statistics[] = [
                'title' => 'Tiendas',
                'color' => 'grey darken-1',
                'icon' => 'mdi-store',
                'link' => 'stores?warehouse=0',
                'total' => DB::table('stores')->where('warehouse', false)->where('deleted_at', null)->count(),
            ];
        }
        if ($permissions->contains('ALMACENES')) {
            $statistics[] = [
                'title' => 'Almacenes',
                'color' => 'blue accent-2',
                'icon' => 'mdi-package-variant',
                'link' => 'stores?warehouse=1',
                'total' => DB::table('stores')->where('warehouse', true)->where('deleted_at', null)->count(),
            ];
        }
        if ($permissions->contains('PROVEEDORES')) {
            $statistics[] = [
                'title' => 'Proveedores',
                'color' => 'indigo',
                'icon' => 'mdi-van-utility',
                'link' => 'suppliers',
                'total' => DB::table('suppliers')->where('deleted_at', null)->count(),
            ];
        }
        if ($permissions->contains('CLIENTES')) {
            $statistics[] = [
                'title' => 'Clientes',
                'color' => 'amber darken-4',
                'icon' => 'mdi-account-cash',
                'link' => 'clients',
                'total' => DB::table('clients')->where('deleted_at', null)->count(),
            ];
        }
        if ($permissions->contains('PRODUCTOS')) {
            $statistics[] = [
                'title' => 'Productos',
                'color' => 'green darken-3',
                'icon' => 'mdi-tshirt-crew',
                'link' => 'products',
                'total' => DB::table('product_names')->count(),
            ];
        }
        if ($permissions->contains('VENTAS') && !$user->store->warehouse) {
            $date_from = Carbon::now()->startOfDay()->toDateTimeString();
            $date_to = Carbon::now()->endOfDay()->toDateTimeString();
            $sells = DB::table('movements')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->where('movement_types.code', 'SELL')->whereDate('movements.created_at', '>=', $date_from)->whereDate('movements.created_at', '<=', $date_to)->where('movements.from_store_id', $user->remember_store_id);
            if ($role->name != 'ADMINISTRADOR') {
                $sells->where('movements.user_id', $user->id);
            }
            $sells->groupBy('movements.from_store_id');
            $sells = $sells->count();
            $returns = DB::table('movements')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->where('movement_types.code', 'CANCEL_SELL')->whereDate('movements.created_at', '>=', $date_from)->whereDate('movements.created_at', '<=', $date_to)->where('movements.to_store_id', $user->remember_store_id);
            if ($role->name != 'ADMINISTRADOR') {
                $returns->where('movements.user_id', $user->id);
            }
            $returns->groupBy('movements.to_store_id')->get();
            $returns = $returns->count();
            $statistics[] = [
                'title' => 'Ventas del día',
                'color' => 'green',
                'icon' => 'mdi-currency-usd',
                'link' => 'sells',
                'total' => $sells - $returns,
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
