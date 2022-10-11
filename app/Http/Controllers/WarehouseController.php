<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
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
                    'data' => DB::table('warehouses')->select('warehouses.id', 'people.name')->leftJoin('people', 'people.id', '=', 'warehouses.person_id')->where('warehouses.active', '=', true)->where('warehouses.deleted_at', '=', null)->orderBy('people.name')->get(),
                ],
            ];
        }

        $query = DB::table('warehouses')->select('warehouses.*', 'people.name as person_name', 'cities.name as city_name')->leftJoin('users', 'users.id', '=', 'warehouses.user_id')->leftJoin('people', 'people.id', '=', 'users.person_id')->leftJoin('cities', 'warehouses.city_id', '=', 'cities.id')->where('warehouses.deleted_at', '=', null);
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('warehouses.name', 'ASC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(warehouses.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(warehouses.address)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
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
        Warehouse::create($request->all());
        return [
            'message' => 'Almacén registrado',
        ];
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->all());
        return [
            'message' => 'Datos de almacén actualizados',
        ];
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
