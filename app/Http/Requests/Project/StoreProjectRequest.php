<?php

namespace App\Http\Requests\Project;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProjectRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('project_create');
    }

    public function rules()
    {
        return [
            "title" => "required|string|max:255",
            "description" => "string|nullable",
            "start_date" => "date|nullable|before:end_date",
            "end_date" => "date|nullable|after:start_date",
            "tags" => "array|nullable",
            "tags.*" => "required|string",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
