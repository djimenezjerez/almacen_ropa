<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'identity_card' => $this->identity_card,
            'email' => $this->email,
            'phone' => $this->phone,
            'city_id' => $this->city_id,
            'city_code' => $this->city_code,
            'city_name' => $this->city_name,
            'role_id' => $this->role_id,
            'role_name' => $this->role_name,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
