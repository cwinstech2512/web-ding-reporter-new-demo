<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'params.role' => 'required',
            'params.password' => 'sometimes|min:4'
        ];
    }

    public function messages()
    {
        return [
            'params.role.required' => '權限必填',
            'params.password.min' => '密碼長度至少4碼'
        ];
    }
}
