<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\StoreResource;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index()
    {
        return [
            'message' => 'Tiendas relacionadas al usuario',
            'payload' => [
                'data' => DB::table('model_has_roles')->select('stores.id', 'stores.warehouse', 'people.name as store_name', 'role_id', 'roles.name as role_name')->leftJoin('roles', 'roles.id', '=', 'role_id')->leftJoin('stores', 'stores.id', '=', 'store_id')->leftJoin('people', 'people.id', '=', 'stores.person_id')->where('model_type', 'App\\Models\\User')->where('model_id', auth()->user()->id)->orderBy('stores.warehouse')->orderBy('people.name')->get()
            ],
        ];
    }

    public function store(AuthRequest $request)
    {
        $user = User::whereUsername($request->username)->whereActive(true)->first();
        if ($user) {
            if ($user->access_attempts >= env('MAX_ACCESS_ATTEMPTS', 5)) {
                return response()->json([
                    'message' => 'Error de autenticación',
                    'errors' => [
                        'username' => ['Máximo número de intentos alcanzado']
                    ]
                ], 401);
            } else {
                if (Hash::check($request->password, $user->password)) {
                    if ($request->store_id == null) {
                        return [
                            'message' => 'Tiendas relacionadas al usuario',
                            'payload' => [
                                'stores' => DB::table('model_has_roles')->select('store_id', 'people.name as store_name', 'role_id', 'roles.name as role_name')->leftJoin('roles', 'roles.id', '=', 'role_id')->leftJoin('stores', 'stores.id', '=', 'store_id')->leftJoin('people', 'people.id', '=', 'stores.person_id')->where('model_type', 'App\\Models\\User')->where('model_id', $user->id)->orderBy('stores.warehouse')->orderBy('people.name')->get()
                            ],
                        ];
                    }
                    $store = $user->stores()->whereActive(true)->wherePivot('store_id', (int)$request->store_id)->first();
                    if ($store != null) {
                        $role = $user->roles()->wherePivot('store_id', (int)$request->store_id)->first();
                        $tokens = $user->tokens()->count();
                        $user_has_token = ($tokens == 1);
                        if ($tokens > 1) {
                            $user->tokens()->delete();
                        }
                        if (!$user_has_token) {
                            $token = $user->createToken('api');
                            $user->remember_token = $token->plainTextToken;
                        }
                        $user->remember_role_id = $role->id;
                        $user->remember_store_id = $store->id;
                        $user->access_attempts = 0;
                        $user->save();
                        return [
                            'message' => 'Sesión iniciada',
                            'payload' => [
                                'access_token' => $user->remember_token,
                                'token_type' => 'Bearer',
                                'user' => new UserResource($user),
                                'role' => [
                                    'id' => $role->id,
                                    'name' => $role->name,
                                    'display_name' => $role->display_name,
                                ],
                                'store' => new StoreResource($store),
                                'permissions' => $role->permissions->pluck('name'),
                            ],
                        ];
                    } else {
                        $user->increment('access_attempts');
                        return response()->json([
                            'message' => 'Error de acceso',
                            'errors' => [
                                'store_id' => ['El acceso a la tienda seleccionada está bloqueado']
                            ]
                        ], 401);
                    }
                } else {
                    $user->increment('access_attempts');
                    return response()->json([
                        'message' => 'Credenciales inválidas',
                        'errors' => [
                            'password' => ['Credenciales inválidas']
                        ]
                    ], 401);
                }
            }
        }
        return response()->json([
            'message' => 'Credenciales inválidas',
            'errors' => [
                'username' => ['Credenciales inválidas']
            ]
        ], 401);
    }

    public function destroy()
    {
        $user = auth()->user();
        $user->setRememberToken(null);
        $user->tokens()->delete();
        return [
            'message' => 'Sesión finalizada',
        ];
    }

    public function update(StoreRequest $request)
    {
        $user = auth()->user();
        $store = $user->stores()->whereActive(true)->wherePivot('store_id', (int)$request->store_id)->first();
        if ($store != null) {
            $role = $user->roles()->wherePivot('store_id', (int)$request->store_id)->first();
            $tokens = $user->tokens()->count();
            $user_has_token = ($tokens == 1);
            if ($tokens > 1) {
                $user->tokens()->delete();
            }
            if (!$user_has_token) {
                $token = $user->createToken('api');
                $user->remember_token = $token->plainTextToken;
            }
            $user->remember_role_id = $role->id;
            $user->remember_store_id = $store->id;
            $user->access_attempts = 0;
            $user->save();
            return [
                'message' => 'Cambio de tienda',
                'payload' => [
                    'access_token' => $user->remember_token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user),
                    'role' => [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                    ],
                    'store' => new StoreResource($store),
                    'permissions' => $role->permissions->pluck('name'),
                ],
            ];
        } else {
            $user->increment('access_attempts');
            return response()->json([
                'message' => 'Error de acceso',
                'errors' => [
                    'store_id' => ['El acceso a la tienda seleccionada está bloqueado']
                ]
            ], 401);
        }
    }
}
