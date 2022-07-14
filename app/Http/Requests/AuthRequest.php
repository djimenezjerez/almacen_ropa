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
            'identity_card' => 'required|integer|min:3',
            'password' => 'required|min:3',
        ];
    }
}
