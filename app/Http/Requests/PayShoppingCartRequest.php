<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayShoppingCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => 'required|image|max:4096',
        ];
    }
}
