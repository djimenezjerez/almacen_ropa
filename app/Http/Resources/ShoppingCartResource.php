<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingCartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'client_id' => $this->client_id,
            'total' => $this->total,
            'state' => $this->state,
            'attachmentFile' => $this->attachment_file,
            'attachmentType' => $this->attachment_type,
            'comment' => $this->comment,
            'products' => ShoppingCartProductResource::collection($this->products),
        ];
    }
}
