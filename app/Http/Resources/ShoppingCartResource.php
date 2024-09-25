<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingCartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'total' => $this->total,
            'state' => $this->state,
            'voucher' => $this->voucher,
            'comment' => $this->comment,
            'products' => ShoppingCartProductResource::collection($this->products),
        ];
    }
}
