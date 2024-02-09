<?php

namespace App\Http\Requests\User;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('user_search');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'periode' => "nullable|array",
            'periode.from' => "nullable|date",
            'periode.to' => "nullable|date",
            'email' => "nullable|string|max:255",
            'firstname' => "nullable|string|max:255",
            'lastname' => "nullable|string|max:255",
            'etablissement' => "nullable|string|max:255",
            'role_id' => "nullable|exists:roles,id",
            
            'role_ids' => "array|nullable",
            'role_ids.*' => "required|exists:roles,id",

            'role' => "nullable|exists:roles,alias",
            
            'roles' => "array|nullable",
            'roles.*' => "required|exists:roles,alias",

            'per_page' => "nullable|numeric|max:100",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
