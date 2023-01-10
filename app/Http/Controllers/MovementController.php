<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Store;
use App\Models\Product;
use App\Models\Movement;
use App\Models\Warehouse;
use App\Models\MovementType;
use App\Models\MovementDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\StoreMovementRequest;

class MovementController extends Controller
{
    public function index(StoreRequest $request)
    {
        $date_from = Carbon::parse($request->date_from)->startOfDay();
        $date_to = Carbon::parse($request->date_to)->endOfDay();
        if ($date_from->greaterThanOrEqualTo($date_to)) {
            return response()->json([
                'message' => 'La fecha inicial debe ser anterior a la fecha final'
            ], 422);
        }

        $active = (int)$request->active ?? 1;
        $query = DB::table('movements')->select('movements.*', 'movement_types.code as movement_type_code', 'movement_types.name as movement_type_name', 'person_user.name as user_name', 'person_from_store.name as from_store_name', 'person_to_store.name as to_store_name', 'movements.total_price')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->leftJoin('users', 'users.id', '=', 'movements.user_id')->leftJoin('stores as from_store', 'from_store.id', '=', 'movements.from_store_id')->leftJoin('stores as to_store', 'to_store.id', '=', 'movements.to_store_id')->leftJoin('people as person_user', 'person_user.id', '=', 'users.person_id')->leftJoin('people as person_from_store', 'person_from_store.id', '=', 'from_store.person_id')->leftJoin('people as person_to_store', 'person_to_store.id', '=', 'to_store.person_id')->where('movement_types.active', $active)->where(function($q) use ($request) {
            return $q->orWhere('from_store_id', (int)$request->store_id)->orWhere('to_store_id', (int)$request->store_id);
        })->whereDate('movements.created_at', '>=', $date_from->toDateTimeString())->whereDate('movements.created_at', '<=', $date_to->toDateTimeString());
        if (!$active) {
            $query->addSelect('person_client.name as client_name', 'person_client.document as client_document', 'document_types.code as document_type_code')->leftJoin('clients', 'clients.id', '=', 'movements.client_id')->leftJoin('people as person_client', 'person_client.id', '=', 'clients.person_id')->leftJoin('document_types', 'document_types.id', '=', 'person_client.document_type_id');
        }
        if ($request->has('sort_by') && $request->has('sort_desc') && !$request->has('print')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('movements.created_at', 'DESC');
        }

        $user = auth()->user();
        if (!in_array($user->roles()->where('store_id', (int)$request->store_id)->first()->name, ['ADMINISTRADOR', 'GERENTE'])) {
            $query->where('movements.user_id', $user->id);
        }

        if ($request->has('print')) {
            if ($request->print) {
                try {
                    $data = [
                        'filename' => 'Ventas_' . Carbon::now()->format('d-m-Y_H-i') . '.pdf',
                        'store' => Store::find($request->store_id)->person->name,
                        'date_from' => $date_from->startOfMonth()->format('d/m/Y'),
                        'date_to' => $date_to->format('d/m/Y'),
                        'sells' => $query->get(),
                    ];
                    $data['total'] = $data['sells']->sum('total_price');
                    $pdf = PDF::loadView('pdf.sells', $data)->output();
                    return [
                        'message' => 'PDF generado',
                        'payload' => [
                            'file' => [
                                'content' => base64_encode($pdf),
                                'name' => $data['filename'],
                            ],
                        ],
                    ];
                } catch(\Throwable $e) {
                    logger($e);
                    return response()->json([
                        'message' => 'Error al generar el PDF',
                    ], 500);
                }
            }
        }

        if ($request->has('search')) {
            if ($request->search != '') {
                $query->where(function($q) use ($request, $active) {
                    $q->orWhere(DB::raw('upper(movements.comment)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_user.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                    if ($active) {
                        $q->orWhere(DB::raw('upper(movement_types.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_from_store.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_to_store.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                    } else {
                        $q->orWhere(DB::raw('upper(person_client.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(person_client.document)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                    }
                    return $q;
                });
            }
        }
        $date = null;
        try {
            $date = Carbon::createFromFormat('d/m/Y', $request->search);
            if ($date != null) {
                $query->orWhereDate('movements.created_at', '=', $date);
            }
        } catch(\Exception $e) {}

        return [
            'message' => 'Lista de movimientos',
            'payload' => $query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1),
        ];
    }

    public function store(StoreMovementRequest $request)
    {
        if ($request->from_store_id == null && $request->to_store_id == null) {
            return response()->json([
                'message' => 'Debe seleccionar una tienda/almacén',
                'errors' => [
                    'from_store_id' => ['Debe definir el origen'],
                    'to_store_id' => ['Debe definir el destino'],
                ]
            ], 422);
        }
        $movement_type = MovementType::find($request->movement_type_id);
        switch ($movement_type->code) {
            case 'ENTRY':
            case 'CANCEL_SELL':
                if ($request->from_store_id != null || $request->to_store_id == null) {
                    return response()->json([
                        'message' => 'Debe seleccionar una tienda/almacén',
                        'errors' => [
                            'from_store_id' => ['Campo prohibido'],
                            'to_store_id' => ['Debe definir el destino'],
                        ]
                    ], 422);
                }
                break;
            case 'ADJUSTMENT':
            case 'SELL':
                if ($request->from_store_id == null || $request->to_store_id != null) {
                    return response()->json([
                        'message' => 'Debe seleccionar una tienda/almacén',
                        'errors' => [
                            'from_store_id' => ['Debe definir el origen'],
                            'to_store_id' => ['Campo prohibido'],
                        ]
                    ], 422);
                }
                break;
            case 'TRANSFER':
                if ($request->from_store_id == null || $request->to_store_id == null) {
                    return response()->json([
                        'message' => 'Debe seleccionar una tienda/almacén',
                        'errors' => [
                            'from_store_id' => ['Debe definir el origen'],
                            'to_store_id' => ['Debe definir el destino'],
                        ]
                    ], 422);
                }
                break;
        }
        $movement = new Movement();
        $movement->comment = $request->comment;
        $movement->total_price = 0;
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
                $product = Product::with('name:id,sell_price')->find((int)$detail['id']);
                $movement->total_price += $product->name->sell_price * (int)$detail['stock'];
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
                        $product->stock += $movement_detail->stock;
                        $product->save();
                        break;
                    case 'TRANSFER':
                        $movement_detail->stock = -1 * (int)$detail['stock'];
                        $movement_detail->store_id = $movement->from_store_id;
                        $movement_detail->movement()->associate($movement);
                        $movement_detail->save();
                        $movement_detail = new MovementDetail();
                        $movement_detail->product()->associate($product);
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
            $movement->save();
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
        $data = DB::table('movements')->select('movements.id', 'movements.total_price', 'movements.created_at', 'movements.deleted_at', 'movements.comment', 'movement_types.code as movement_type_code', 'movement_types.name as movement_type_name', 'pu.name as user_name', 'movements.from_store_id', 'pfs.name as from_store_name', 'movements.to_store_id', 'pts.name as to_store_name', 'movements.client_id', 'pc.name as client_name', 'pc.document as client_document', 'document_types.code as client_document_type')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->leftJoin('users', 'users.id', '=', 'movements.user_id')->leftJoin('people as pu', 'pu.id', '=', 'users.person_id')->leftJoin('stores as fs', 'fs.id', '=', 'movements.from_store_id')->leftJoin('people as pfs', 'pfs.id', '=', 'fs.person_id')->leftJoin('stores as ts', 'ts.id', '=', 'movements.to_store_id')->leftJoin('people as pts', 'pts.id', '=', 'ts.person_id')->leftJoin('clients', 'clients.id', '=', 'movements.client_id')->leftJoin('people as pc', 'pc.id', '=', 'clients.person_id')->leftJoin('document_types', 'document_types.id', '=', 'pc.document_type_id')->where('movements.id', $movement->id)->first();

        $products = DB::table('movement_details')->select('products.product_name_id', 'product_names.name as product_name', 'products.brand_id', 'brands.name as brand_name', 'products.gender_id', 'genders.name as gender_name', 'products.color_id', 'colors.name as color_name', 'product_names.category_id', 'categories.name as category_name', 'sizes.size_type_id', 'size_types.name as size_type_name', 'product_names.sell_price')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('genders', 'genders.id', '=', 'products.gender_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('movement_details.movement_id', $movement->id)->groupBy('products.product_name_id', 'products.brand_id', 'products.gender_id', 'products.color_id', 'product_names.category_id', 'sizes.size_type_id')->get();

        foreach ($products as $i => $product) {
            $query = DB::table('movement_details')->select('movement_details.id', 'sizes.name as size_name')->selectRaw('ABS(movement_details.stock) as stock')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('movement_details.movement_id', $movement->id)->where('products.product_name_id', $product->product_name_id)->where('products.brand_id', $product->brand_id)->where('products.gender_id', $product->gender_id)->where('products.color_id', $product->color_id)->where('product_names.category_id', $product->category_id)->where('sizes.size_type_id', $product->size_type_id);
            if ($data->movement_type_code === 'TRANSFER') {
                $query->where('movement_details.stock', '>', 0);
            }
            $products[$i]->products = $query->get();
        }

        return [
            'message' => 'Movimiento de stock',
            'payload' => [
                'movement' => $data,
                'products' => $products,
            ],
        ];
    }

    public function destroy(Movement $movement)
    {
        //
    }
}
