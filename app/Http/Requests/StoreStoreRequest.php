<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'warehouse' => 'required|boolean',
            'document' => 'nullable|string|min:3|max:255',
            'address' => 'nullable|string|min:3|max:255',
            'email' => 'nullable|email:rfc',
            'phone' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
            'whatsapp' => 'nullable|url:http,https',
            'facebook' => 'nullable|url:http,https',
            'youtube' => 'nullable|url:http,https',
            'instagram' => 'nullable|url:http,https',
            'tiktok' => 'nullable|url:http,https',
            'pinterest' => 'nullable|url:http,https',
        ];
    }
}
