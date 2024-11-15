<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PayShoppingCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in('image/bmp', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/html', 'image/jpeg', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'image/png', 'application/pdf', 'application/vnd.rar', 'image/svg+xml', 'application/x-tar', 'image/tiff', 'application/xhtml+xml', 'application/zip', 'application/x-7z-compressed')],
        ];
    }
}
