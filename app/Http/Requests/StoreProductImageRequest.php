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
        $rules = [
            'url' => 'required_without:file|url:http,https|max:255',
            'video' => 'required|nullable',
            'order' => 'nullable|integer|min:0',
        ];
        if ($this->boolean('video')) {
            $rules['file'] = 'required_without:url|mimetypes:video/mp4,video/x-flv,video/x-ms-asf,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi,video/webm,video/ogg,video/mpeg|max:20480';
        } else {
            $rules['file'] = 'required_without:url|image|max:4096';
        }
        return $rules;
    }
}
