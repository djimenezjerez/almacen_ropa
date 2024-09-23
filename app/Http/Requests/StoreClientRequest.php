<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'document' => 'required|min:3',
            'document_type_id' => 'required|exists:document_types,id',
            'address' => 'nullable|min:3',
            'email' => 'required|email:rfc1|unique:users,username',
            'phone' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
            'password' => ['required', Password::min(8)],
        ];
    }
}
