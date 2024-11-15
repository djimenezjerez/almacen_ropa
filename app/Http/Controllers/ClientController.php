<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Client;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ClientResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de clientes',
                'payload' => [
                    'data' => DB::table('clients')->select('clients.id', 'people.name', 'people.email', 'people.document', 'document_types.code as document_type_code')->leftJoin('users', 'users.id', '=', 'clients.user_id')->leftJoin('people', 'people.id', '=', 'users.person_id')->leftJoin('document_types', 'document_types.id', '=', 'people.document_type_id')->where('clients.active', '=', true)->where('clients.deleted_at', null)->orderBy('people.name')->get(),
                ],
            ];
        }

        $query = DB::table('clients')->select('clients.id', 'clients.active', 'clients.user_id', 'people.name', 'people.email', 'people.document', 'people.document_type_id', 'people.address', 'people.phone', 'people.city_id', 'cities.name as city_name', 'cities.code as city_code', 'document_types.name as document_type_name', 'document_types.code as document_type_code')->leftJoin('users', 'users.id', '=', 'clients.user_id')->leftJoin('people', 'people.id', '=', 'users.person_id')->leftJoin('cities', 'people.city_id', '=', 'cities.id')->leftJoin('document_types', 'people.document_type_id', '=', 'document_types.id')->where('clients.deleted_at', null);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('people.name');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function ($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(people.name)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('upper(people.document)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('upper(people.email)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('cast(people.phone AS CHAR)'), 'like', '%' . $request->search . '%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('upper(cities.code)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('upper(document_types.name)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%')->orWhere(DB::raw('upper(document_types.code)'), 'like', '%' . trim(mb_strtoupper($request->search)) . '%');
                });
            }
        }
        return [
            'message' => 'Lista de clientes',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreClientRequest $request)
    {
        try {
            DB::beginTransaction();
            $person = Person::create($request->only('name', 'email', 'document', 'document_type_id', 'address', 'phone', 'city_id'));
            $person->user()->create([
                'username' => $request->email,
                'password' => $request->password,
            ]);
            $person->user->client()->create();
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'model_id' => $person->user->id,
                    'store_id' => null,
                    'model_type' => 'App\Models\User',
                ],
                [
                    'role_id' => Role::where('name', 'CLIENTE')->first()->id,
                ]
            );
            DB::commit();
            return [
                'message' => 'Cliente registrado',
                'client' => new ClientResource($person->user->client),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error al registrar cliente',
            ];
        }
    }

    public function show(Client $client)
    {
        return [
            'message' => 'Datos del cliente',
            'payload' => [
                'client' => new ClientResource($client),
            ]
        ];
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $auth = Auth::user();
        if ($auth->client->id != $client->id && !$auth->can('CLIENTES')) {
            return abort(401, 'This action is unauthorized');
        }
        $user = DB::table('users')->where(DB::raw('upper(username)'), trim(mb_strtoupper($request->email)))->first();
        if ($user) {
            if ($client->user_id != $user->id) {
                return response()->json([
                    'message' => 'Error al actualizar',
                    'errors' => [
                        'email' => ['El email ya está en uso']
                    ]
                ], 403);
            }
        }
        try {
            DB::beginTransaction();
            $client->user->person->update($request->only('name', 'email', 'document', 'document_type_id', 'address', 'phone', 'city_id'));
            $client->user->update([
                'username' => $request->email,
            ]);
            if ($request->has('password') && $request->password != null && $request->password != '') {
                $client->user->update([
                    'password' => $request->password,
                ]);
            }
            $client->update($request->only('active'));
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'model_id' => $client->user->id,
                    'store_id' => null,
                    'model_type' => 'App\Models\User',
                ],
                [
                    'role_id' => Role::where('name', 'CLIENTE')->first()->id,
                ]
            );
            DB::commit();
            return [
                'message' => 'Datos de cliente actualizados',
            ];
        } catch (Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al actualizar',
                'client' => new ClientResource($client),
            ];
        }
    }

    public function destroy(Client $client)
    {
        $client->user->person->delete();
        $client->user->update([
            'username' => null
        ]);
        $client->user->delete();
        $client->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
