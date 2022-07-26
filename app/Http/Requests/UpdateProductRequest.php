<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'active' => 'required|boolean',
            'category_name' => 'required|string|min:1',
            'brand_name' => 'required|string|min:1',
            'size_name' => 'required|string|min:1',
            'size_type_id' => 'required|exists:size_types,id',
            'color_name' => 'required|string|min:1',
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge([
            'category_name' => trim($this->category_name),
            'brand_name' => trim($this->brand_name),
            'size_name' => trim($this->size_name),
            'color_name' => trim($this->color_name),
        ]);
    }
}
