<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RoleUpdateRequest extends FormRequest
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
            'params.name' => 'required|min:3|unique:roles,name,' . $this->role->id
        ];
    }

    public function messages()
    {
        return [
            'params.name.required' => '角色名稱必填',
            'params.name.min' => '角色名稱至少需三碼',
            'params.name.unique' => '角色名稱已存在'
        ];
    }
}
