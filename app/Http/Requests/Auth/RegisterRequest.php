<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',

            "email" => "required|email|unique:users,email|max:255",
            "password" => "required|string|min:8|confirmed",

            "login_after_register" => "nullable|boolean",

            // "attached_files" => "array|nullable",
            // "attached_files.*" => "file|max:5120",

            "avatar" => "file|max:5120",
        ];
    }

    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
