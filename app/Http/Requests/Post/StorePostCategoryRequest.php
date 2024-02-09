<?php

namespace App\Http\Requests\Post;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('post_category_create');
    }

    public function rules()
    {
        return [

            "title" => "required|string|unique:post_categories,title|max:255",
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