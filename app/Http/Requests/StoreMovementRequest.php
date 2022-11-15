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
            'movement_type' => 'required|in:entry,adjustment,transfer,sell',
            'client_id' => 'nullable|sometimes|exists:clients,id',
            'from_id' => 'nullable|sometimes|integer|min:1',
            'from_type' => 'nullable|sometimes|string|in:store,warehouse',
            'to_id' => 'nullable|sometimes|integer|min:1',
            'to_type' => 'nullable|sometimes|string|in:store,warehouse',
            'details' => 'required|array|min:1',
            'details.*.id' => 'required|exists:products,id',
            'details.*.stock' => 'required|integer|min:1',
        ];
    }
}
