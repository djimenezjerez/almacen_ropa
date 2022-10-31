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
            'gender_id' => 'required|exists:genders,id',
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'required|exists:sizes,id',
            'brands' => 'required|array|min:1',
            'brands.*' => 'required|exists:brands,id',
            'colors' => 'required|array|min:1',
            'colors.*' => 'required|exists:colors,id',
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
