<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            "title" => "nullable|string|max:255",
            "description" => "string|nullable",
            "post_category_id" => "nullable|integer|exists:post_categories,id",
            "secteur_id" => "nullable|integer|exists:secteurs,id",
            "type" => "nullable|string|in:".implode(',', Post::TYPES),
            "tags" => "array|nullable",
            "tags.*" => "string|max:255",
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
