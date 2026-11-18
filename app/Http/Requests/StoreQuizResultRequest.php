<?php

namespace App\Http\Requests;

use App\Models\QuizQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQuizResultRequest extends FormRequest
{
    /**
     * @var array<int, int>
     */
    private array $optionIds = [];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $answers = $this->input('answers');
                $questions = QuizQuestion::query()->with('options:id,quiz_question_id')->get();

                foreach ($questions as $question) {
                    $optionId = (int) ($answers[$question->id] ?? 0);

                    if (! $question->options->contains('id', $optionId)) {
                        $validator->errors()->add('answers', 'Respondé todas las preguntas antes de ver tu resultado.');

                        return;
                    }

                    $this->optionIds[] = $optionId;
                }
            },
        ];
    }

    /**
     * @return array<int, int>
     */
    public function optionIds(): array
    {
        return $this->optionIds;
    }
}
