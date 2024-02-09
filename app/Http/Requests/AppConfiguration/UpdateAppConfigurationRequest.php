<?php

namespace App\Http\Requests\AppConfiguration;

use App\Models\AppConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppConfigurationRequest extends FormRequest
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
            'code' => 'string|max:255',
            'name' => 'string|max:255',
            'value' => '',
            'type' => 'in:'.implode(',', AppConfiguration::TYPES),
            'visible' => 'nullable|boolean',
            'description' => 'nullable|string',
        ];
    }
}
