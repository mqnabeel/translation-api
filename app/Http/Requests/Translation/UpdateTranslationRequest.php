<?php

namespace App\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id'
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'The translation content is required',
            'tags.array' => 'Tags must be an array',
            'tags.*.exists' => 'One or more selected tags do not exist'
        ];
    }
} 