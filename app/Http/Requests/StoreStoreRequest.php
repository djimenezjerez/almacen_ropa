<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|alpha_spaces|min:3',
            'document' => 'required|min:3',
            'address' => 'nullable|min:3',
            'email' => 'nullable|email:rfc',
            'phone' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
        ];
    }
}
