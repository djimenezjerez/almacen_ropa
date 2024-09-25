<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingCartProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'quantity' => $this->quantity,
            'sell_price' => $this->sell_price,
            'subtotal' => $this->subtotal,
            'product' => new ProductResource($this->product),
        ];
    }
}
