<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|alpha_spaces|min:3',
            'warehouse' => 'required|boolean',
            'document' => 'nullable|min:3',
            'address' => 'nullable|min:3',
            'email' => 'nullable|email:rfc',
            'phone' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
        ];
    }
}
