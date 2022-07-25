<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Person;
use App\Http\Resources\SupplierResource;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('combo')) {
            return [
                'message' => 'Lista de proveedores',
                'payload' => [
                    'data' => DB::table('suppliers')->select('suppliers.id', 'people.name')->leftJoin('people', 'people.id', '=', 'suppliers.person_id')->where('suppliers.active', '=', true)->where('suppliers.deleted_at', '=', null)->orderBy('people.name')->get(),
                ],
            ];
        }

        $query = DB::table('suppliers')->select('suppliers.id', 'suppliers.active', 'suppliers.person_id', 'people.name', 'people.document', 'people.document_type_id', 'people.address', 'people.email', 'people.phone', 'people.city_id', 'cities.name as city_name', 'cities.code as city_code', 'document_types.name as document_type_name', 'document_types.code as document_type_code')->leftJoin('people', 'people.id', '=', 'suppliers.person_id')->leftJoin('cities', 'people.city_id', '=', 'cities.id')->leftJoin('document_types', 'people.document_type_id', '=', 'document_types.id')->where('suppliers.deleted_at', '=', null);
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
                    return $q->orWhere(DB::raw('upper(people.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.document)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(people.email)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('cast(people.phone AS CHAR)'), 'like', '%'.$request->search.'%')->orWhere(DB::raw('upper(cities.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(cities.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(document_types.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(document_types.code)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de proveedores',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreSupplierRequest $request)
    {
        try {
            DB::beginTransaction();
            $person = Person::create($request->only('name', 'document', 'document_type_id', 'address', 'email', 'phone', 'city_id'));
            $person->supplier()->create();
            DB::commit();
            return [
                'message' => 'Proveedor registrado',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al registrar proveedor',
            ];
        }
    }

    public function show(Supplier $supplier)
    {
        return [
            'message' => 'Datos del proveedor',
            'payload' => [
                'supplier' => new SupplierResource($supplier),
            ]
        ];
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        try {
            DB::beginTransaction();
            $supplier->person()->update($request->only('name', 'document', 'document_type_id', 'address', 'email', 'phone', 'city_id'));
            $supplier->update($request->only('active'));
            DB::commit();
            return [
                'message' => 'Datos de proveedor actualizados',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al actualizar',
            ];
        }
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->person->delete();
        $supplier->delete();
        return [
            'message' => 'Registro eliminado',
        ];
    }
}
