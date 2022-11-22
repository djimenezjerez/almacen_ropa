<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\MovementType;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Store;
use App\Models\Warehouse;
use App\Http\Requests\StoreMovementRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRequest;

class MovementController extends Controller
{
    public function index(StoreRequest $request)
    {
        $query = DB::table('movements')->select('movements.*', 'movement_types.code as movement_type_code', 'movement_types.name as movement_type_name', 'person_user.name as user_name', 'person_from_store.name as from_store_name', 'person_to_store.name as to_store_name')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->leftJoin('users', 'users.id', '=', 'movements.user_id')->leftJoin('stores as from_store', 'from_store.id', '=', 'movements.from_store_id')->leftJoin('stores as to_store', 'to_store.id', '=', 'movements.to_store_id')->leftJoin('people as person_user', 'person_user.id', '=', 'users.person_id')->leftJoin('people as person_from_store', 'person_from_store.id', '=', 'from_store.person_id')->leftJoin('people as person_to_store', 'person_to_store.id', '=', 'to_store.person_id')->where('movement_types.active', 1)->where(function($q) use ($request) {
            return $q->orWhere('from_store_id', (int)$request->store_id)->orWhere('to_store_id', (int)$request->store_id);
        });
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('movements.created_at', 'DESC');
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(movements.comment)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(movement_types.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_user.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_from_store.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_to_store.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhereDate('created_at', '=', $request->search);
                });
            }
        }
        return [
            'message' => 'Lista de clientes',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreMovementRequest $request)
    {
        if ($request->from_store_id == null && $request->to_store_id == null) {
            return response()->json([
                'message' => 'Error al registrar',
                'errors' => [
                    'from_store_id' => ['Debe definir el origen'],
                    'to_store_id' => ['Debe definir el destino'],
                ]
            ], 422);
        }
        $movement_type = MovementType::find($request->movement_type_id);
        $movement = new Movement();
        $movement->comment = $request->comment;
        $movement->movement_type()->associate($movement_type);
        $movement->user()->associate(auth()->user());
        $movement->from_store_id = $request->from_store_id;
        $movement->to_store_id = $request->to_store_id;
        if ($movement_type->code == 'SELL') {
            $movement->client_id = $request->client_id;
        } else {
            $movement->client_id = null;
        }
        try {
            DB::beginTransaction();
            $movement->save();
            foreach ($request->details as $detail) {
                $product = Product::find((int)$detail['id']);
                $movement_detail = new MovementDetail();
                $movement_detail->product()->associate($product);
                switch ($movement_type->code) {
                    case 'ENTRY':
                    case 'CANCEL_SELL':
                        $movement_detail->stock = (int)$detail['stock'];
                        $movement_detail->store_id = $movement->to_store_id;
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        $product->stock += $movement_detail->stock;
                        $product->save();
                        break;
                    case 'ADJUSTMENT':
                    case 'SELL':
                        $movement_detail->stock = -1 * (int)$detail['stock'];
                        $movement_detail->store_id = $movement->from_store_id;
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        $product->stock -= $movement_detail->stock;
                        $product->save();
                        break;
                    case 'TRANSFER':
                        $movement_detail->stock = -1 * (int)$detail['stock'];
                        $movement_detail->store_id = $movement->from_store_id;
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        $movement_detail->stock = (int)$detail['stock'];
                        $movement_detail->store_id = $movement->to_store_id;
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        break;
                    default:
                        return response()->json([
                            'message' => 'Error al registrar',
                            'errors' => [
                                'movement_type' => ['Movimiento inválido']
                            ]
                        ], 422);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Movimiento registrado',
                'errors' => []
            ]);
        } catch(Exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar',
                'errors' => [
                    'movement_type' => ['Movimiento inválido']
                ]
            ], 500);
        }
    }

    public function show(Movement $movement)
    {
        //
    }

    public function destroy(Movement $movement)
    {
        //
    }
}
