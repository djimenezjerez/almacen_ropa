<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $query = DB::table('model_has_roles')->select('users.id as user_id', 'people.id as person_id', 'people.name as person_name', 'roles.id as role_id', 'roles.display_name as role_display_name', 'model_has_roles.store_id')->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')->leftJoin('users', 'users.id', '=', 'model_has_roles.model_id')->leftJoin('people', 'people.id', '=', 'users.person_id')->where('model_type', '=', 'App\Models\User')->where('store_id', $store->id)->where('model_has_roles.model_id', '!=', auth()->user()->id);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('people.name');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(people.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(roles.display_name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de empleados',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreEmployeeRequest $request, Store $store)
    {
        DB::table('model_has_roles')->updateOrInsert(
            [
                'model_id' => $request->user_id,
                'store_id' => $store->id,
                'model_type' => 'App\Models\User',
            ], [
                'role_id' => $request->role_id,
            ]
        );
        return [
            'message' => 'Operación exitosa',
        ];
    }

    public function destroy($store_id, $user_id)
    {
        DB::table('model_has_roles')->where('model_type', '=', 'App\Models\User')->where('store_id', $store_id)->where('model_id', $user_id)->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
