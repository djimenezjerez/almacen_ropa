<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|alpha_spaces|min:3',
            'address' => 'nullable|min:3',
            'active' => 'required|boolean',
            'city_id' => 'required|exists:cities,id',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
