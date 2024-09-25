<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Client;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PayShoppingCartRequest;
use App\Http\Requests\StoreShoppingCartRequest;
use App\Http\Resources\ShoppingCartResource;

class ShoppingCartController extends Controller
{
    public function index()
    {
        //
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
        $shopping_cart->delete();
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
                'shopping_cart' => $shopping_cart ? new ShoppingCartResource($shopping_cart) : null,
            ],
        ]);
    }

    public function pay(PayShoppingCartRequest $request, Client $client, ShoppingCart $shopping_cart)
    {
        $file = 'pedido_' . str($shopping_cart->id) . '_cliente_' . str($client->id) . '.' . $request->file->getClientOriginalExtension();
        $file = $request->file->storeAs('shopping_carts', $file, 'public');
        $shopping_cart->update([
            'state' => 'paid',
            'voucher' => 'storage/' . $file,
        ]);
        return [
            'message' => 'Pedido en proceso',
        ];
    }
}
