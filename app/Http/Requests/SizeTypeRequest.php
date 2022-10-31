<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SizeTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'size_type_id' => 'required|exists:size_types,id',
        ];
    }
}
