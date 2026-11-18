<?php

namespace App\Http\Requests\Admin;

use App\Enums\AiMode;
use App\Enums\CodingLevel;
use App\Enums\Level;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name', '')),
            'daily_tasks' => $this->cleanList('daily_tasks'),
            'skills' => $this->cleanList('skills'),
            'job_titles' => $this->cleanList('job_titles'),
        ]);
    }

    public function rules(): array
    {
        return [
            'area_id' => ['required', 'integer', 'exists:areas,id'],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', Rule::unique('careers')->ignore($this->route('career'))],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'coding_level' => ['required', Rule::enum(CodingLevel::class)],
            'daily_tasks' => ['array', 'max:20'],
            'daily_tasks.*' => ['string', 'max:255'],
            'skills' => ['array', 'max:30'],
            'skills.*' => ['string', 'max:100'],
            'job_titles' => ['array', 'max:20'],
            'job_titles.*' => ['string', 'max:100'],
            'job_outlook' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['boolean'],
            'steps' => ['array', 'max:50'],
            'steps.*.id' => ['nullable', 'integer'],
            'steps.*.level' => ['required', Rule::enum(Level::class)],
            'steps.*.title' => ['required', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string', 'max:2000'],
            'challenges' => ['array', 'max:50'],
            'challenges.*.id' => ['nullable', 'integer'],
            'challenges.*.level' => ['required', Rule::enum(Level::class)],
            'challenges.*.title' => ['required', 'string', 'max:255'],
            'challenges.*.description' => ['required', 'string', 'max:3000'],
            'challenges.*.deliverable' => ['required', 'string', 'max:500'],
            'challenges.*.ai_mode' => ['required', Rule::enum(AiMode::class)],
            'challenges.*.solution' => ['nullable', 'string', 'max:3000'],
            'challenges.*.guide' => ['nullable', 'array'],
            'challenges.*.guide.before' => ['array', 'max:10'],
            'challenges.*.guide.before.*' => ['nullable', 'string', 'max:500'],
            'challenges.*.guide.steps' => ['array', 'max:15'],
            'challenges.*.guide.steps.*' => ['nullable', 'string', 'max:500'],
            'challenges.*.guide.hints' => ['array', 'max:5'],
            'challenges.*.guide.hints.*' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function cleanList(string $key): array
    {
        $items = $this->input($key, []);

        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($item) => is_string($item) ? trim($item) : '',
            $items,
        )));
    }
}
