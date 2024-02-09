<?php

namespace App\Http\Requests\Task;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_search');
    }

    public function rules()
    {
        return [

            "title" => "nullable|string|max:255",
            "description" => "string|nullable",
            "created_by_id" => "nullable|integer|exists:users,id",
            "assigned_to_id" => "nullable|integer|exists:users,id",
            "tags" => "array|nullable",
            "tags.*" => "required|string",
            "per_page" => "nullable|numeric|max:100"

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
