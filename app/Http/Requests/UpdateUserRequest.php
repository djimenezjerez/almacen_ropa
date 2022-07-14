<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        return $this->user->id == $user->id || $user->can('USUARIOS');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'alpha_spaces|min:3',
            'last_name' => 'alpha_spaces|min:3',
            'role_id' => 'exists:roles,id',
            'email' => 'email:rfc',
            'phone' => 'numeric',
            'city_id' => 'exists:cities,id',
            'identity_card' => 'unique:users,identity_card,'.$this->id,
        ];
        if ($this->id == auth()->user()->id) {
            $rules = [
                'old_password' => 'string|min:4',
                'password' => 'string|min:4',
            ] + $rules;
            foreach (array_slice($rules, 2) as $key => $rule) {
                $rules[$key] = 'prohibited';
            }
            foreach (array_slice($rules, 0, 2) as $key => $rule) {
                $rules[$key] = implode('|', ['required', $rule]);
            }
        } else {
            $rules['identity_card'] .= '|integer|min:3';
            foreach ($rules as $key => $rule) {
                $rules[$key] = implode('|', ['sometimes|required', $rule]);
            }
        }
        return $rules;
    }
}
