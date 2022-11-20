<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'comment' => 'nullable|sometimes|string|min:1',
            'movement_type_id' => 'required|exists:movement_types,id',
            'client_id' => 'nullable|sometimes|exists:clients,id',
            'from_store_id' => 'nullable|sometimes|exists:stores,id',
            'to_store_id' => 'nullable|sometimes|exists:stores,id',
            'details' => 'required|array|min:1',
            'details.*.id' => 'required|exists:products,id',
            'details.*.stock' => 'required|integer|min:1',
        ];
    }
}
