<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'stock' => $this->stock,
            'image' => $this->image,
            'name' => new ProductNameResource($this->name),
            'category' => new CategoryResource($this->name->category),
            'brand' => $this->brand,
            'gender' => $this->gender,
            'size' => $this->size,
            'color' => $this->color,
        ];
    }
}
