<?php

namespace App\Http\Requests\Mail;

use App\Models\Mail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('mail_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "fullname" => "nullable|string",
            "subject" => "nullable|string",
            "object" => "nullable|string",
            "content" => "nullable|string",
            "email" => "nullable|email",
            "cc" => "nullable|array",
            "bcc" => "nullable|array",
            "type" => "in:".implode(",", Mail::TYPES),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
