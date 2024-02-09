<?php

namespace App\Http\Requests\Auth;

use App\Rules\CanLogin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            // "email" => ['required','email','exists:users,email'],
            "email" => ['required','email', new CanLogin],
            "password" => "required|string",
            // 'token' => 'required|recaptchav3:login,0.5'
        ];
    }

    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
