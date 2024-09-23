<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            // Client
            'id' => $this->id,
            'active' => $this->active,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            // Person
            'name' => $this->user->person->name,
            'document' => $this->user->person->document,
            'address' => $this->user->person->address,
            'email' => $this->user->person->email,
            'phone' => $this->user->person->phone,
            'city_id' => $this->user->person->city_id,
            'city_code' => $this->user->person->city ? $this->user->person->city->code : null,
            'city_name' => $this->user->person->city ? $this->user->person->city->name : null,
            'document_type_id' => $this->user->person->document_type_id,
            'document_type_name' => $this->user->person->document_type->name,
            'document_type_code' => $this->user->person->document_type->code,
        ];
    }
}
