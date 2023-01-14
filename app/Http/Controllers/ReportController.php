<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Store;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SizeTypeRequest;

class ReportController extends Controller
{
    public function products(SizeTypeRequest $request)
    {
        $store = false;
        if ($request->has('store_id')) {
            if ((int)$request->store_id > 0) {
                $store = DB::table('stores')->where('id', (int)$request->store_id)->exists();
                if ($store) {
                    $movements = DB::table('movement_details')->select('product_id')->selectRaw('cast(sum(stock) as INTEGER) as stock')->where('store_id', (int)$request->store_id)->groupBy('product_id');
                }
            }
        }
        $sizes = DB::table('sizes')->select('sizes.id', 'sizes.name', 'sizes.numeric')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('size_types.id', (int)$request->size_type_id)->orderBy('sizes.numeric')->orderBy('sizes.order')->orderBy('sizes.name')->get();

        $products = DB::table('products')->select('products.product_name_id', 'categories.name as category_name', 'product_names.name as product_name');
        if ($store) {
            $products->selectRaw('cast(sum(md.stock) as INTEGER) as total_stock')->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        } else {
            $products->selectRaw('cast(sum(products.stock) as INTEGER) as total_stock');
        }
        $products->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null)->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id');
        if ($request->has('search')) {
            if ($request->search != '') {
                $products->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(categories.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        $products = $products->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1)->toArray();

        $details = DB::table('products')->select('products.product_name_id', 'products.size_id');
        if ($store) {
            $details->selectRaw('cast(sum(md.stock) as INTEGER) as stock')->joinSub($movements, 'md', function($join) {
                $join->on('products.id', '=', 'md.product_id');
            });
        } else {
            $details->selectRaw('cast(sum(products.stock) as INTEGER) as stock');
        }
        $details->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null);
        if ($request->has('search')) {
            if ($request->search != '') {
                $details->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(categories.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        $details->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id')->groupBy('size_id');
        $details = $details->get();

        foreach ($products['data'] as $i => $product) {
            $stock = [];
            $product_details = $details->where('product_name_id', $product->product_name_id);
            foreach ($sizes as $size) {
                $detail = $product_details->filter(function($item) use ($size) {
                    return $item->size_id == $size->id;
                })->first();
                if ($detail) {
                    $stock[] = $detail->stock;
                } else {
                    $stock[] = 0;
                }
            }
            $products['data'][$i]->stock = $stock;
        }

        $remove = [];
        foreach ($sizes as $i => $size) {
            $sum = 0;
            foreach ($products['data'] as $product) {
                $sum += $product->stock[$i];
            }
            if ($sum == 0) {
                $remove[] = $i;
            }
        }
        $sizes = $sizes->toArray();
        $remove = array_reverse($remove);
        foreach ($remove as $i) {
            array_splice($sizes, $i, 1);
            foreach ($products['data'] as $j => $product) {
                array_splice($products['data'][$j]->stock, $i, 1);
            }
        }

        return [
            'message' => 'Lista de productos',
            'payload' => [
                'sizes' => $sizes,
                'products' => $products,
            ],
        ];
    }

    public function sells(SizeTypeRequest $request)
    {
        $date_from = Carbon::parse($request->date_from)->startOfDay();
        $date_to = Carbon::parse($request->date_to)->endOfDay();
        if ($date_from->greaterThanOrEqualTo($date_to)) {
            return response()->json([
                'message' => 'La fecha inicial debe ser anterior a la fecha final'
            ], 422);
        }

        $store = false;
        $movements = DB::table('movement_details')->select('movement_details.product_id')->selectRaw('cast(abs(coalesce(sum(movement_details.stock), 0)) as INTEGER) as stock')->leftJoin('movements', 'movements.id', '=', 'movement_details.movement_id')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->where('movement_types.active', false)->whereDate('movements.created_at', '>=', $date_from->toDateTimeString())->whereDate('movements.created_at', '<=', $date_to->toDateTimeString());
        if ($request->has('store_id')) {
            if ((int)$request->store_id > 0) {
                $store = DB::table('stores')->where('id', (int)$request->store_id)->exists();
                if ($store) {
                    $movements->where('movement_details.store_id', (int)$request->store_id);
                }
            }
        }
        $movements->groupBy('movement_details.product_id');
        $sizes = DB::table('sizes')->select('sizes.id', 'sizes.name', 'sizes.numeric')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('size_types.id', (int)$request->size_type_id)->orderBy('sizes.numeric')->orderBy('sizes.order')->orderBy('sizes.name')->get();

        $products = DB::table('products')->select('products.product_name_id', 'categories.name as category_name', 'product_names.name as product_name')->selectRaw('cast(sum(md.stock) as INTEGER) as total_stock')->joinSub($movements, 'md', function($join) {
            $join->on('products.id', '=', 'md.product_id');
        })->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null)->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id');
        if ($request->has('search')) {
            if ($request->search != '') {
                $products->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(categories.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        $products = $products->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1)->toArray();

        $details = DB::table('products')->select('products.product_name_id', 'products.size_id')->selectRaw('cast(sum(md.stock) as INTEGER) as stock')->joinSub($movements, 'md', function($join) {
            $join->on('products.id', '=', 'md.product_id');
        })->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('size_types', 'size_types.id', '=', 'sizes.size_type_id')->where('products.deleted_at', null);
        if ($request->has('search')) {
            if ($request->search != '') {
                $details->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('categories', 'categories.id', '=', 'product_names.category_id')->where(function($q) use ($request) {
                    return $q->orWhere(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%')->orWhere(DB::raw('upper(categories.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
                });
            }
        }
        $details->where('size_types.id', (int)$request->size_type_id)->groupBy('products.product_name_id')->groupBy('size_id');
        $details = $details->get();

        foreach ($products['data'] as $i => $product) {
            $stock = [];
            $product_details = $details->where('product_name_id', $product->product_name_id);
            foreach ($sizes as $size) {
                $detail = $product_details->filter(function($item) use ($size) {
                    return $item->size_id == $size->id;
                })->first();
                if ($detail) {
                    $stock[] = $detail->stock;
                } else {
                    $stock[] = 0;
                }
            }
            $products['data'][$i]->stock = $stock;
        }

        $remove = [];
        foreach ($sizes as $i => $size) {
            $sum = 0;
            foreach ($products['data'] as $product) {
                $sum += $product->stock[$i];
            }
            if ($sum == 0) {
                $remove[] = $i;
            }
        }
        $sizes = $sizes->toArray();
        $remove = array_reverse($remove);
        foreach ($remove as $i) {
            array_splice($sizes, $i, 1);
            foreach ($products['data'] as $j => $product) {
                array_splice($products['data'][$j]->stock, $i, 1);
            }
        }

        return [
            'message' => 'Lista de productos vendidos',
            'payload' => [
                'sizes' => $sizes,
                'products' => $products,
            ],
        ];
    }

    public function sellsUnitary(Request $request)
    {
        if (!$request->has('pdf')) {
            $request->merge(['pdf' => 0]);
        }

        $date_from = Carbon::parse($request->date_from)->startOfDay();
        $date_to = Carbon::parse($request->date_to)->endOfDay();
        if ($date_from->greaterThanOrEqualTo($date_to)) {
            return response()->json([
                'message' => 'La fecha inicial debe ser anterior a la fecha final'
            ], 422);
        }

        $num = DB::table('movement_details')->selectRaw('@num := 0')->leftJoin('movements', 'movement_details.movement_id', '=', 'movements.id')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->where('movement_types.code', 'SELL')->where('movements.deleted_at', null)->where('movements.from_store_id', (int)$request->store_id)->whereDate('movements.created_at', '>=', $date_from->toDateTimeString())->whereDate('movements.created_at', '<=', $date_to->toDateTimeString());

        $counter = DB::table('movement_details')->selectRaw('@num := @num + 1 as count')->joinSub($num, 'num', function ($join) {
            $join->on('movement_details.id', '=', 'movement_details.id');
        })->leftJoin('movements', 'movement_details.movement_id', '=', 'movements.id')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->where('movement_types.code', 'SELL')->where('movements.deleted_at', null)->where('movements.from_store_id', (int)$request->store_id)->whereDate('movements.created_at', '>=', $date_from->toDateTimeString())->whereDate('movements.created_at', '<=', $date_to->toDateTimeString())->orderBy('movement_details.created_at');

        if ((int)$request->pdf == 0) {
            $counter->limit($request->per_page ?? 100);
        }

        $query = DB::table('movements')->select('movement_details.product_id', 'movements.created_at', 'product_names.name as product_name', 'product_names.sell_price', 'colors.name as color_name', 'sizes.name as size_name')->leftJoin('movement_details', 'movement_details.movement_id', '=', 'movements.id')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('colors', 'colors.id', '=', 'products.color_id')->leftJoin('sizes', 'sizes.id', '=', 'products.size_id')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id')->joinSub($counter, 'counter', function ($join) {
            $join->on(DB::raw('abs(movement_details.stock)'), '>=', 'counter.count');
        });

        if ($request->has('search') && (int)$request->pdf == 0) {
            if ($request->search != '') {
                $query->where(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
            }
        }

        $query->where('movement_types.code', 'SELL')->where('movements.deleted_at', null)->where('movements.from_store_id', (int)$request->store_id)->whereDate('movements.created_at', '>=', $date_from->toDateTimeString())->whereDate('movements.created_at', '<=', $date_to->toDateTimeString())->orderBy('movements.created_at')->orderBy('product_names.name')->orderBy('colors.name')->orderBy('sizes.numeric')->orderBy('sizes.order');

        if ((int)$request->pdf == 1) {
            try {
                $data = [
                    'filename' => 'Ventas_detalladas_' . Carbon::now()->format('d-m-Y_H-i') . '.pdf',
                    'store' => Store::find($request->store_id)->person->name,
                    'date_from' => $date_from->startOfMonth()->format('d/m/Y'),
                    'date_to' => $date_to->format('d/m/Y'),
                    'products' => $query->get(),
                ];
                $data['total'] = $data['products']->sum('sell_price');
                $pdf = PDF::loadView('pdf.sells_unitary', $data)->output();
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

        $details = DB::table('movements')->selectRaw('product_names.sell_price * abs(movement_details.stock) as subtotal')->leftJoin('movement_details', 'movement_details.movement_id', '=', 'movements.id')->leftJoin('products', 'products.id', '=', 'movement_details.product_id')->leftJoin('product_names', 'product_names.id', '=', 'products.product_name_id')->leftJoin('movement_types', 'movement_types.id', '=', 'movements.movement_type_id');

        if ($request->has('search') && (int)$request->pdf == 0) {
            if ($request->search != '') {
                $details->where(DB::raw('upper(product_names.name)'), 'like', '%'.trim(mb_strtoupper($request->search)).'%');
            }
        }

        $details->where('movement_types.code', '=', 'SELL')->where('movements.deleted_at', null)->where('movements.from_store_id', (int)$request->store_id)->whereDate('movements.created_at', '>=', $date_from->toDateTimeString())->whereDate('movements.created_at', '<=', $date_to->toDateTimeString());

        $sum = DB::query()->fromSub($details, 'details')->selectRaw('coalesce(sum(details.subtotal), 0) as total');

        return [
            'message' => 'Lista de movimientos',
            'payload' => [
                'total' => $sum->first()->total,
                'products' => $query->paginate($request->per_page ?? 100, ['*'], 'page', $request->page ?? 1)
            ],
        ];
    }
}
