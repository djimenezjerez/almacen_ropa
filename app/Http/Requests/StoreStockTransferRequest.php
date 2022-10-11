<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:transfer,entry,adjustment',
            'destiny_id' => 'nullable|integer|min:1',
            'destiny_type' => 'nullable|in:store,warehouse',
            'origin_id' => 'nullable|integer|min:1',
            'origin_type' => 'nullable|in:store,warehouse',
            'products' => 'required|array|min:1',
            'products.*.products' => 'required|array|min:1',
            'products.*.products.*.id' => 'required|integer|exists:products,id',
            'products.*.products.*.quantity' => 'required|integer|max:18446744073709551615|min:1',
        ];
    }
}
