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

class MovementController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreMovementRequest $request)
    {
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
