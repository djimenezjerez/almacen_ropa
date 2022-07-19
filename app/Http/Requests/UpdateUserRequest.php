<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user->id == auth()->user()->id || auth()->user()->can('USUARIOS');
    }

    public function rules()
    {
        $rules = [
            'name' => 'sometimes|required|alpha_spaces|min:3',
            'active' => 'sometimes|required|boolean',
            'document' => 'sometimes|required|min:3',
            'address' => 'sometimes|nullable|min:3',
            'email' => 'sometimes|nullable|email:rfc',
            'phone' => 'sometimes|nullable|numeric',
            'city_id' => 'sometimes|nullable|exists:cities,id',
            'username' => 'sometimes|required|alpha_dash|min:3|unique:users,username,'.$this->id,
            'password' => 'sometimes|string|min:3|required_with:old_password',
        ];
        if ($this->id == auth()->user()->id) {
            $rules['name'] = 'prohibited';
            $rules['active'] = 'prohibited';
            $rules['document'] = 'prohibited';
            $rules['username'] = 'prohibited';
            $rules['old_password'] = 'sometimes|string|min:3';
        }
        return $rules;
    }
}
