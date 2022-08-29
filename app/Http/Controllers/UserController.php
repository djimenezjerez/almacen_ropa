<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Person;
use App\Models\DocumentType;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de usuarios',
                'payload' => [
                    'data' => DB::table('users')->select('users.id', 'people.name')->leftJoin('people', 'people.id', '=', 'users.person_id')->where('users.deleted_at', '=', null)->orderBy('people.name')->get(),
                ],
            ];
        }

        $auth_user = auth()->user();
        $query = DB::table('users')->select('users.id', 'users.username', 'users.access_attempts', 'users.active', 'users.person_id', 'people.name', 'people.document', 'people.document_type_id', 'people.address', 'people.email', 'people.phone', 'people.city_id', 'cities.name as city_name', 'cities.code as city_code')->leftJoin('people', 'people.id', '=', 'users.person_id')->leftJoin('cities', 'people.city_id', '=', 'cities.id')->where('users.deleted_at', '=', null)->where('users.id', '!=', $auth_user->id);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('people.name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(people.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.document)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.email)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('cast(people.phone AS CHAR)'), 'like', '%'.$request->search.'%')->orWhere('users.username', 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de usuarios',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $document_type = DocumentType::whereCode('CI')->first();
            DB::beginTransaction();
            $person = Person::create($request->merge([
                'document_type_id' => $document_type->id,
            ])->only('name', 'document', 'document_type_id', 'address', 'email', 'phone', 'city_id'));
            $user = $person->user()->create($request->merge([
                'password' => $person->document,
            ])->only('username', 'password'));
            DB::commit();
            return [
                'message' => 'Usuario registrado',
            ];
        } catch(Exception) {
            DB::rollBack();
            abort(500, 'Error al registrar usuario');
        }
    }

    public function show(User $user)
    {
        $auth_user = auth()->user();
        if ($auth_user->id == $user->id || $auth_user->can('USUARIOS')) {
            return [
                'message' => 'Datos de usuario',
                'payload' => [
                    'user' => new UserResource($user),
                ]
            ];
        }
        abort(403, 'Prohibido');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
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

        try {
            DB::beginTransaction();
            $user->person()->update($request->only('name', 'document', 'address', 'email', 'phone', 'city_id'));
            if ($request->password != null && $request->password != '') {
                $user->update($request->only('username', 'password', 'access_attempts', 'active'));
            } else {
                $user->update($request->only('username', 'access_attempts', 'active'));
            }
            DB::commit();
            return [
                'message' => 'Datos de usuario actualizados',
            ];
        } catch(Exception) {
            DB::rollBack();
            abort(500, 'Error al actualizar');
        }
    }

    public function destroy(User $user)
    {
        $user->person->delete();
        $user->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
