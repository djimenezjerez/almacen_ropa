<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:products,id',
            'file' => 'required_without:url|image|max:4096',
            'url' => 'required_without:file|url:http,https|max:255',
        ];
    }
}
