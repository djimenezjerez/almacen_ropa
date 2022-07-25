<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|alpha_spaces|min:3',
            'document' => 'required|min:3',
            'document_type_id' => 'required|exists:document_types,id',
            'address' => 'nullable|min:3',
            'email' => 'nullable|email:rfc',
            'phone' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
        ];
    }
}
