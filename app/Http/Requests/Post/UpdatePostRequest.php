<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePostRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('post_edit');
    }

    public function rules()
    {
        return [

            "title" => "nullable|string|max:255",
            "response" => "string|nullable",
            "faq_section_id" => "nullable|integer|exists:faq_sections,id",
            "secteur_id" => "nullable|integer|exists:secteurs,id",
            "link" => "string|nullable",
            "tags" => "array|nullable",
            "tags.*" => "string|max:255",
            "status" => "string|nullable|in:" . implode(',', Post::STATUSES),

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), 422)
        );
    }
}
