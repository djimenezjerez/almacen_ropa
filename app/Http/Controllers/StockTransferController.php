<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Requests\UpdateStockTransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('stock_transfers')->select('stock_transfers.*', 'origin_person_stores.name as origin_store_name', 'destiny_person_stores.name as destiny_store_name', 'origin_warehouses.name as origin_warehouse_name', 'destiny_warehouses.name as destiny_warehouse_name', 'user_person.name as user_name')->leftJoin('stores as origin_stores', 'origin_stores.id', '=', 'stock_transfers.origin_store_id')->leftJoin('stores as destiny_stores', 'destiny_stores.id', '=', 'stock_transfers.destiny_store_id')->leftJoin('warehouses as origin_warehouses', 'origin_warehouses.id', '=', 'stock_transfers.origin_warehouse_id')->leftJoin('warehouses as destiny_warehouses', 'destiny_warehouses.id', '=', 'stock_transfers.destiny_warehouse_id')->leftJoin('people as origin_person_stores', 'origin_person_stores.id', '=', 'origin_stores.person_id')->leftJoin('people as destiny_person_stores', 'destiny_person_stores.id', '=', 'destiny_stores.person_id')->leftJoin('users', 'users.id', '=', 'stock_transfers.user_id')->leftJoin('people as user_person', 'user_person.id', '=', 'users.person_id');

        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('stock_transfers.created_at', 'DESC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(origin_person_stores.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(destiny_person_stores.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(origin_warehouses.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(destiny_warehouses.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(user_person.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        return [
            'message' => 'Lista de transferencias de stock',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreStockTransferRequest $request)
    {
        //
    }

    public function show(StockTransfer $stockTransfer)
    {
        //
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        //
    }
}
