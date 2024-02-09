<?php

namespace App\Http\Requests\Task;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_search');
    }

    public function rules()
    {
        return [

            "title" => "required|string|max:255",
            "description" => "string|nullable",
            "assigned_to_id" => "nullable|integer|exists:users,id",
            "start_date" => "date|nullable|before:end_date",
            "end_date" => "date|nullable|after:start_date",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
