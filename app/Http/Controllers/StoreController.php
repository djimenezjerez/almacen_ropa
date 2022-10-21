<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Person;
use App\Models\DocumentType;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de tiendas',
                'payload' => [
                    'data' => DB::table('stores')->select('stores.id', 'people.name')->leftJoin('people', 'people.id', '=', 'stores.person_id')->where('stores.active', '=', true)->where('stores.deleted_at', '=', null)->orderBy('people.name')->get(),
                ],
            ];
        }

        $query = DB::table('stores')->select('stores.id', 'stores.active', 'stores.person_id', 'people.name', 'people.document', 'people.document_type_id', 'people.address', 'people.email', 'people.phone', 'people.city_id', 'cities.name as city_name', 'cities.code as city_code')->leftJoin('people', 'people.id', '=', 'stores.person_id')->leftJoin('cities', 'people.city_id', '=', 'cities.id')->where('stores.deleted_at', '=', null);
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
                    return $q->orWhere(DB::raw('upper(people.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.document)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.email)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('cast(people.phone AS CHAR)'), 'like', '%'.$request->search.'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }

logger($query->toSql());
logger($query->getBindings());

        return [
            'message' => 'Lista de tiendas',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreStoreRequest $request)
    {
        try {
            $document_type = DocumentType::whereCode('NIT')->first();
            DB::beginTransaction();
            $person = Person::create($request->merge([
                'document_type_id' => $document_type->id,
            ])->only('name', 'document', 'document_type_id', 'address', 'email', 'phone', 'city_id'));
            $store = $person->store()->create();
            $role = Role::where('name', 'ADMINISTRADOR')->first();
            $user = auth()->user();
            $query = DB::table('model_has_roles')->where('model_type', '=', 'App\Models\User')->where('model_id', $user->id)->where('store_id', $store->id);
            $count = $query->where('role_id', $role->id)->count();
            if ($count == 0) {
                DB::table('model_has_roles')->insert([
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'store_id' => $store->id,
                    'role_id' => $role->id,
                ]);
            } else {
                $query->update([
                    'role_id' => $role->id,
                ]);
            }
            DB::commit();
            return [
                'message' => 'Tienda registrada',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al registrar tienda',
            ];
        }
    }

    public function show(Store $store)
    {
        return [
            'message' => 'Datos de la tienda',
            'payload' => [
                'store' => new StoreResource($store),
            ]
        ];
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        try {
            DB::beginTransaction();
            $store->person()->update($request->only('name', 'document', 'address', 'email', 'phone', 'city_id'));
            $store->update($request->only('active'));
            DB::commit();
            return [
                'message' => 'Datos de tienda actualizados',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al actualizar',
            ];
        }
    }

    public function destroy(Store $store)
    {
        $store->person->delete();
        $store->delete();
        $query = DB::table('model_has_roles')->where('store_id', $store->id)->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
