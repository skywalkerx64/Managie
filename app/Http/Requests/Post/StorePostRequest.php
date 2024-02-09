<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('post_create');
    }

    public function rules()
    {
        return [

            "title" => "required|string|max:255",
            "description" => "string|required",
            "post_category_id" => "nullable|integer|exists:post_categories,id",
            "secteur_id" => "nullable|integer|exists:secteurs,id",
            "tags" => "array|nullable",
            "tags.*" => "string|max:255",
            "cover" => "nullable|file|max:5120",
            "link" => "string|nullable",
            "attached_files" => "array|nullable",
            "attached_files.*" => "file|max:5120",
            "status" => "string|nullable|in:" . implode(',', Post::STATUSES),
            "type" => "string|required|in:" . implode(',', Post::TYPES),
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
