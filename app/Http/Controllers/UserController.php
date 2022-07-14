<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de usuarios',
                'payload' => [
                    'data' => DB::table('users')->select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
                ],
            ];
        }

        /** @var \App\Models\User */
        $auth_user = Auth::user();
        $query = DB::table('users')->select('users.*', 'roles.name as role_name', 'cities.name as city_name', 'cities.code as city_code')->leftJoin('roles', 'users.role_id', '=', 'roles.id')->leftJoin('cities', 'users.city_id', '=', 'cities.id')->where('users.id', '!=', $auth_user->id);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('users.first_name', 'ASC');
        }

        $trashed = null;
        if ($request->has('search')) {
            if ($request->search != '') {
                if (in_array(trim(mb_strtoupper($request->search)), ['INACTIVO', 'ACTIVO'])) {
                    if (trim(mb_strtoupper($request->search)) == 'INACTIVO') {
                        $query->where('deleted_at', '!=', null);
                        $trashed = true;
                    } else {
                        $trashed = false;
                    }
                } else {
                    $query->where(function($q) use ($request) {
                        return $q->orWhere(DB::raw('upper(users.first_name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(users.last_name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(users.identity_card)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere('users.identity_card', 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(roles.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('cast(users.phone AS CHAR)'), 'like', '%'.$request->search.'%')->orWhere('users.email', 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                    });
                }
            }
        }
        if ($trashed === false) {
            $query->where('deleted_at', '=', null);
        }
        return [
            'message' => 'Lista de usuarios',
            'payload' => UserResource::collection($query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1))->resource,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $request->merge([
            'password' => $request->identity_card,
        ]);
        $user = User::create($request->all());
        $user->syncRoles([$user->role_id]);
        return [
            'message' => 'Usuario registrado',
            'payload' => [
                'user' => new UserResource($user),
            ]
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        /** @var \App\Models\User */
        $auth_user = Auth::user();
        if ($auth_user->id == $user->id || $auth_user->can('USUARIOS')) {
            $user->role_name = $user->role->name;
            $user->city_name = $user->city->name;
            return [
                'message' => 'Datos de usuario',
                'payload' => [
                    'user' => new UserResource($user),
                ]
            ];
        }
        abort(403, 'Prohibido');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->has('identity_card')) {
            if (DB::table('users')->where('identity_card', '=', $request->identity_card)->where('id', '!=', $user->id)->exists()) {
                return response()->json([
                    'message' => 'Nombre de usuario inválido',
                    'errors' => [
                        'identity_card' => ['El nombre de usuario ya existe']
                    ]
                ], 400);
            }
        }
        if ($request->has('identity_card')) {
            if (DB::table('users')->where('identity_card', '=', $request->identity_card)->where('id', '!=', $user->id)->exists()) {
                return response()->json([
                    'message' => 'Documento de identidad inválido',
                    'errors' => [
                        'identity_card' => ['El documento de identidad ya existe']
                    ]
                ], 400);
            }
        }
        if (auth()->user()->id == $user->id) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'message' => 'Credenciales inválidas',
                    'errors' => [
                        'old_password' => ['Contraseña actual incorrecta']
                    ]
                ], 400);
            }
        }
        $user->update($request->all());
        if ($request->has('role_id')) {
            $user->syncRoles([$request->role_id]);
        }
        return [
            'message' => 'Datos de usuario actualizados',
            'payload' => [
                'user' => new UserResource($user),
            ]
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return [
            'message' => 'Usuario desactivado',
        ];
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function restore($user)
    {
        User::withTrashed()->where('id', $user)->restore();
        return [
            'message' => 'Usuario reactivado',
        ];
    }

}
