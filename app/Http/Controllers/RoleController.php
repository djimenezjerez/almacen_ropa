<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            $query = DB::table('roles')->select('id', 'name');
            return [
                'message' => 'Lista de roles',
                'payload' => [
                    'data' => $query->orderBy('id')->get(),
                ],
            ];
        }
        return [
            'message' => 'Lista de roles',
            'payload' => [
                'data' => RoleResource::collection(DB::table('roles')->orderBy('name')->get()),
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($user->hasRole($role) || $user->can('USUARIOS')) {
            return [
                'message' => 'Datos de rol',
                'payload' => [
                    'role' => new RoleResource($role),
                    'permissions' => PermissionResource::collection($role->permissions()->orderBy('id')->get()),
                ],
            ];
        }
        abort(403, 'Prohibido');
    }
}
