<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Person;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de proveedores',
                'payload' => [
                    'data' => DB::table('clients')->select('clients.id', 'people.name')->leftJoin('people', 'people.id', '=', 'clients.person_id')->where('clients.active', '=', true)->where('clients.deleted_at', null)->orderBy('people.name')->get(),
                ],
            ];
        }

        $query = DB::table('clients')->select('clients.id', 'clients.active', 'clients.person_id', 'people.name', 'people.document', 'people.document_type_id', 'people.address', 'people.email', 'people.phone', 'people.city_id', 'cities.name as city_name', 'cities.code as city_code', 'document_types.name as document_type_name', 'document_types.code as document_type_code')->leftJoin('people', 'people.id', '=', 'clients.person_id')->leftJoin('cities', 'people.city_id', '=', 'cities.id')->leftJoin('document_types', 'people.document_type_id', '=', 'document_types.id')->where('clients.deleted_at', null);
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
                    return $q->orWhere(DB::raw('upper(people.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.document)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.email)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('cast(people.phone AS CHAR)'), 'like', '%'.$request->search.'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(document_types.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(document_types.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
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
            $person = Person::create($request->only('name', 'document', 'document_type_id', 'address', 'email', 'phone', 'city_id'));
            $person->client()->create();
            DB::commit();
            return [
                'message' => 'Cliente registrado',
            ];
        } catch(Exception) {
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
        try {
            DB::beginTransaction();
            $client->person()->update($request->only('name', 'document', 'document_type_id', 'address', 'email', 'phone', 'city_id'));
            $client->update($request->only('active'));
            DB::commit();
            return [
                'message' => 'Datos de cliente actualizados',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al actualizar',
            ];
        }
    }

    public function destroy(Client $client)
    {
        $client->person->delete();
        $client->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
