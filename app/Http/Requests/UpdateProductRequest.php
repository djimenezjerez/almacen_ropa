<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'active' => 'required|boolean',
            'sell_price' => 'required|numeric|min:1',
        ];
    }
}
