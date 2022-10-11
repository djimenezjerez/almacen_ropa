<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'category_name' => 'required|string|min:1',
            'size_types' => 'required|array|min:1',
            'size_types.*.id' => 'required|exists:size_types,id',
            'size_types.*.name' => 'required|string|min:1',
            'size_types.*.genders' => 'required|array|min:1',
            'size_types.*.genders.*.id' => 'required|exists:genders,id',
            'size_types.*.genders.*.name' => 'required|string|min:1',
            'size_types.*.genders.*.attributes' => 'required|array|min:1',
            'size_types.*.genders.*.attributes.alphabetic_sizes' => 'present|array',
            'size_types.*.genders.*.attributes.numeric_sizes' => 'present|array',
            'size_types.*.genders.*.attributes.brands' => 'present|array',
            'size_types.*.genders.*.attributes.colors' => 'present|array',
        ];
    }

    protected function prepareForValidation()
    {
        return $this->merge([
            'name' => trim($this->name),
            'category_name' => trim($this->category_name),
        ]);
    }
}
