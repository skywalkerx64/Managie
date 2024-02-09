<?php

namespace App\Http\Requests\Permission;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ManageRolePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('permission_manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "role_id" => "required|integer|exists:roles,id",
            "permission_ids" => "required|nullable",
            "permission_ids.*" => "integer|exists:permissions,id",
            "is_active" => "required|boolean",
        ];
    }

    protected function failedValidation(Validator $validator)
    {   
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
