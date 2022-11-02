<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:entries,adjustments,outcomes,incomes',
            'store_id' => 'required|integer|exists:stores,id',
        ];
    }
}
