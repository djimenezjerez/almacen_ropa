<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Person;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de almacenes',
                'payload' => [
                    'data' => DB::table('warehouses')->select('warehouses.id', 'person_warehouses.name')->leftJoin('people as person_warehouses', 'person_warehouses.id', '=', 'warehouses.person_id')->where('warehouses.active', '=', true)->where('warehouses.deleted_at', null)->orderBy('person_warehouses.name')->get(),
                ],
            ];
        }

        $query = DB::table('warehouses')->select('warehouses.id', 'warehouses.active', 'warehouses.person_id', 'warehouses.user_id', 'person_users.name as user_name', 'person_warehouses.name', 'person_warehouses.address', 'person_warehouses.phone', 'person_warehouses.city_id', 'cities.name as city_name', 'cities.code as city_code')->leftJoin('users', 'users.id', '=', 'warehouses.user_id')->leftJoin('people as person_users', 'person_users.id', '=', 'users.person_id')->leftJoin('people as person_warehouses', 'person_warehouses.id', '=', 'warehouses.person_id')->leftJoin('cities', 'person_warehouses.city_id', '=', 'cities.id')->where('warehouses.deleted_at', null);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('person_warehouses.name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(person_users.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_warehouses.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('cast(person_warehouses.phone AS CHAR)'), 'like', '%'.$request->search.'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de almacenes',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreWarehouseRequest $request)
    {
        try {
            DB::beginTransaction();
            $person = Person::create($request->only('name', 'address', 'city_id'));
            $warehouse = $person->warehouse()->create($request->only('user_id'));
            DB::commit();
            return [
                'message' => 'Almacén registrada',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al registrar almacén',
            ];
        }
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        try {
            DB::beginTransaction();
            $warehouse->person()->update($request->only('name', 'address', 'city_id'));
            $warehouse->update($request->only('user_id', 'active'));
            DB::commit();
            return [
                'message' => 'Datos de almacén actualizados',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al actualizar',
            ];
        }
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->person->delete();
        $warehouse->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
