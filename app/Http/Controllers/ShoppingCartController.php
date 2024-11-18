<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Client;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ShoppingCartResource;
use App\Http\Requests\PayShoppingCartRequest;
use App\Http\Requests\StoreShoppingCartRequest;

class ShoppingCartController extends Controller
{
    public function index(Request $request, Client $client)
    {
        $query = $client->shopping_carts();
        if ($request->has('sort_by') && $request->has('sort_desc')) {
            foreach ($request->sort_by as $i => $sort) {
                $query->orderBy($sort, filter_var($request->sort_desc[$i], FILTER_VALIDATE_BOOLEAN) ? 'DESC' : 'ASC');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        return [
            'message' => 'Lista de pedidor',
            'payload' => ShoppingCartResource::collection($query->paginate($request->per_page ?? 8, ['*'], 'page', $request->page ?? 1)),
        ];
    }

    public function store(StoreShoppingCartRequest $request, Client $client)
    {
        try {
            DB::beginTransaction();
            $shopping_cart = $client->shopping_carts()->create([
                'total' => 0,
                'state' => 'open',
            ]);
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                $shopping_cart->products()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'sell_price' => $product->name->sell_price,
                    'subtotal' => $item['quantity'] * $product->name->sell_price,
                ]);
            }
            $shopping_cart->total = $shopping_cart->products->sum('subtotal');
            $shopping_cart->save();
            DB::commit();
            return response()->json([
                'message' => 'Pedido guardado',
                'payload' => [
                    'shopping_cart' => new ShoppingCartResource($shopping_cart),
                ],
            ]);
        } catch (Exception $e) {
            logger($e);
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar pedido',
            ], 500);
        }
    }

    public function show(ShoppingCart $shopping_cart)
    {
        //
    }

    public function destroy(Client $client, ShoppingCart $shopping_cart)
    {
        $shopping_cart->update(['state' => 'closed']);
        return [
            'message' => 'Registro eliminado',
        ];
    }

    public function current(Client $client)
    {
        $shopping_cart = $client->shopping_carts()->where('state', 'open')->orderBy('id', 'desc')->first();
        return response()->json([
            'message' => 'Pedido activo',
            'payload' => [
                'shopping_cart' => $shopping_cart ? new ShoppingCartResource($shopping_cart) : (object)[],
            ],
        ]);
    }

    public function pay(PayShoppingCartRequest $request, Client $client, ShoppingCart $shopping_cart)
    {
        @list($type, $file_data) = explode(';', $request->content);
        @list(, $file_data) = explode(',', $file_data);
        switch ($request->type) {
            case 'image/bmp':
                $type = 'bmp';
                break;
            case 'application/msword':
                $type = 'doc';
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $type = 'docx';
                break;
            case 'text/html':
                $type = 'html';
                break;
            case 'image/jpeg':
                $type = 'jpg';
                break;
            case 'application/vnd.oasis.opendocument.text':
                $type = 'odt';
                break;
            case 'image/png':
                $type = 'png';
                break;
            case 'application/pdf':
                $type = 'pdf';
                break;
            case 'application/vnd.rar':
                $type = 'rar';
                break;
            case 'image/svg+xml':
                $type = 'svg';
                break;
            case 'application/x-tar':
                $type = 'tar';
                break;
            case 'image/tiff':
                $type = 'tiff';
                break;
            case 'application/xhtml+xml':
                $type = 'xhtml';
                break;
            case 'application/zip':
                $type = 'zip';
                break;
            case 'application/x-7z-compressed':
                $type = '7z';
                break;
        }
        $file_name = 'shopping_carts/' . 'pedido_' . str($shopping_cart->id) . '_cliente_' . str($client->id) . '.' . $type;
        Storage::disk('local')->put($file_name, base64_decode($file_data));
        $shopping_cart->update([
            'state' => 'paid',
            'attachment_file' => 'storage/' . $file_name,
            'attachment_type' => $type,
        ]);
        return response()->json([
            'message' => 'Pedido en proceso',
        ]);
    }
}
