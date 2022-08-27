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
            'origin_store_id' => 'nullable|exists:stores,id',
            'destiny_store_id' => 'nullable|exists:stores,id',
            'origin_warehouse_id' => 'nullable|exists:warehouses,id',
            'destiny_warehouse_id' => 'nullable|exists:warehouses,id',
            'products' => 'required|array:product_id,stock',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.stock' => 'required|integer|min:1|max:18446744073709551615',
        ];
    }
}
