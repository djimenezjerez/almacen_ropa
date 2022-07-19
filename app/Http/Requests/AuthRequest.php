<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'username' => 'required|alpha_dash|min:3',
            'password' => 'required|min:3',
            'store_id' => 'required|exists:stores,id',
        ];
    }
}
