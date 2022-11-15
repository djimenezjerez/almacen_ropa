<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Store;
use App\Models\Warehouse;
use App\Http\Requests\StoreMovementRequest;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreMovementRequest $request)
    {
        $movement = new Movement();
        $movement->comment = $request->comment;
        $movement->user()->associate(auth()->user());
        $from = null;
        $to = null;
        switch ($request->movement_type) {
            case 'entry':
                $to = $request->to_type == 'store' ? Store::find($request->to_id) : Warehouse::find($request->to_id);
                $movement->toable()->associate($to);
                break;
            case 'adjustment':
                $from = $request->from_type == 'store' ? Store::find($request->from_id) : Warehouse::find($request->from_id);
                $movement->fromable()->associate($from);
                break;
            case 'transfer':
                $from = $request->from_type == 'store' ? Store::find($request->from_id) : Warehouse::find($request->from_id);
                $movement->fromable()->associate($from);
                $to = $request->to_type == 'store' ? Store::find($request->to_id) : Warehouse::find($request->to_id);
                $movement->toable()->associate($to);
                break;
            case 'sell':
                $movement->to = null;
                $client = Client::find($request->client_id);
                $movement->client()->associate($client);
                $from = Store::find($request->from_id);
                $movement->fromable()->associate($from);
                break;
            default:
                return response()->json([
                    'message' => 'Error al registrar',
                    'errors' => [
                        'movement_type' => ['Movimiento inválido']
                    ]
                ], 422);
        }
        try {
            DB::beginTransaction();
            $movement->save();
            foreach ($request->details as $detail) {
                $movement_detail = new MovementDetail();
                $movement_detail->product_id = (int)$detail['id'];
                switch ($request->movement_type) {
                    case 'entry':
                        $movement_detail->stock = (int)$detail['stock'];
                        $movement_detail->storable()->associate($to);
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        break;
                    case 'adjustment':
                    case 'sell':
                        $movement_detail->stock = -1 * (int)$detail['stock'];
                        $movement_detail->storable()->associate($from);
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        break;
                    case 'transfer':
                        $movement_detail->stock = -1 * (int)$detail['stock'];
                        $movement_detail->storable()->associate($from);
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        $movement_detail->stock = (int)$detail['stock'];
                        $movement_detail->storable()->associate($to);
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
            return [
                'message' => 'Movimiento registrado',
            ];
        } catch(Exception) {
            DB::rollBack();
            return [
                'message' => 'Error al registrar',
            ];
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
