<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => 'required_without:url|image|max:4096',
            'url' => 'required_without:file|url:http,https|max:255',
            'video' => 'required|nullable',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
