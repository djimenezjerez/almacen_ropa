<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'address' => 'nullable|min:3',
            'email' => 'nullable|email:rfc',
            'phone' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
            'username' => 'required|alpha_dash|min:3|unique:users,username',
            'password' => 'nullable',
        ];
    }
}
