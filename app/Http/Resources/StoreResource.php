<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            // Store
            'id' => $this->id,
            'active' => $this->active,
            'warehouse' => $this->warehouse,
            'person_id' => $this->person_id,
            'created_at' => $this->created_at,
            // Person
            'name' => $this->person->name,
            'document' => $this->person->document,
            'address' => $this->person->address,
            'email' => $this->person->email,
            'phone' => $this->person->phone,
            'city_id' => $this->person->city_id,
            'city_code' => $this->person->city->code,
            'city_name' => $this->person->city->name,
            'document_type_id' => $this->person->document_type_id,
            'document_type_name' => $this->warehouse ? null : $this->person->document_type->name,
            'document_type_code' => $this->warehouse ? null : $this->person->document_type->code,
        ];
    }
}
