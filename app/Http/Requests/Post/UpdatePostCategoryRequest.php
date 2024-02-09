<?php

namespace App\Http\Requests\Post;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePostCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('post_category_edit');
    }

    public function rules()
    {
        return [

            "title" => "nullable|string|max:255",
            "description" => "string|nullable",

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
