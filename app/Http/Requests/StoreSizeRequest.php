<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSizeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:1',
            'size_type_id' => 'required|exists:size_types,id',
            'numeric' => 'required|boolean',
        ];
    }
}
