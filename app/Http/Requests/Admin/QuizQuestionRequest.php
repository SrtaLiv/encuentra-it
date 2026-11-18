<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:0', 'max:999'],
            'options' => ['required', 'array', 'min:2', 'max:8'],
            'options.*.id' => ['nullable', 'integer'],
            'options.*.text' => ['required', 'string', 'max:255'],
            'options.*.career_ids' => ['array'],
            'options.*.career_ids.*' => ['integer', 'exists:careers,id'],
        ];
    }
}
