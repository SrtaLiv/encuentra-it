<?php

namespace App\Http\Requests\Admin;

use App\Enums\Level;
use App\Enums\ResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url:http,https', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', Rule::enum(ResourceType::class)],
            'level' => ['required', Rule::enum(Level::class)],
            'language' => ['required', Rule::in(['es', 'en'])],
            'is_free' => ['boolean'],
            'is_published' => ['boolean'],
            'career_ids' => ['array'],
            'career_ids.*' => ['integer', 'exists:careers,id'],
        ];
    }
}
