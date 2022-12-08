<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSizeOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sizes' => 'required|array|min:1',
            'sizes.*.id' => 'required|integer|min:1|exists:sizes,id',
            'sizes.*.order' => 'required|integer|min:1',
        ];
    }
}
