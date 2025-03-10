<?php

namespace App\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

class CreateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'content' => 'required|string',
            'locale' => 'required|string|size:2',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id'
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'The translation key is required',
            'content.required' => 'The translation content is required',
            'locale.required' => 'The locale is required',
            'locale.size' => 'The locale must be 2 characters (e.g., en, es)',
            'tags.*.exists' => 'One or more selected tags do not exist'
        ];
    }
} 