<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\WarehouseRequest;

class RoleController extends Controller
{
    public function index(WarehouseRequest $request)
    {
        return [
            'message' => 'Lista de roles',
            'payload' => [
                'data' => DB::table('roles')->select('id', 'display_name')->where('warehouse', $request->warehouse)->orWhereNull('warehouse')->orderBy('order')->get(),
            ],
        ];
    }
}
