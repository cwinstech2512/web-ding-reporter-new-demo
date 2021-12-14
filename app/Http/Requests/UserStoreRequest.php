<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'params.name' => 'required|min:3|unique:users,name',
            'params.password' => 'required|min:4|max:20',
            'params.role' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'params.name.required' => '帳號名稱必填',
            'params.name.min' => '帳號名稱至少需三碼',
            'params.name.unique' => '帳號名稱已存在',
            'params.password.required' => '密碼必填',
            'params.password.min' => '密碼長度至少4碼',
            'params.password.max' => '密碼長度至多20碼',
            'params.role.required' => '權限必填'
        ];
    }
}
