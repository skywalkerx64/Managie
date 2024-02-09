<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('password_can_change');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "old_password" => "required|string",
            "password" => "required|string|min:8|confirmed",
        ];
    }

    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
